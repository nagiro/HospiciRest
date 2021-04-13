
Vue.component('form-inscripcio-espai', {        
    props: { 
        formdata: Object,
        estats: Array,       //Els estats que pot tenir una reserva
        espaisdisponibles: Array
    
    },          
    created: function() {
        this.formErrors = const_and_helpers_iniciaErrors(this.formErrors);        // Funció que inicia tots els errors a 0
    },
    data: function() {
        return {            
            isFormLoading: false,
            IdUsuariEncrypted: '',            
            OpcionsSiNo: [{id: 1, text: "Sí"}, {id: 0, text: "No"}],
            isFormValid: false,            
            formValues: Vue.util.extend({}, this.formdata),
            formErrors: Vue.util.extend({}, this.formdata)
        }
    },    
    computed: {},
    watch: {},    
    methods: {        
        submitFormulari: function() {
            this.isFormLoading = true;
            
            let FD = new FormData();            
            FD.append('Accio', 'addReservaEspai');            
            FD.append('DadesFormulari', JSON.stringify(this.formValues));                                                
            
            axios.post( CONST_api_web + '/ajaxReservaEspais', FD ).then( X => {
                // Si hi ha hagut errors, ho ensenyo.
                this.isFormLoading = false;
            }).catch( E => { alert(E); });
        },
        OnUsuariLoaded: function($UserData)  {                        
            this.IdUsuariEncrypted = $UserData.IdUsuariEncrypted;
            this.formValues.RESERVAESPAIS_UsuariId = this.IdUsuariEncrypted;            
        },
        isValidFormEspais: function($camp, $E) {
            this.formErrors[$camp] = $E;
            this.isFormValid = const_and_helpers_isFormValid(this.formErrors, $camp);                                    
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
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_Nom'" :title = "'Títol de lactivitat'" :value = "formValues.RESERVAESPAIS_Nom" @onkeyup="formValues.RESERVAESPAIS_Nom = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Nom', $event)"
                ></form-utils>                
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_DataActivitat'" :title = "'Proposta de dates per activitat'" :value = "formValues.RESERVAESPAIS_DataActivitat" @onkeyup="formValues.RESERVAESPAIS_DataActivitat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_DataActivitat', $event)"
                ></form-utils>

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_HorariActivitat'" :title = "'Proposta d horaris per l activitat'" :value = "formValues.RESERVAESPAIS_HorariActivitat" @onkeyup="formValues.RESERVAESPAIS_HorariActivitat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_HorariActivitat', $event)"
                ></form-utils>                
                
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_Representacio'" :title = "'A quina entitat representa?'" :value = "formValues.RESERVAESPAIS_Representacio" @onkeyup="formValues.RESERVAESPAIS_Representacio = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Representacio', $event)"
                ></form-utils>

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_Responsable'" :title = "'Nom del coordinador?'" :value = "formValues.RESERVAESPAIS_Responsable" @onkeyup="formValues.RESERVAESPAIS_Responsable = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Responsable', $event)"
                ></form-utils>
                
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_TelefonResponsable'" :title = "'Telèfon del coordinador'" :value = "formValues.RESERVAESPAIS_TelefonResponsable" @onkeyup="formValues.RESERVAESPAIS_TelefonResponsable = $event" :errors = "[]" :sterrors = "['Required', 'Telefon']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_TelefonResponsable', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_PersonalAutoritzat'" :title = "'Nom i telèfon del personal de suport el dia de l activitat'" :value = "formValues.RESERVAESPAIS_PersonalAutoritzat" @onkeyup="formValues.RESERVAESPAIS_PersonalAutoritzat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_PersonalAutoritzat', $event)"
                ></form-utils>                
                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_PrevisioAssistents'" :title = "'Previsió d assistents'" :value = "formValues.RESERVAESPAIS_PrevisioAssistents" @onkeyup="formValues.RESERVAESPAIS_PrevisioAssistents = $event" :errors = "[]" :sterrors = "['Required', 'Number']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_PrevisioAssistents', $event)"
                ></form-utils>                

                <form-utils 
                    :fieldtype="'multipleselect'" 
                    :id = "'RESERVAESPAIS_EspaisSolicitats'" 
                    :title = "'Espais sol·licitats'" 
                    :valuemultiple = "formValues.RESERVAESPAIS_EspaisSolicitats"                     
                    :options="espaisdisponibles" 
                    :errors = "[]" 
                    :sterrors = "['Required']" 
                    :groupclass="['col-lg-6', 'col-12']"
                    @onchange="formValues.RESERVAESPAIS_EspaisSolicitats = $event" 
                    @isvalid="isValidFormEspais('RESERVAESPAIS_EspaisSolicitats', $event)"
                ></form-utils>                        

                <form-utils :fieldtype="'textarea'" :id = "'RESERVAESPAIS_Comentaris'" :title = "'Breu descripció de l acte i comentaris'" :value = "formValues.RESERVAESPAIS_Comentaris" @onkeyup="formValues.RESERVAESPAIS_Comentaris = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Comentaris', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_TipusActe'" :title = "'Tipus acte'" :value = "formValues.RESERVAESPAIS_TipusActe" @onkeyup="formValues.RESERVAESPAIS_TipusActe = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_TipusActe', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'input'" :id = "'RESERVAESPAIS_Organitzadors'" :title = "'Entitat organitzadora'" :value = "formValues.RESERVAESPAIS_Organitzadors" @onkeyup="formValues.RESERVAESPAIS_Organitzadors = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Organitzadors', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'select'" :id = "'RESERVAESPAIS_EsCicle'" :title = "'És un cicle?'" :value = "formValues.RESERVAESPAIS_EsCicle" @onchange="formValues.RESERVAESPAIS_EsCicle = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_EsCicle', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'select'" :id = "'RESERVAESPAIS_IsEnregistrable'" :title = "'Cal enregistrar-lo?'" :value = "formValues.RESERVAESPAIS_IsEnregistrable" @onchange="formValues.RESERVAESPAIS_IsEnregistrable = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_IsEnregistrable', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'select'" :id = "'RESERVAESPAIS_HasDifusio'" :title = "'Cal fer difusió?'" :value = "formValues.RESERVAESPAIS_HasDifusio" @onchange="formValues.RESERVAESPAIS_HasDifusio = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_HasDifusio', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'textarea'" :id = "'RESERVAESPAIS_WebDescripcio'" :title = "'Descripció per la web'" :value = "formValues.RESERVAESPAIS_WebDescripcio" @onkeyup="formValues.RESERVAESPAIS_WebDescripcio = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-12', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_WebDescripcio', $event)"
                ></form-utils>                

                <form-utils :fieldtype="'image'" :id = "'TMP_ArxiuImatge'" :title = "'Imatge per la web'" :valuefile = "formValues.TMP_ArxiuImatge" @onchange="formValues.TMP_ArxiuImatge = $event" :errors = "[]" :sterrors = "[]" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('TMP_ArxiuImatge', $event)"
                >
                </form-utils>                

                <form-utils :fieldtype="'file'" :id = "'TMP_ArxiuPdf'" :title = "'Arxiu PDF'" :valuefile = "formValues.TMP_ArxiuPdf" @onchange="formValues.TMP_ArxiuPdf = $event" :errors = "[]" :sterrors = "[]" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('TMP_ArxiuPdf', $event)"
                ></form-utils>                                

                
                <form-utils 
                    :fieldtype="'button'" :id = "'BSEGUEIX'" :title = "'Demana espai'" 
                    :value = "''" :disabled = "!isFormValid"
                    :groupclass="['col-lg-2']"
                    @onButtonPress = "submitFormulari()"
                ></form-utils>

    

            </div>


        </div>
                
    </div>

`
});