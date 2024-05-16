
Vue.component('form-inscripcio-espai', {        
    props: { 
        formdata: Object,
        estats: Array,       //Els estats que pot tenir una reserva
        espaisdisponibles: Array,
        tipusactivitatsdisponibles: Array,
        espaiescollit: Number,            
        formularicampsvisibles: Object,
    },          
    created: function() {
        // Trec els camps que entro i que no són visibles
        // FORMULARI CAMPS VISIBLES ES CARREGA DE LES OPCIONS DE LA INTRNAET
        for(let i in this.formErrors) {        
            if(this.formularicampsvisibles[i] == 0) delete this.formErrors[i];
        }                
        this.formErrors = const_and_helpers_iniciaErrors(this.formErrors);        // Funció que inicia tots els errors a 0        
        this.formdata.RESERVAESPAIS_EspaisSolicitats = [this.espaiescollit];        
        this.formValues.RESERVAESPAIS_EspaisSolicitats = [this.espaiescollit];        
        Vue.set(this.formValues, "RESERVAESPAIS_HorariActivitat2", '');
        Vue.set(this.formErrors, 'RESERVAESPAIS_HorariActivitat2', true);        
        
    },
    data: function() {
        return {            
            isFormLoading: false,
            IdUsuariEncrypted: '',            
            OpcionsSiNo: [{id: 1, text: "Sí"}, {id: 0, text: "No"}],
            isFormValid: false,            
            formValues: Vue.util.extend({}, this.formdata),
            formErrors: Vue.util.extend({}, this.formdata),
            Pas: 0,            
        }
    },    
    computed: {},
    watch: {},    
    methods: {        
        submitFormulari: function() {
            this.isFormLoading = true;

            if(this.formValues['RESERVAESPAIS_TipusActe'].length == 0) this.formValues['RESERVAESPAIS_TipusActe'] = 'Activitat';
            if(this.formularicampsvisibles.RESERVAESPAIS_HorariActivitat == 2) {                
                this.formValues.RESERVAESPAIS_HorariActivitat = 'Inici: ' + this.formValues.RESERVAESPAIS_HorariActivitat + ' / Fi: ' +  this.formValues.RESERVAESPAIS_HorariActivitat2;
                delete this.formValues.RESERVAESPAIS_HorariActivitat2;
            }
            
            let FD = new FormData();            
            FD.append('Accio', 'addReservaEspai');            
            FD.append('DadesFormulari', JSON.stringify(this.formValues));
            
            axios.post( CONST_api_web + '/ajaxReservaEspais', FD ).then( X => {
                // Si hi ha hagut errors, ho ensenyo.
                this.formValues = Vue.util.extend({}, X.data.FormulariReservaComplet);
                this.formErrors = Vue.util.extend({}, X.data.FormulariReservaComplet);
                debugger
                // Separem HorariEspai2 en cas que sigui necessari
                if(this.formularicampsvisibles.RESERVAESPAIS_HorariActivitat == 2)  {
                    const regex = /Inici:\s*(\d{2}:\d{2})\s*\/\s*Fi:\s*(\d{2}:\d{2})/;
                    const match = this.formValues.RESERVAESPAIS_HorariActivitat.match(regex);
                    Vue.set(this.formValues, "RESERVAESPAIS_HorariActivitat", match[1] )
                    Vue.set(this.formValues, "RESERVAESPAIS_HorariActivitat2", match[2]);
                    Vue.set(this.formErrors, "RESERVAESPAIS_HorariActivitat2", false );
                }

                this.Pas = 2;
            }).catch( E => { alert(E); });
        },
        OnUsuariLoaded: function($UserData)  {                        
            this.IdUsuariEncrypted = $UserData.IdUsuariEncrypted;            
            this.formValues.RESERVAESPAIS_UsuariId = this.IdUsuariEncrypted;            
            this.Pas = 1;
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
            v-if="Pas == 0"
            @on-id-usuari-encrypted-loaded="OnUsuariLoaded">
        </form-usuari-auth>

        <div v-if="Pas == 1 || Pas == 2">            
        
            <div class="row">
                
                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_Nom == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_Nom'" :title = "'Títol de l\\'activitat'" :value = "formValues.RESERVAESPAIS_Nom" @onkeyup="formValues.RESERVAESPAIS_Nom = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Nom', $event)"
                ></form-utils>                
                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_DataActivitat == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_DataActivitat'" :title = "'Proposta de dates per l\\'activitat'" :value = "formValues.RESERVAESPAIS_DataActivitat" @onkeyup="formValues.RESERVAESPAIS_DataActivitat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_DataActivitat', $event)"
                ></form-utils>
                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_DataActivitat == 2" :fieldtype="'date'" :id = "'RESERVAESPAIS_DataActivitat'" :title = "'Proposta de data per l\\'activitat'" :value = "formValues.RESERVAESPAIS_DataActivitat" @onkeyup="formValues.RESERVAESPAIS_DataActivitat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_DataActivitat', $event)"
                ></form-utils>

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_HorariActivitat == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_HorariActivitat'" :title = "'Proposta d\\'horaris per l\\'activitat'" :value = "formValues.RESERVAESPAIS_HorariActivitat" @onkeyup="formValues.RESERVAESPAIS_HorariActivitat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_HorariActivitat', $event)"
                ></form-utils>                

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_HorariActivitat == 2" :fieldtype="'time'" :id = "'RESERVAESPAIS_HorariActivitat'" :title = "'Proposta d\\'hora d\\'inici'" :value = "formValues.RESERVAESPAIS_HorariActivitat" @onkeyup="formValues.RESERVAESPAIS_HorariActivitat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-3', 'col-6']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_HorariActivitat', $event)"
                ></form-utils>                
                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_HorariActivitat == 2" :fieldtype="'time'" :id = "'RESERVAESPAIS_HorariActivitat2'" :title = "'Proposta d\\'hora de finalització'" :value = "formValues.RESERVAESPAIS_HorariActivitat2" @onkeyup="formValues.RESERVAESPAIS_HorariActivitat2 = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-3', 'col-6']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_HorariActivitat2', $event)"
                ></form-utils>                

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_Representacio == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_Representacio'" :title = "'A quina entitat representa?'" :value = "formValues.RESERVAESPAIS_Representacio" @onkeyup="formValues.RESERVAESPAIS_Representacio = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Representacio', $event)"
                ></form-utils>

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_Responsable == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_Responsable'" :title = "'Nom del coordinador?'" :value = "formValues.RESERVAESPAIS_Responsable" @onkeyup="formValues.RESERVAESPAIS_Responsable = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Responsable', $event)"
                ></form-utils>
                
                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_TelefonResponsable == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_TelefonResponsable'" :title = "'Telèfon del coordinador'" :value = "formValues.RESERVAESPAIS_TelefonResponsable" @onkeyup="formValues.RESERVAESPAIS_TelefonResponsable = $event" :errors = "[]" :sterrors = "['Required', 'Telefon']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_TelefonResponsable', $event)"
                ></form-utils>                

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_PersonalAutoritzat == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_PersonalAutoritzat'" :title = "'Nom i telèfon del personal de suport el dia de l activitat'" :value = "formValues.RESERVAESPAIS_PersonalAutoritzat" @onkeyup="formValues.RESERVAESPAIS_PersonalAutoritzat = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_PersonalAutoritzat', $event)"
                ></form-utils>                
                
                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_PrevisioAssistents == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_PrevisioAssistents'" :title = "'Previsió d\\'assistents'" :value = "formValues.RESERVAESPAIS_PrevisioAssistents" @onkeyup="formValues.RESERVAESPAIS_PrevisioAssistents = $event" :errors = "[]" :sterrors = "['Required', 'Number']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_PrevisioAssistents', $event)"
                ></form-utils>                                
                
                <form-utils 
                    v-if="formularicampsvisibles.RESERVAESPAIS_EspaisSolicitats == 1"
                    :fieldtype="'multipleselect'" 
                    :id = "'RESERVAESPAIS_EspaisSolicitats'" 
                    :title = "'Espais sol·licitats'" 
                    :valorinicial = "[espaiescollit]"                                         
                    :options="espaisdisponibles" 
                    :errors = "[]" 
                    :sterrors = "['Required']" 
                    :groupclass="['col-lg-6', 'col-12']"
                    @onchange="formValues.RESERVAESPAIS_EspaisSolicitats = $event" 
                    @isvalid="isValidFormEspais('RESERVAESPAIS_EspaisSolicitats', $event)"
                ></form-utils>                        

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_Comentaris == 1" :fieldtype="'textarea'" :id = "'RESERVAESPAIS_Comentaris'" :title = "'Breu descripció de l\\'acte, material necessari i comentaris'" :value = "formValues.RESERVAESPAIS_Comentaris" @onkeyup="formValues.RESERVAESPAIS_Comentaris = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Comentaris', $event)"
                ></form-utils>                
                
                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_TipusActe == 1" :fieldtype="'select'" :id = "'RESERVAESPAIS_TipusActe'" :title = "'Tipus d\\'acte'" :value = "formValues.RESERVAESPAIS_TipusActe" @onchange="formValues.RESERVAESPAIS_TipusActe = $event" :options="tipusactivitatsdisponibles" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_EsCicle', $event)"
                ></form-utils>            

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_Organitzadors == 1" :fieldtype="'input'" :id = "'RESERVAESPAIS_Organitzadors'" :title = "'Entitat organitzadora'" :value = "formValues.RESERVAESPAIS_Organitzadors" @onkeyup="formValues.RESERVAESPAIS_Organitzadors = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_Organitzadors', $event)"
                ></form-utils>                
                
                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_EsCicle == 1" :fieldtype="'select'" :id = "'RESERVAESPAIS_EsCicle'" :title = "'És un cicle?'" :value = "formValues.RESERVAESPAIS_EsCicle" @onchange="formValues.RESERVAESPAIS_EsCicle = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_EsCicle', $event)"
                ></form-utils>                

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_IsEnregistrable == 1" :fieldtype="'select'" :id = "'RESERVAESPAIS_IsEnregistrable'" :title = "'Cal enregistrar-lo?'" :value = "formValues.RESERVAESPAIS_IsEnregistrable" @onchange="formValues.RESERVAESPAIS_IsEnregistrable = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_IsEnregistrable', $event)"
                ></form-utils>                

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_HasDifusio == 1" :fieldtype="'select'" :id = "'RESERVAESPAIS_HasDifusio'" :title = "'Cal fer difusió?'" :value = "formValues.RESERVAESPAIS_HasDifusio" @onchange="formValues.RESERVAESPAIS_HasDifusio = $event" :options="OpcionsSiNo" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-4', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_HasDifusio', $event)"
                ></form-utils>                

                <form-utils v-if="formularicampsvisibles.RESERVAESPAIS_WebDescripcio == 1" :fieldtype="'textarea'" :id = "'RESERVAESPAIS_WebDescripcio'" :title = "'Descripció per la web'" :value = "formValues.RESERVAESPAIS_WebDescripcio" @onkeyup="formValues.RESERVAESPAIS_WebDescripcio = $event" :errors = "[]" :sterrors = "['Required']" :groupclass="['col-lg-12', 'col-12']"
                @isvalid="isValidFormEspais('RESERVAESPAIS_WebDescripcio', $event)"
                ></form-utils>                

                <form-utils v-if="formularicampsvisibles.TMP_ArxiuImatge == 1" :fieldtype="'crop'" :id = "'TMP_ArxiuImatge'" :title = "'Imatge per la web'" :valuefile = "formValues.TMP_ArxiuImatge" @onchange="formValues.TMP_ArxiuImatge = $event" :errors = "[]" :sterrors = "[]" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('TMP_ArxiuImatge', $event)"
                >
                </form-utils>                

                <form-utils v-if="formularicampsvisibles.TMP_ArxiuPdf == 1" :fieldtype="'file'" :id = "'TMP_ArxiuPdf'" :title = "'Arxiu PDF'" :valuefile = "formValues.TMP_ArxiuPdf" @onchange="formValues.TMP_ArxiuPdf = $event" :errors = "[]" :sterrors = "[]" :groupclass="['col-lg-6', 'col-12']"
                @isvalid="isValidFormEspais('TMP_ArxiuPdf', $event)"
                ></form-utils>                                

                <div v-if="isFormLoading && Pas == 1" class="alert alert-info">
                    Espereu un moment mentre guardem la petició d'espai.
                </div>
                
                <form-utils 
                    v-if="!isFormLoading && Pas == 1"
                    :fieldtype="'button'" :id = "'BSEGUEIX'" :title = "'Demana espai'" 
                    :value = "''" :disabled = "!isFormValid"
                    :groupclass="['col-lg-2']"
                    @onButtonPress = "submitFormulari()"
                ></form-utils>

                <div v-if="Pas == 2" class="alert alert-success">
                    La reserva amb codi <strong>{{formValues.RESERVAESPAIS_Codi}}</strong> s'ha efectuat correctament. 
                    Ens posarem en contacte amb vostè properament per comunicar-li les condicions i si és possible o no concedir-li l'espai.
                </div>
    
            </div>        

        </div>
                        
    </div>

`
});