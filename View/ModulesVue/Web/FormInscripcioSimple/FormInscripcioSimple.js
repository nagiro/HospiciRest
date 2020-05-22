
Vue.component('form-inscripcio-simple', {
    props: {        
        InputColor: String, 
        InputDades: Object,
        ActivitatId: Number,
        CicleId: Number
    },          
    data: function() {
        return {    ActivitatHome: {}, 
                    DNI: '', 
                    Nom: '', 
                    Cog1: '',
                    Cog2: '',
                    Telefon: '', 
                    Email: '', 
                    QuantesEntrades: 1,
                    Pas: 0, 
                    classDNI: 'form-control', 
                    classNom: 'form-control',
                    classCog1: 'form-control',
                    classCog2: 'form-control',
                    classTelefon: 'form-control',
                    classEmail: 'form-control', 
                    MatriculesArray: Array,
                    ErrorInscripcio: '',
                    ConfirmoAssistencia: false
                }
    },    
    computed: {
    },
    watch: {              
    },
    methods: {
        dnikeymonitor: function($event) {
            
            if( ValidaDNI(this.DNI) ) {
                this.classDNI = 'form-control is-valid';
                axios.get( CONST_api_web + '/ExisteixDNI', {'params': {'DNI': this.DNI}}).then( X => {
                    if(X.data.ExisteixDNI) {
                        this.Pas = 2;
                    } else {                    
                        this.Pas = 1;
                    }
                }).catch( E => { alert(E); });
            } else {
                this.Pas = 0;
                this.classDNI = 'form-control is-invalid';                
            }
        },
        keymonitor: function($event) {                        
            const ValNom = (this.Nom.length > 1);
            const ValCog1 = (this.Cog1.length > 1);
            const ValTelefon = ValidaTelefon( this.Telefon );
            const ValEmail = ValidaEmail(this.Email);

            this.classNom = (!ValNom) ? 'form-control is-invalid' : 'form-control is-valid';
            this.classCog1 = (!ValCog1) ? 'form-control is-invalid' : 'form-control is-valid';
            this.classCog2 = 'form-control';
            this.classEmail = (!ValEmail) ? 'form-control is-invalid' : 'form-control is-valid';
            this.classTelefon = (!ValTelefon) ? 'form-control is-invalid' : 'form-control is-valid';
            
            if( ValNom && ValCog1 && ValTelefon && ValEmail ) {
                this.Pas = 4;
            } else {
                this.Pas = 1;
            }
        },
        doInscripcio: function() {
            $FD = new FormData();
            $FD.append('DNI', this.DNI);
            $FD.append('Nom', this.Nom);
            $FD.append('Cog1', this.Cog1);
            $FD.append('Cog2', this.Cog2);
            $FD.append('Email', this.Email);
            $FD.append('Telefon', this.Telefon);
            $FD.append('QuantesEntrades', this.QuantesEntrades);
            $FD.append('ActivitatId', this.ActivitatId);
            $FD.append('CicleId', this.CicleId);
            
            axios.post( CONST_api_web + '/AltaUsuariSimple', $FD ).then( X => {
                if(X.data.matricules.length > 0) {
                    //Mostro el link per baixar-se el resguard d'inscripcions
                    this.Pas = 5; // Finalitzada.                     
                    this.MatriculesArray = X.data.matricules;
                    
                } else {
                    //Mostro l'error.
                    this.Pas = 6; //Hi ha error
                    this.ErrorInscripcio = X.data.error;
                }
                
            }).catch( E => { alert(E); });
        }

    },
    template: `            

    <form class="formulari-inscripcio">
        <h2>Inscriu-te a l'activitat!</h2>

        <div v-if="Pas == 5" class="row alert alert-success Pas5"> 
            <p>La seva inscripció ha finalitzat correctament. Pot descarregar-se els resguards clicant els enllaços:</p>
            <ul>
                <li v-for="M of MatriculesArray"><a href="/link/descarrega">Matrícula {{M}}</a></li>
            </ul>            
        </div>

        <div v-if="Pas == 6" class="row alert alert-danger Pas6"> 
            <p>Hi ha hagut el següent error fent la seva inscripció. Pot consultar amb nosaltres trucant al 972.20.20.13 (Ext 3).</p>
            <p><b>{{ErrorInscripcio}}</b>                            </p>
        </div>

        <div class="row" v-if="Pas == 0">
            <div class="col">
                <label for="DNI">DNI/NIE</label>
                <input type="text" :class="classDNI" id="DNI" v-on:keyup="dnikeymonitor" v-model="DNI" aria-describedby="DNI" placeholder="Escriu el DNI/NIE..." /> 
                <small id="DNIHelp" class="form-text text-muted">Entri el seu DNI per apuntar-se. </small>                
            </div>            
        </div>
        
        <div v-if="Pas == 2" class="row alert alert-success"> Hem trobat el seu DNI a la nostra base de dades. <br />Pot seguir amb la inscripció! </div>
        
        <div v-if="Pas == 1" class="row alert alert-warning"> No hem trobat el seu DNI a la nostra base de dades. <br />Si és tant amable, ens hauria d'informar del seu nom, telèfon i email per si hem de posar-nos en conacte amb vostè per a poder seguir amb la inscripció. </div>
        
        <div class="row" v-if="Pas == 1 || Pas == 4">                                                
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
        <div class="row" v-if="Pas == 1 || Pas == 4">                                                
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
        <div>

            <div class="row">
            
                <div class="col">
                    <label for="QuantesEntrades">Quantes places reserves</label>
                    <select :disabled="!(Pas == 2 || Pas == 4)" class="form-control" v-model="QuantesEntrades" id="QuantesEntrades">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>                                        
                </div>            
            
            </div>

            <div class="form-check">
                <input :disabled="!(Pas == 2 || Pas == 4)" type="checkbox" class="form-check-input" v-model="ConfirmoAssistencia" id="Assistire">
                <label class="form-check-label" for="Assistire">Confirmo que <b>assistiré a l'acte</b> o que <b>avisaré</b>, a la Casa de Cultura, en cas de no poder-ho fer.</label>
            </div>
            
            <button :disabled="!ConfirmoAssistencia" type="submit" class="btn btn-primary" @click.prevent="doInscripcio()">Inscriu-me</button>
            <small id="EmailHelp" class="form-text text-muted">Nomes podrà prèmer el botó si ha omplert totes les dades necessàries.</small>
        </div>
    </form>

`
});