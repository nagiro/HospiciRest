
Vue.component('form-inscripcio-simple', {
    props: {        
        InputColor: String, 
        InputDades: Object,
        ActivitatId: String,
        CicleId: String,
        CursId: String,
        DetallCurs: Object,
        DetallDescomptes: Array,
        DetallTeatre: Object,
        DetallSite: Object,
        SeientsOcupats: Array,
        UrlActual: String, 
        Token: Object // Site, token     
    },          
    data: function() {
        return {    ActivitatHome: {}, 
                    IdUsuariEncrypted: '',
                    QuantesEntrades: 1,
                    DescompteAplicat: -1,
                    Localitats: [],
                    TipusPagament: CONST_PAGAMENT_CAP,                         
                    PlacesLliures: 0,
                    LlistaEsperaActiu: false,
                    DadesExtres: '',
                    CodiOperacio: '',
                    Pas: 0, 
                    MatriculesArray: Array,
                    ErrorInscripcio: '',
                    ConfirmoAssistencia: false,
                    TPV: {},
                    isAdmin: (this.Token[0] == this.DetallCurs.CURSOS_SiteId),
                    Loading: false,
                    TEMP_CONST_LLISTA_ESPERA: CONST_PAGAMENT_LLISTA_ESPERA,
                    Alert: {'Missatge': '', 'Class': 'col-12 alert alert-success', 'Mostro': false }
                }
    },    
    computed: {
        genUrlInscripcio: function() {
            return '/apiweb/GeneraResguard?i=' + this.MatriculesArray[0] + '&g=&u=' + btoa(this.UrlActual); 
        },
        getOptions: function() {                        
            let TipusPagaments = (this.isAdmin) ? this.DetallCurs.CURSOS_PagamentIntern.split('@') : this.DetallCurs.CURSOS_PagamentExtern.split('@');            
            let ReturnPagaments = [];
            
            if( this.LlistaEsperaActiu ) {
                ReturnPagaments.push({'id': CONST_PAGAMENT_LLISTA_ESPERA, 'text': "Posar en llista d'espera" });
                this.TipusPagament = CONST_PAGAMENT_LLISTA_ESPERA;
            } else {
             
                if(TipusPagaments.length > 1) {
                    ReturnPagaments.push({"id": CONST_PAGAMENT_CAP, "text": "-- ESCULL MODALITAT --"});
                    this.TipusPagament = CONST_PAGAMENT_CAP;
                } else {
                    this.TipusPagament = TipusPagaments[0];
                }
    
                for(let T of TipusPagaments) {                
                    if(T == CONST_PAGAMENT_METALIC) ReturnPagaments.push({"id": CONST_PAGAMENT_METALIC, "text": "Metàl·lic"});
                    if(T == CONST_PAGAMENT_TARGETA) ReturnPagaments.push({"id": CONST_PAGAMENT_TARGETA, "text": "Targeta (Online)"});
                    if(T == CONST_PAGAMENT_DATAFON) ReturnPagaments.push({"id": CONST_PAGAMENT_DATAFON, "text": "Targeta (Datàfon)"});
                    if(T == CONST_PAGAMENT_INVITACIO) ReturnPagaments.push({"id": CONST_PAGAMENT_INVITACIO, "text": "Invitació"});
                    
                    if(T == CONST_PAGAMENT_CODI_DE_BARRES) ReturnPagaments.push({"id": CONST_PAGAMENT_CODI_DE_BARRES, "text": "Codi de barres"});
                    if(T == CONST_PAGAMENT_RESERVA) ReturnPagaments.push({"id": CONST_PAGAMENT_RESERVA, "text": "Reserva (Gratuït)"});
                    if(T == CONST_PAGAMENT_LLISTA_ESPERA && this.PlacesLliures <= 0) ReturnPagaments.push({"id": CONST_PAGAMENT_LLISTA_ESPERA, "text": "Posar en llista d'espera (Gratuït)"});                    
                }                

            }            
                        
            return ReturnPagaments;
        }
    },
    watch: {              
    },
    /*
    * Pas 0 = Inici
    * Pas 1 = No he trobat el DNI i demano dades extres per crear usuari.
    * Pas 2 = He trobat el DNI i passo a demanar quantes entrades
    * Pas 3 = No existeix.
    * Pas 4 = Demano quantes entrades vull i finalitzo.
    * Pas 5 = Finalitzada correctament, mostro resguards. 
    * Pas 6 = Hi ha hagut error fent la inscripció
    * Pas 7 = Error previ en condicions del curs ( data inici matrícula, restringit, etc. )
    * Pas 8 = Pagament amb TPV
    * Pas 9 = Pagament amb Datàfon, permet entrar el codi operació
    * Pas 10 = Quan entrem , si l'usuari ja té una matrícula, pot escollir si fer baixa o fer-ne una altra
    */
    methods: {
        EtiquetaTitol: function(DetallCurs) {                    
            if( DetallCurs.CURSOS_Categoria == '29' && DetallCurs.CURSOS_PagamentExtern.includes( CONST_PAGAMENT_TARGETA ) ) return 'Compra una entrada';
            else if( DetallCurs.CURSOS_Categoria == '29' && !DetallCurs.CURSOS_PagamentExtern.includes( CONST_PAGAMENT_TARGETA ) ) return 'Reserva una entrada';
            else return 'Inscriu-te a l\'activitat';
        },
        PoseuEnContacteString: function() {
            return '<br /> Poseu-vos en contacte amb ' + this.DetallSite.SITES_Nom + ' al telèfon ' + this.DetallSite.SITES_Telefon + ' o per correu electrònic a ' + this.DetallSite.SITES_Email + ' per a tenir més informació.';
        },
        PucMatricular: function(DetallCurs) {
          
            // Treballem les places lliures i la llista d'espera                   
            this.PlacesLliures = DetallCurs.CURSOS_Places - this.SeientsOcupats.QuantesMatricules;                                       
            
            // Tindrà la llista d'espera activa si és administrador i té activada l'opció o bé si és usuari i també hi ha activada l'opció.
            this.TeLlistaEsperaActiu = ( 
                this.isAdmin && 0 <=  DetallCurs.CURSOS_PagamentIntern.split("@").findIndex( X => ( X == 36 ) )
                || 0 <=  DetallCurs.CURSOS_PagamentExtern.split("@").findIndex( X => ( X == 36 ) ) 
            );                   
            
            if( this.PlacesLliures <= 0 && !this.TeLlistaEsperaActiu ) {
                this.Pas = 7;
                this.ErrorInscripcio = '<strong>Aquest curs no disposa de places lliures.</strong>' + this.PoseuEnContacteString();
                return false;
            } else if( this.PlacesLliures <= 0 && this.TeLlistaEsperaActiu ) {
                this.LlistaEsperaActiu = true;                                       
            }
                    
            if(this.isAdmin) return true;
            else {               
                if( DetallCurs && DetallCurs.CURSOS_VisibleWeb) {
                    //Existeix un curs on matricular-se
                    const DataIniciMatricula = ConvertirData(DetallCurs.CURSOS_DataInMatricula, 'Javascript');   // Funció const_and_helpers.js               
                    const DIM = ConvertirData(DetallCurs.CURSOS_DataInMatricula, 'TDMA');   // Funció const_and_helpers.js                               
                    const DataFiMatricula = ConvertirData(DetallCurs.CURSOS_DataFiMatricula, 'Javascript');   // Funció const_and_helpers.js               
                    const DFM = ConvertirData(DetallCurs.CURSOS_DataFiMatricula, 'TDMA');   // Funció const_and_helpers.js               
                    const Today = new Date();
                    Today.setHours(0,0,0,0);
  
                    if( !( DataIniciMatricula <= Today && DataFiMatricula >= Today ) ) {
                        this.Pas = 7;
                        this.ErrorInscripcio = 'Inscripcions obertes del &nbsp;<b>' + DIM + '</b>&nbsp;al&nbsp;<b>' + DFM + '</b>&nbsp;inclosos.';
                        return false;
                    }                
  
                    // Si només té matrícula presencial donem error
                    if( 0 <=  DetallCurs.CURSOS_PagamentExtern.split("@").findIndex( X => ( X == 37 ) )) {
                        this.Pas = 7;
                        this.ErrorInscripcio = '<strong>Aquest curs només disposa de matrícula presencial.</strong>' + this.PoseuEnContacteString();
                        return false;
                    }
    
                    return true;               
                }
            }
        },
        getQuantesPlacesOptions: function() {           
                        
            if( this.PlacesLliures > 0 && this.DetallCurs.CURSOS_IsRestringit && this.DetallCurs.CURSOS_IsRestringit.indexOf(CONST_RESTRINGIT_NOMES_UNA) >= 0 ) return [1];

            if( this.PlacesLliures < 6 && this.PlacesLliures > 0 ){                
                return Array.from(Array(this.PlacesLliures), (_, i) => i + 1);                
            } else return Array.from(Array(5), (_, i) => i + 1);

        }, 
        doAccioRepetit: function($accio) { 
            switch($accio) {
                case 'Baixa': 
                    axios.get( CONST_api_web + '/AccionsExisteixDNI', {'params': {'I' : this.IdUsuariEncrypted, 'C' : this.DetallCurs.CURSOS_IdCurs, 'A' : 'B'}})
                    .then( R => { 
                        this.Alert.Missatge = R.data + " inscripció/ns donades de baixa correctament. Gràcies per avisar-nos.";
                        this.Alert.Class = 'col-12 alert alert-success';
                        this.Alert.Mostro = true;
                    })
                    .catch( E => { 
                        this.Alert.Missatge = "Hi ha hagut el següent error: " + E;
                        this.Alert.Class = 'col-12 alert alert-danger';
                        this.Alert.Mostro = true;
                    });
                break;
                case 'NovaInscripcio': 
                    this.Pas = 2;
                break;
                case 'Reenviar':
                    axios.get( CONST_api_web + '/AccionsExisteixDNI', {'params': {'I' : this.IdUsuariEncrypted, 'C' : this.DetallCurs.CURSOS_IdCurs, 'A' : 'R'}})
                    .then( R => { 
                        this.Alert.Missatge = R.data + " inscripció/ns reenviades correctament al seu correu. Si no les ha rebut, faci'ns ho saber.";
                        this.Alert.Class = 'col-12 alert alert-success';
                        this.Alert.Mostro = true;
                    })
                    .catch( E => { 
                        this.Alert.Missatge = "Hi ha hagut el següent error: " + E;
                        this.Alert.Class = 'col-12 alert alert-danger';
                        this.Alert.Mostro = true;
                    });
                break;
            }
        },
        OnUsuariLoaded: function($UserDataDNIAndEncryptedId) {
            
            const $DNI = $UserDataDNIAndEncryptedId.DNI;
            const $IdUsuariEncrypted = $UserDataDNIAndEncryptedId.IdUsuariEncrypted;
            this.IdUsuariEncrypted = $IdUsuariEncrypted;            
            
            this.Loading = true;
            axios.get( CONST_api_web + '/getPermisosUsuarisCursos', {'params': {'DNI': $DNI, 'IdUsuariEncrypted': this.IdUsuariEncrypted, 'idCurs': this.DetallCurs.CURSOS_IdCurs, 'IsRestringit': this.DetallCurs.CURSOS_IsRestringit }}).then( X => {                    
                this.Loading = false;
                                
                // Segons restriccions pot matricular o bé és administrador i saltem les restriccions
                if( X.data.PotMatricularCursRestringit.IsOk || this.isAdmin ) {                    
                    // Si l'usuari ja té una matrícula a aquest curs, li deixem escollir si fer baixa o fer-ne una altra
                    if(X.data.HasUsuariMatriculaAAquestCurs) {
                        this.Pas = 10;
                    } else {
                        this.Pas = 2;
                    }                                
                } else {                                                         
                    this.Pas = 7; 
                    this.ErrorInscripcio = '<strong>Vostè no disposa de permisos per a matricular-se en aquest curs.</strong><br />';
                    if(X.data.PotMatricularCursRestringit.CursosOk.length > 0) {
                        this.ErrorInscripcio += 'Els cursos als que es pot matricular són: <ul style="display: block; width: 100%; margin-top: 2vw;">';
                        for(C of X.data.PotMatricularCursRestringit.CursosOk) {
                            this.ErrorInscripcio += '<li><a href="/inscripcio/'+C.id+'">'+C.nom+'</a></li>';
                        }
                        this.ErrorInscripcio += '</ul>';                                
                    }
                                                        
                }                                                                
            }).catch( E => { alert(E); });
        
        },
        setLocalitat: function(fila, seient) {
                                    
            const IndexEscollits = this.Localitats.findIndex( X => X[0] == fila && X[1] == seient);
            const ExisteixAJaComprats = (this.SeientsOcupats.Localitats.findIndex( X => X[0] == fila && X[1] == seient) > -1);

            // Si en tinc 5 i el que he escollit no existeix al llistat, no puc agafar-ne més. 
            if(this.Localitats.length == 5 && IndexEscollits < 0 ) alert('Ho sento però només pots escollir 5 localitats.');
            else {
                // Si l'element ja existeix, el trec. Si no existeix i no existeix als ja comprats per altres persones, l'afegeixo
                if(IndexEscollits > -1) this.Localitats.splice(IndexEscollits, 1);
                else if( !ExisteixAJaComprats ) this.Localitats.push([fila, seient]);            
            }            
        },
        getColorLocalitat: function(fila, seient, Estil, Tipus) {
            
            let Estil2 = {}; Object.assign( Estil2, Estil)
            const ExisteixAEscollits = (this.Localitats.findIndex( X => X[0] == fila && X[1] == seient) > -1);
            const ExisteixAJaComprats = (this.SeientsOcupats.Localitats.findIndex( X => X[0] == fila && X[1] == seient) > -1);
            if ( ExisteixAEscollits ) {
                Estil2["color"] = "Purple";                
                Estil2["cursor"] = "Pointer";
            } else if (ExisteixAJaComprats ) {
                Estil2["color"] = "Red";
                Estil2["cursor"] = "not-allowed";
            } else { 
                Estil2["color"] = "Green";
                Estil2["cursor"] = "Pointer";                
            }

            if(Tipus == 'bloc') { Estil2["color"] = 'gray'; Estil2["cursor"] = "not-allowed"; }
            if(Tipus == 'blanc') { Estil2["color"] = '#EAEAEA'; }
            
            return Estil2;
        },
        getPreu: function() {
            
            let Preu = this.DetallCurs.CURSOS_Preu

            // Si tenim la llista d'espera activa, retornem 0
            if( this.LlistaEsperaActiu ) return 0;
            if( this.TipusPagament == CONST_PAGAMENT_INVITACIO ) return 0;

            // Mirem si hem escollit descompte
            if( this.DescompteAplicat > 0 ) {
                let ObjecteDescompteAplicat = this.DetallDescomptes.find( X => X.DESCOMPTES_IdDescompte == this.DescompteAplicat );
                if(ObjecteDescompteAplicat.DESCOMPTES_Percentatge > 0) Preu = Preu - (Preu * ObjecteDescompteAplicat.DESCOMPTES_Percentatge / 100 );
                else Preu = ObjecteDescompteAplicat.DESCOMPTES_Preu;
            }

            if( this.DetallTeatre.Seients.length > 0 ) Preu = Preu * this.Localitats.length;
            else Preu = Preu * this.QuantesEntrades;

            return Preu;
            
        },
        NoPucSeguir: function() {            
            return (   ! this.ConfirmoAssistencia 
                    || ( this.TipusPagament == 0 ) 
                    || ( this.Localitats.length == 0 && this.DetallTeatre.Seients.length > 0 )
                    || ( this.QuantesEntrades == 0 && this.DetallTeatre.Seients.length == 0 )
                    || ( ! ( ! this.DetallCurs.CURSOS_DadesExtres || ( this.DetallCurs.CURSOS_DadesExtres && this.DetallCurs.CURSOS_DadesExtres.length > 4 && this.DadesExtres.length > 1 ) ) )
                    );                                                                                               
        },
        doInscripcio: function() {
            $FD = new FormData();
            $FD.append('IdUsuariEncrypted', this.IdUsuariEncrypted);
            $FD.append('QuantesEntrades', this.QuantesEntrades);
            $FD.append('ActivitatId', this.ActivitatId);
            $FD.append('CicleId', this.CicleId);     
            $FD.append('CursId', this.CursId);     
            $FD.append('TipusPagament', this.TipusPagament);       
            $FD.append('UrlDesti', this.UrlActual);
            $FD.append('DescompteAplicat', this.DescompteAplicat);
            $FD.append('Localitats', JSON.stringify(this.Localitats));
            $FD.append('Token', JSON.stringify(this.Token));
            $FD.append('DadesExtres', this.DadesExtres);
            
            this.Loading = true;
            axios.post( CONST_api_web + '/NovaInscripcio', $FD ).then( X => {
                this.Loading = false;
                if(X.data.AltaUsuari && X.data.AltaUsuari.MATRICULES.length > 0) {
                    // Si el pagament és amb targeta, anem a la nova web per a fer el pagament
                    if(X.data.AltaUsuari.TPV) {
                        Object.keys( X.data.AltaUsuari.TPV ).forEach((K) => Vue.set(this.TPV, K, X.data.AltaUsuari.TPV[K]));                        
                        this.Pas = 8; // Fem pagament amb TPV
                    } else if( X.data.AltaUsuari.TIPUS_PAGAMENT == CONST_PAGAMENT_DATAFON ) {
                        this.Pas = 9; // Fem pagament amb Datàfon                                                
                        this.MatriculesArray = X.data.AltaUsuari.MATRICULES;   //{[Matricules, ?TPV]}                                                                        
                    } else {
                        //Mostro el link per baixar-se el resguard d'inscripcions
                        this.Pas = 5; // Finalitzada.                     
                        this.MatriculesArray = X.data.AltaUsuari.MATRICULES;   //{[Matricules, ?TPV]}                                                                        
                    }                    
                    
                } else {
                    //Mostro l'error.
                    this.Pas = 6; //Hi ha error
                    this.ErrorInscripcio = X.data.error;
                }
                
            }).catch( E => { alert(E); });
        },
        doTPVConfirm: function(PagatCorrectament) {
            // let P = prompt('Entreu el codi d\'operació que apareix al TPV');            
            let FD = new FormData();
            FD.append('CodiOperacio', this.CodiOperacio);
            FD.append('Matricules', JSON.stringify(this.MatriculesArray));
            FD.append('PagatCorrectament', (PagatCorrectament) ? 1 : 0 );            
            FD.append('UrlDesti', this.UrlActual);
            axios.post( CONST_api_web + '/PutOperacioDatafon', FD ).then( X => {
                if(PagatCorrectament) { this.Pas = 5; }
                else { this.Pas = 6; this.ErrorInscripcio = 'El pagament no s\'ha finalitzat correctament'; }
            }).catch( E => { alert(E); });
        },
        getNomBotoInscriume() {
            if(this.getPreu() > 0) {
                return 'Seguir fent el pagament';
            } else {
                return 'Inscriu-me';
            }
        }

    },
    template: `            

    <div v-if="DetallCurs.CURSOS_VisibleWeb == 1 || this.isAdmin">
        <div class="formulari-inscripcio">                        

            <h2>{{EtiquetaTitol(DetallCurs)}}</h2>
            <div class="row alert alert-info" v-if="isAdmin">Estàs accedint com administrador.</div>
            



            <form-usuari-auth                 
                v-if="Pas == 0 || Pas == 1"
                @on-id-usuari-encrypted-loaded="OnUsuariLoaded">
            </form-usuari-auth>
           



            <div v-if="Pas == 2 && !LlistaEsperaActiu" class="row alert alert-success"> Hem trobat el seu DNI a la nostra base de dades. <br />Pot seguir amb la inscripció! </div>
            <div v-if="Pas == 2 && LlistaEsperaActiu" class="row alert alert-danger"> L'activitat ja no disposa de places lliures. Tot i això, si ho desitja pot posar-se en llista d'espera i l'avisarem si torna a haver-n'hi. </div>             
            



            <div v-if="Pas == 4 || Pas == 2">

                <div v-if="DetallTeatre.Seients.length > 0" class="row" style="display: flex; flex-direction: column; ">                   
                    <div v-for="Fila of DetallTeatre.Seients" style="display: flex; flex-wrap: nowrap;">
                        <div style="" v-for=" Seient of Fila ">
                            <div v-if="Seient.tipus == 'text'" :style="DetallTeatre.Estils[Seient.Estil]"> <h1>{{Seient.text}}</h1> </div>
                            <div v-if="Seient.tipus == 'fila'" :style="DetallTeatre.Estils[Seient.Estil]"> <h4>{{Seient.text}}</h4> </div>
                            <div v-if="Seient.tipus == 'loc'" :style="getColorLocalitat(Seient.fila, Seient.seient, DetallTeatre.Estils[Seient.Estil], Seient.tipus )">
                                <a @click="setLocalitat(Seient.fila, Seient.seient)"><i class="fas fa-chair"></i></a>
                            </div>
                            <div v-if="Seient.tipus == 'bloc'" :style="getColorLocalitat(Seient.fila, Seient.seient, DetallTeatre.Estils[Seient.Estil], Seient.tipus )"><i class="fas fa-times"></i></div>
                            <div v-if="Seient.tipus == 'blanc'" :style="getColorLocalitat(Seient.fila, Seient.seient, DetallTeatre.Estils[Seient.Estil], Seient.tipus )"><i class="fas fa-chair"></i></div>
                        </div>
                    </div>
                </div>
            
                <div class="row">
                
                    <div class="col" v-if="DetallTeatre.Seients.length == 0">
                        <label for="QuantesEntrades">Quantes places</label>
                        <select :disabled="!(Pas == 2 || Pas == 4)" class="form-control" v-model="QuantesEntrades" id="QuantesEntrades">
                            <option v-for="Q of getQuantesPlacesOptions()" :value="Q">{{Q}}</option>
                        </select>                                        
                    </div>            
                    
                    <div class="col" v-if="true || DetallCurs.CURSOS_Preu == 0">
                        <label for="TipusPagament">Tipus pagament</label>
                        <select :disabled="!(Pas == 2 || Pas == 4)" class="form-control" v-model="TipusPagament" id="TipusPagament">
                            <option v-for="O in getOptions" :value="O.id">{{O.text}}</option>
                        </select>                                        
                    </div>

                    <div class="col" v-if="DetallDescomptes.length > 1 && LlistaEsperaActiu == 0">
                        <label for="TipusPagament">Descompte</label>
                        <select :disabled="!(Pas == 2 || Pas == 4)" class="form-control" v-model="DescompteAplicat" id="DescompteAplicat">
                            <option v-for="O in DetallDescomptes" :value="O.DESCOMPTES_IdDescompte">{{O.DESCOMPTES_Nom}}</option>
                        </select>                                        
                    </div>

                    <div class="col" v-if="( DetallCurs.CURSOS_DadesExtres && DetallCurs.CURSOS_DadesExtres.length > 4 )">
                        <label for="DadesExtres">{{DetallCurs.CURSOS_DadesExtres}}</label>                        
                        <input type="text" class="form-control" v-model="DadesExtres" id="DadesExtres" :placeholder="DetallCurs.CURSOS_DadesExtres">
                    </div>
                
                </div>

                <div class="row">
                    <div class="col">
                        <span style="font-size: 1.5rem; font-weight: bold;">Preu final: {{getPreu()}} €</span>
                    </div>
                </div>

                <div class="form-check">
                    <input :disabled="!(Pas == 2 || Pas == 4)" type="checkbox" class="form-check-input" v-model="ConfirmoAssistencia" id="Assistire">
                    <label class="form-check-label" for="Assistire">Confirmo que <b>assistiré a l'acte</b> o que <b>avisaré</b>, a la Casa de Cultura, en cas de no poder-ho fer.</label>
                </div>
                
                <div v-show="!Loading">
                    <button v-if="LlistaEsperaActiu" :disabled="NoPucSeguir()" type="submit" class="boto btn btn-danger" @click.prevent="doInscripcio()">Posa'm en espera</button>
                    <small v-if="LlistaEsperaActiu"  id="EmailHelp" class="form-text text-muted">Vostè ha quedat en llista d'espera. Si, en un futur hi ha places disponibles, ens posarem en contacte amb vostè.</small>
    
                    <button v-if="!LlistaEsperaActiu"  :disabled="NoPucSeguir()" type="submit" class="boto btn btn-primary" @click.prevent="doInscripcio()">{{getNomBotoInscriume()}}</button>
                    <small  v-if="!LlistaEsperaActiu" id="EmailHelp" class="form-text text-muted">Nomes podrà prèmer el botó si ha omplert totes les dades necessàries.</small>
                </div>

                <div v-show="Loading" style="margin-top: 2vw">
                    <div class="alert alert-info">Carregant...</div>
                </div>
                 
            </div>





            <div v-if="Pas == 5">            
                <div v-if="TipusPagament != TEMP_CONST_LLISTA_ESPERA " class="row alert alert-success Pas5"> 
                    <p>La seva inscripció ha finalitzat correctament. Pot descarregar-se els resguards clicant els enllaços:</p>
                    <p><a target="_NEW" :href="genUrlInscripcio">Baixa't la inscripció</a></p>                            
                </div>
                <div v-else class="row alert alert-warning Pas5"> 
                    <p>La seva inscripció ha finalitzat correctament.</p><p>Si en un futur tornem a tenir places disponibles, contactarem amb vostè.</p>                    
                </div>
            </div>




            <div v-if="Pas == 6" class="row alert alert-danger Pas6"> 
                <p>Hi ha hagut el següent error fent la seva inscripció. Pot consultar amb nosaltres trucant al 972.20.20.13 (Ext 3).</p>
                <p><b>{{ErrorInscripcio}}</b>                            </p>                
                <br /><p> <a href="./">Torna a carregar la pàgina.</a></p>
            </div>




            <div class="row alert alert-danger" v-if=" ! PucMatricular(DetallCurs) || Pas == 7" v-html="ErrorInscripcio">            
            </div>





            <div class="row" v-if="Pas == 9">
                <div class="col">
                    <label for="CodiOperacio">Codi d'operació datàfon</label>                        
                    <input type="text" class="form-control" v-model="CodiOperacio" />
                </div>
                <div class="col">                        
                    <button class="boto btn btn-success" @click.prevent="doTPVConfirm(true)">Pagat!</button>
                </div>
                <div class="col">                        
                    <button class="boto btn btn-danger" @click.prevent="doTPVConfirm(false)">No pagat!</button>
                </div>

            </div>

            <div v-if="Pas == 10">                
                <div v-if="!Alert.Mostro" class="row">
                    <div class="col-12">
                        Hem trobat inscripcions amb aquest DNI per aquest curs. 
                        <br /><br /><strong>Quina acció desitja fer?</strong>
                    </div>           
                    <div class="col"><button class="boto btn btn-danger" @click.prevent="doAccioRepetit('Baixa')">Donar-me de baixa</button></div>
                    <div class="col"><button class="boto btn btn-info" @click.prevent="doAccioRepetit('NovaInscripcio')">Fer una altra inscripció</button></div>
                    <div class="col"><button class="boto btn btn-success" @click.prevent="doAccioRepetit('Reenviar')">Reenvia'm les entrades</button></div>
                </div>

                <div v-if="Alert.Mostro" :class="Alert.Class">{{Alert.Missatge}}</div>

            </div>

            
        </div>
        
        <form class="formulari-inscripcio" v-if="Pas == 8" name="frm" :action="TPV.url" method="POST" target="_blank">
            <input type="hidden" name="Ds_SignatureVersion" :value="TPV.version" /></br>
            <input type="hidden" name="Ds_MerchantParameters" :value="TPV.params"/></br>
            <input type="hidden" name="Ds_Signature" :value="TPV.signature" /></br>
            <button type="submit" class="boto btn btn-primary"> Fes el pagament </button>
        </form>    

    </div>

`
});