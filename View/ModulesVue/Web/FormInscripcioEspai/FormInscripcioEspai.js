
Vue.component('form-inscripcio-espai', {        
    props: { 
        IdSite: Number
    },          
    created: function() {},
    data: function() {
        return {            
            isFormLoading: false,
            IdUsuariEncrypted: '',
            OpcionsEstat: [{ id: 0, text: "En espera"}, { id: 1, text: "Acceptada"},{ id: 2, text:"Denegada"}, { id:3, text:"Anul·lada"}, { id:4, text:"Pendent d'acceptar condicions"}, {id: 5, text:"Esborrada"}],
            OpcionsSiNo: [{id: 1, text: "Sí"}, {id: 0, text: "No"}],
            isFormValid: false,
            formValues: {
                RESERVAESPAIS_Nom: '',
                RESERVAESPAIS_DataActivitat: '',
                RESERVAESPAIS_HorariActivitat: '', 
                RESERVAESPAIS_Representacio: '', 
                RESERVAESPAIS_Responsable: '',
                RESERVAESPAIS_TelefonResponsable: '',
                RESERVAESPAIS_PersonalAutoritzat: '',
                RESERVAESPAIS_PrevisioAssistents: '',
                RESERVAESPAIS_EsCicle: '',
                RESERVAESPAIS_Comentaris: '',                 
                RESERVAESPAIS_Organitzadors: '',
                RESERVAESPAIS_TipusActe: '',
                RESERVAESPAIS_IsEnregistrable: '',
                RESERVAESPAIS_EspaisSolicitats: [],
                RESERVAESPAIS_HasDifusio: '', 
                RESERVAESPAIS_WebDescripcio: '',
                RESERVAESPAIS_TmpArxiuImatge: '',
                RESERVAESPAIS_TmpArxiuPdf: ''                
            },
            formErrors: {
                RESERVAESPAIS_Nom: '',
                RESERVAESPAIS_DataActivitat: '',
                RESERVAESPAIS_HorariActivitat: '', 
                RESERVAESPAIS_Representacio: '', 
                RESERVAESPAIS_Responsable: '',
                RESERVAESPAIS_TelefonResponsable: '',
                RESERVAESPAIS_PersonalAutoritzat: '',
                RESERVAESPAIS_PrevisioAssistents: '',
                RESERVAESPAIS_EsCicle: '',
                RESERVAESPAIS_Comentaris: '',                 
                RESERVAESPAIS_Organitzadors: '',
                RESERVAESPAIS_TipusActe: '',
                RESERVAESPAIS_IsEnregistrable: '',
                RESERVAESPAIS_EspaisSolicitats: '',
                RESERVAESPAIS_HasDifusio: '', 
                RESERVAESPAIS_WebDescripcio: '',
                RESERVAESPAIS_TmpArxiuImatge: '',
                RESERVAESPAIS_TmpArxiuPdf: ''                
            },
            
            EspaisDisponiblesEntitat: []
        }
    },    
    computed: {},
    watch: {},    
    methods: {
        submitHandler: function() {
            this.isFormLoading = true;
            let FD = new FormData();            
            FD.append('DadesFormulari', JSON.stringify(this.formValues));                                    
            FD.append('Accio', 'addReservaEspai');
            axios.post( CONST_api_web + '/ajaxReservaEspais', FD ).then( X => {
                // Si hi ha hagut errors, ho ensenyo.
                this.isFormLoading = false;
            }).catch( E => { alert(E); });
        },
        OnUsuariLoaded: function($UserData)  {                        
            this.IdUsuariEncrypted = $UserData.IdUsuariEncrypted;
            
            axios.get( CONST_api_web + '/ajaxReservaEspais', { 'params':  {'Accio' : 'getEspaisDisponibles', 'IdSite': this.IdSite } } ).then( X => {                
                this.isFormLoading = false;
                this.EspaisDisponiblesEntitat = X.data;                
            }).catch( E => { alert(E); });
        },
        isValidFormEspais: function($camp, $E) {
            this.formErrors[$camp] = $E;
            for(E of Object.keys(this.formErrors)) {
                if(!this.formErrors[E]) { this.isFormValid = false; return; }
            }
            this.isFormValid = true;                        
            return;            
        },

    },
    template: `            
    <div class="form-inscripcio-espai">    

        <h3>Reserva un espai</h3>
        <form-usuari-auth 
            v-if="IdUsuariEncrypted.length == 0"
            @on-id-usuari-encrypted-loaded="OnUsuariLoaded">
        </form-usuari-auth>

        <div 
            v-if="IdUsuariEncrypted.length > 0"
        >            
            <div class="row">
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_Nom'" :title = "'Títol de lactivitat'" :value = "formValues.RESERVAESPAIS_Nom" @onkeyup="formValues.RESERVAESPAIS_Nom = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Nom', $event)"
                ></form-utils>                
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_DataActivitat'" :title = "'Proposta de dates per activitat'" :value = "formValues.RESERVAESPAIS_DataActivitat" @onkeyup="formValues.RESERVAESPAIS_DataActivitat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_DataActivitat', $event)"
                ></form-utils>

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_HorariActivitat'" :title = "'Proposta d horaris per l activitat'" :value = "formValues.RESERVAESPAIS_HorariActivitat" @onkeyup="formValues.RESERVAESPAIS_HorariActivitat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_HorariActivitat', $event)"
                ></form-utils>                
                
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_Representacio'" :title = "'A quina entitat representa?'" :value = "formValues.RESERVAESPAIS_Representacio" @onkeyup="formValues.RESERVAESPAIS_Representacio = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Representacio', $event)"
                ></form-utils>

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_Responsable'" :title = "'Nom del coordinador?'" :value = "formValues.RESERVAESPAIS_Responsable" @onkeyup="formValues.RESERVAESPAIS_Responsable = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Responsable', $event)"
                ></form-utils>
                
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_TelefonResponsable'" :title = "'Telèfon del coordinador'" :value = "formValues.RESERVAESPAIS_TelefonResponsable" @onkeyup="formValues.RESERVAESPAIS_TelefonResponsable = $event" :errors = "[]" :sterrors = "['Required', 'Telefon']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_TelefonResponsable', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_PersonalAutoritzat'" :title = "'Nom i telèfon del personal de suport el dia de l activitat'" :value = "formValues.RESERVAESPAIS_PersonalAutoritzat" @onkeyup="formValues.RESERVAESPAIS_PersonalAutoritzat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_PersonalAutoritzat', $event)"
                ></form-utils>                
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_PrevisioAssistents'" :title = "'Previsió d assistents'" :value = "formValues.RESERVAESPAIS_PrevisioAssistents" @onkeyup="formValues.RESERVAESPAIS_PrevisioAssistents = $event" :errors = "[]" :sterrors = "['Required', 'Number']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_PrevisioAssistents', $event)"
                ></form-utils>                

                <form-utils 
                    :fieldtype="'multipleselect'" 
                    :id = "'RESERVAESPAIS_EspaisSolicitats'" 
                    :title = "'Espais sol·licitats'" 
                    :valuemultiple = "formValues.RESERVAESPAIS_EspaisSolicitats"                     
                    :options="EspaisDisponiblesEntitat.map(function(E) { return { 'id': E.ESPAIS_EspaiId, 'text' : E.ESPAIS_Nom } } )" 
                    :errors = "[]" 
                    :sterrors = "['Required']" 
                    :groupclass="['col-lg-6', 'col-xs-12']"
                    @onchange="formValues.RESERVAESPAIS_EspaisSolicitats = $event" 
                    @isvalid="isValidFormEspais('RESERVAESPAIS_EspaisSolicitats', $event)"
                ></form-utils>                        

                <form-utils :fieldtype="'textarea'" :id = "'RESERVAESPAIS_Comentaris'" :title = "'Breu descripció de l acte i comentaris'" :value = "formValues.RESERVAESPAIS_Comentaris" @onkeyup="formValues.RESERVAESPAIS_Comentaris = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Comentaris', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_TipusActe'" :title = "'Tipus acte'" :value = "formValues.RESERVAESPAIS_TipusActe" @onkeyup="formValues.RESERVAESPAIS_TipusActe = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_TipusActe', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_Organitzadors'" :title = "'Entitat organitzadora'" :value = "formValues.RESERVAESPAIS_Organitzadors" @onkeyup="formValues.RESERVAESPAIS_Organitzadors = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Organitzadors', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'select'" :id = "'RESERVAESPAIS_EsCicle'" :title = "'És un cicle?'" :value = "formValues.RESERVAESPAIS_EsCicle" @onchange="formValues.RESERVAESPAIS_EsCicle = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_EsCicle', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'select'" :id = "'RESERVAESPAIS_IsEnregistrable'" :title = "'Cal enregistrar-lo?'" :value = "formValues.RESERVAESPAIS_IsEnregistrable" @onchange="formValues.RESERVAESPAIS_IsEnregistrable = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_IsEnregistrable', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'select'" :id = "'RESERVAESPAIS_HasDifusio'" :title = "'Cal fer difusió?'" :value = "formValues.RESERVAESPAIS_HasDifusio" @onchange="formValues.RESERVAESPAIS_HasDifusio = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_HasDifusio', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'textarea'" :id = "'RESERVAESPAIS_WebDescripcio'" :title = "'Descripció per la web'" :value = "formValues.RESERVAESPAIS_WebDescripcio" @onkeyup="formValues.RESERVAESPAIS_WebDescripcio = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-12', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_WebDescripcio', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'image'" :id = "'RESERVAESPAIS_TmpArxiuImatge'" :title = "'Imatge per la web'" :valuefile = "formValues.RESERVAESPAIS_TmpArxiuImatge" @onchange="formValues.RESERVAESPAIS_TmpArxiuImatge = $event" :errors = "[]" :sterrors = "[]" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_TmpArxiuImatge', $event)"
                >
                </form-utils>                

                <form-utils :fieldtype="'file'" :id = "'RESERVAESPAIS_TmpArxiuPdf'" :title = "'Arxiu PDF'" :value = "formValues.RESERVAESPAIS_TmpArxiuPdf" @onchange="formValues.RESERVAESPAIS_TmpArxiuPdf = $event" :errors = "[]" :sterrors = "[]" :groupclass="['col-lg-6', 'col-xs-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_TmpArxiuPdf', $event)"
                ></form-utils>                                

                
                <form-utils 
                    :fieldtype="'button'" :id = "'BSEGUEIX'" :title = "'Demana espai'" 
                    :value = "''" :disabled = "!isFormValid"
                    :groupclass="['col-lg-2']"                    
                    @onButtonPress = "submitHandler()"
                ></form-utils>

    

            </div>


        </div>


<!--                                                                                        
                                                                                                
            <div v-if="false">
                                                
                <formulate-input type="image" name="RESERVAESPAIS_TmpArxiuImatge" label="Imatge per la web"></formulate-input>
                <formulate-input type="file" name="RESERVAESPAIS_TmpArxiuPdf" label="Arxiu Pdf" validation=""></formulate-input>                                                                                            
            </div>
               
            <formulate-input #default="{ isFormLoading }"  type="submit" :label="( isFormLoading ? 'Enviant...' : 'Fes la petició')" :disabled = "isFormLoading"></formulate-input>                                                                                                        
                
        </formulate-form>                            

        -->
                
    </div>

`
});