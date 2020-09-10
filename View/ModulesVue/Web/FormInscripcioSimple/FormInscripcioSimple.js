
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
                    DNI: '', 
                    Nom: '', 
                    Cog1: '',
                    Cog2: '',
                    Telefon: '', 
                    Email: '', 
                    Municipi: '',
                    Genere: '',
                    AnyNaixement: '',
                    QuantesEntrades: 1,
                    DescompteAplicat: -1,
                    Localitats: [],
                    TipusPagament: CONST_PAGAMENT_CAP,                         
                    PlacesLliures: 0,
                    LlistaEsperaActiu: false,
                    DadesExtres: '',
                    CodiOperacio: '',
                    Pas: 0, 
                    classDNI: 'form-control', 
                    classNom: 'form-control',
                    classCog1: 'form-control',
                    classCog2: 'form-control',
                    classTelefon: 'form-control',
                    classEmail: 'form-control', 
                    classMunicipi: 'form-control',
                    classGenere: 'form-control',
                    classAnyNaixement: 'form-control',
                    MatriculesArray: Array,
                    ErrorInscripcio: '',
                    ConfirmoAssistencia: false,
                    TPV: {},
                    isAdmin: (this.Token[0] == this.DetallCurs.CURSOS_SiteId),
                    Loading: false
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
            this.TeLlistaEsperaActiu = ( 0 <=  DetallCurs.CURSOS_PagamentExtern.split("@").findIndex( X => ( X == 36 ) ) );                   
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
        dnikeymonitor: function($event) {
            
            if( ValidaDNI(this.DNI) ) {
                this.classDNI = 'form-control is-valid';
                this.Loading = true;
                axios.get( CONST_api_web + '/ExisteixDNI', {'params': {'DNI': this.DNI, 'idCurs': this.DetallCurs.CURSOS_IdCurs, 'IsRestringit': this.DetallCurs.CURSOS_IsRestringit }}).then( X => {                    
                    this.Loading = false;
                    if( X.data.PotMatricularCursRestringit.IsOk || this.isAdmin ) {
                        if(X.data.ExisteixDNI) {
                            this.Pas = 2;
                        } else {                               
                            this.Pas = 1;
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
                        
                        // this.ErrorInscripcio //+ this.PoseuEnContacteString();
                    }                                        
                }).catch( E => { alert(E); });
            } else {
                this.Pas = 0;
                this.classDNI = 'form-control is-invalid';                
            }
        },
        keymonitor: function() {                        
            const ValNom = (this.Nom.length > 1);
            const ValCog1 = (this.Cog1.length > 1);
            const ValTelefon = ValidaTelefon( this.Telefon );
            const ValEmail = ValidaEmail(this.Email);
            const ValMunicipi = (true);
            const ValGenere = (true);
            const ValAnyNaixement = (!isNaN(this.AnyNaixement));

            this.classNom = (!ValNom) ? 'form-control is-invalid' : 'form-control is-valid';
            this.classCog1 = (!ValCog1) ? 'form-control is-invalid' : 'form-control is-valid';
            this.classCog2 = 'form-control';
            this.classEmail = (!ValEmail) ? 'form-control is-invalid' : 'form-control is-valid';
            this.classTelefon = (!ValTelefon) ? 'form-control is-invalid' : 'form-control is-valid';

            this.classMunicipi = (!ValMunicipi) ? 'form-control is-invalid' : 'form-control is-valid';
            this.classGenere = (!ValGenere) ? 'form-control is-invalid' : 'form-control is-valid';
            this.classAnyNaixement = (!ValAnyNaixement) ? 'form-control is-invalid' : 'form-control is-valid';
            
            if( ValNom && ValCog1 && ValTelefon && ValEmail && ValAnyNaixement ) {
                return true;
            } else {
                return false;
            }
        },
        botoDadesUsuari: function() {
            if(this.keymonitor()) this.Pas = 4;
            else this.Pas = 1;
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
        getColorLocalitat: function(fila, seient, Estil) {
            
            let Estil2 = {}; Object.assign( Estil2, Estil)
            const ExisteixAEscollits = (this.Localitats.findIndex( X => X[0] == fila && X[1] == seient) > -1);
            const ExisteixAJaComprats = (this.SeientsOcupats.Localitats.findIndex( X => X[0] == fila && X[1] == seient) > -1);
            if ( ExisteixAEscollits ) {
                Estil2["color"] = "Red";
            } else if (ExisteixAJaComprats ) {
                Estil2["color"] = "Blue";
            } else { 
                Estil2["color"] = "Black";
            }
            
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
            $FD.append('DNI', this.DNI);
            $FD.append('Nom', this.Nom);
            $FD.append('Cog1', this.Cog1);
            $FD.append('Cog2', this.Cog2);
            $FD.append('Email', this.Email);
            $FD.append('Telefon', this.Telefon);            
            $FD.append('Municipi', this.Municipi);
            $FD.append('Genere', this.Genere);
            $FD.append('AnyNaixement', this.AnyNaixement);
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
            axios.post( CONST_api_web + '/AltaUsuariSimple', $FD ).then( X => {
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
            console.log(PagatCorrectament);
            let FD = new FormData();
            FD.append('CodiOperacio', this.CodiOperacio);
            FD.append('Matricules', JSON.stringify(this.MatriculesArray));
            FD.append('PagatCorrectament', (PagatCorrectament) ? 1 : 0 );            
            axios.post( CONST_api_web + '/PutOperacioDatafon', FD ).then( X => {
                if(PagatCorrectament) { this.Pas = 5; }
                else { this.Pas = 6; this.ErrorInscripcio = 'El pagament no s\'ha finalitzat correctament'; }
            }).catch( E => { alert(E); });
        }

    },
    template: `            

    <div v-if="DetallCurs.CURSOS_VisibleWeb == 1">
        <form class="formulari-inscripcio">                        

            <h2>{{EtiquetaTitol(DetallCurs)}}</h2>
            <div class="row alert alert-info" v-if="isAdmin">Estàs accedint com administrador.</div>

            <div class="row alert alert-danger" v-if=" ! PucMatricular(DetallCurs) || Pas == 7" v-html="ErrorInscripcio">            
            </div>

            <div v-if="Pas == 5" class="row alert alert-success Pas5"> 
                <p>La seva inscripció ha finalitzat correctament. Pot descarregar-se els resguards clicant els enllaços:</p>
                <p><a target="_NEW" :href="genUrlInscripcio">Baixa't la inscripció</a></p>                            
            </div>

            <div v-if="Pas == 6" class="row alert alert-danger Pas6"> 
                <p>Hi ha hagut el següent error fent la seva inscripció. Pot consultar amb nosaltres trucant al 972.20.20.13 (Ext 3).</p>
                <p><b>{{ErrorInscripcio}}</b>                            </p>                
                <br /><p> <a href="./">Torna a carregar la pàgina.</a></p>
            </div>

            <div class="row" v-if="Pas == 0">
                <div v-show="!Loading" class="col">
                    <label for="DNI">DNI/NIE</label>
                    <input type="text" :class="classDNI" id="DNI" v-on:keyup="dnikeymonitor" v-model="DNI" aria-describedby="DNI" placeholder="Escriu el DNI/NIE..." />                         
                    <small id="DNIHelp" class="form-text text-muted">Entri el seu DNI per apuntar-se. </small>                
                </div>            
                <div class="col" v-show="Loading">
                    <div class="alert alert-info">Carregant...</div>
                </div>
            </div>
            
            <div v-if="Pas == 2 && !LlistaEsperaActiu" class="row alert alert-success"> Hem trobat el seu DNI a la nostra base de dades. <br />Pot seguir amb la inscripció! </div>
            <div v-if="Pas == 2 && LlistaEsperaActiu" class="row alert alert-danger"> L'activitat ja no disposa de places lliures. Tot i això, si ho desitja pot posar-se en llista d'espera i l'avisarem si torna a haver-n'hi. </div>
             
            <div v-if="Pas == 1" class="row alert alert-warning"> No hem trobat el seu DNI a la nostra base de dades. <br />Si és tant amable, ens hauria d'informar del seu nom, telèfon i email per si hem de posar-nos en conacte amb vostè per a poder seguir amb la inscripció. </div>
            
            <div v-if="Pas == 1">
                <div class="row">                                                
                    <div class="col">
                        <label for="NomComplet">Nom</label>
                        <input type="text" :class="classNom" v-on:blur="keymonitor" v-model="Nom" id="NomComplet" aria-describedby="Nom complet" placeholder="Escriu el nom..." /> 
                        <small id="NomCompletHelp" class="form-text text-muted">Entri el seu nom complet.</small>                
                    </div>
                    <div class="col">
                        <label for="Cog1">Primer cognom</label>
                        <input type="text" :class="classCog1" v-on:blur="keymonitor" v-model="Cog1" id="Cog1" aria-describedby="Primer cognom" placeholder="" /> 
                        <small id="Cog1Help" class="form-text text-muted">Entri el seu primer cognom.</small>                
                    </div>            
                    <div class="col">
                        <label for="Cog2">Segon cognom</label>
                        <input type="text" :class="classCog2" v-on:blur="keymonitor" v-model="Cog2" id="Cog2" aria-describedby="Segon cognom" placeholder="" /> 
                        <small id="Cog2Help" class="form-text text-muted">Entri el seu segon cognom, si en té.</small>                
                    </div>                        
                </div>
                <div class="row">                                                
                    <div class="col">
                        <label for="telefon">Mòbil</label>
                        <input type="text" :class="classTelefon" v-on:blur="keymonitor" v-model="Telefon" id="telefon" placeholder="">
                        <small id="TelefonHelp" class="form-text text-muted">Entri el seu telèfon de contacte.</small>
                    </div>
                    <div class="col">
                        <label for="Email">Correu electrònic</label>
                        <input type="text" :class="classEmail" v-on:blur="keymonitor" v-model="Email" id="telefon" placeholder="">
                        <small id="EmailHelp" class="form-text text-muted">Entri el seu correu electrònic.</small>
                    </div>            
                </div>
                <div class="row">                                                
                    <div class="col">
                        <label for="municipi">Municipi</label>
                        <input type="text" :class="classMunicipi" v-on:blur="keymonitor" v-model="Municipi" id="municipi" placeholder="">
                        <small id="MunicipiHelp" class="form-text text-muted">Opcional: El seu municipi de residència.</small>
                    </div>
                    <div class="col">
                        <label for="genere">Gènere</label>
                        <select :class="classGenere" v-on:change="keymonitor" v-model="Genere" id="genere">
                            <option value="M">Masculí</option>
                            <option value="F">Femení</option>
                            <option value="A">Altres</option>
                        </select>                
                        <small id="GenereHelp" class="form-text text-muted">Opcional: El seu gènere.</small>
                    </div>            
                    <div class="col">
                        <label for="anynaixement">Any de naixement</label>
                        <input type="text" :class="classAnyNaixement" v-on:blur="keymonitor" v-model="AnyNaixement" id="anynaixement" placeholder="">
                        <small id="AnyNaixementHelp" class="form-text text-muted">Opcional: El seu any de naixement.</small>
                    </div>                        

                    <div class="col-12">
                        <button :disabled="!keymonitor()" type="submit" class="btn btn-info" @click.prevent="botoDadesUsuari()">Segueix...</button>
                    </div>                    

                </div>    
            </div>    
            
            
            <div v-if="Pas == 4 || Pas == 2">

                <div v-if="DetallTeatre.Seients.length > 0" class="row" style="display: flex; flex-direction: column; ">                   
                    <div v-for="Fila of DetallTeatre.Seients" style="display: flex; flex-wrap: nowrap;">
                        <div style="" v-for=" Seient of Fila ">
                            <div v-if="Seient.tipus == 'text'" :style="DetallTeatre.Estils[Seient.Estil]"> <h1>{{Seient.text}}</h1> </div>
                            <div v-if="Seient.tipus == 'fila'" :style="DetallTeatre.Estils[Seient.Estil]"> <h4>{{Seient.text}}</h4> </div>
                            <div v-if="Seient.tipus == 'loc'" :style="getColorLocalitat(Seient.fila, Seient.seient, DetallTeatre.Estils[Seient.Estil] )">
                                <a class="withHand" @click="setLocalitat(Seient.fila, Seient.seient)"><i class="fas fa-chair"></i></a>
                            </div>
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
                    <button v-if="LlistaEsperaActiu" :disabled="NoPucSeguir()" type="submit" class="btn btn-danger" @click.prevent="doInscripcio()">Posa'm en espera</button>
                    <small v-if="LlistaEsperaActiu"  id="EmailHelp" class="form-text text-muted">Vostè ha quedat en llista d'espera. Si, en un futur hi ha places disponibles, ens posarem en contacte amb vostè.</small>
    
                    <button v-if="!LlistaEsperaActiu"  :disabled="NoPucSeguir()" type="submit" class="btn btn-primary" @click.prevent="doInscripcio()">Inscriu-me</button>
                    <small  v-if="!LlistaEsperaActiu" id="EmailHelp" class="form-text text-muted">Nomes podrà prèmer el botó si ha omplert totes les dades necessàries.</small>
                </div>

                <div v-show="Loading" style="margin-top: 2vw">
                    <div class="alert alert-info">Carregant...</div>
                </div>
                 
            </div>

            <div class="row" v-if="Pas == 9">
                <div class="col">
                    <label for="CodiOperacio">Codi d'operació datàfon</label>                        
                    <input type="text" class="form-control" v-model="CodiOperacio" />
                </div>
                <div class="col">                        
                    <button class="btn btn-success" @click.prevent="doTPVConfirm(true)">Pagat!</button>
                </div>
                <div class="col">                        
                    <button class="btn btn-danger" @click.prevent="doTPVConfirm(false)">No pagat!</button>
                </div>

            </div>

        </form>
        
        <form class="formulari-inscripcio" v-if="Pas == 8" name="frm" :action="TPV.url" method="POST" target="_blank">
            <input type="hidden" name="Ds_SignatureVersion" :value="TPV.version" /></br>
            <input type="hidden" name="Ds_MerchantParameters" :value="TPV.params"/></br>
            <input type="hidden" name="Ds_Signature" :value="TPV.signature" /></br>
            <button type="submit" class="btn btn-primary"> Fes el pagament </button>
        </form>    

    </div>

`
});