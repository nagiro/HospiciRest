
Vue.component('form-inscripcio-espai', {        
    props: { 
        IdSite: Number,           
        ModelInicial: {},
        ModelOmplert: {
            RESERVAESPAIS_Estat: 1
        }        

    },          
    created: function() {},
    data: function() {
        return {            
            isFormLoading: false,
            IdUsuariEncrypted: '',
            OpcionsEstat: {0: "En espera", 1: "Acceptada", 2:"Denegada", 3:"Anul·lada", 4:"Pendent d'acceptar condicions", 5:"Esborrada"},
            OpcionsSiNo: {1: "Sí", 0: "No"},
            formValues: {},
            EspaisDisponiblesEntitat: []
        }
    },    
    computed: {},
    watch: {},    
    methods: {
        submitHandler: function(data) {
            this.isFormLoading = true;
            let FD = new FormData();            
            FD.append('DadesFormulari', JSON.stringify(data));                                    
            axios.post( CONST_api_web + '/PutNovaReservaEspai', FD ).then( X => {
                // Si hi ha hagut errors, ho ensenyo.
                this.isFormLoading = false;
            }).catch( E => { alert(E); });
        },
        OnUsuariLoaded: function($UserData)  {                        
            this.IdUsuariEncrypted = $UserData.IdUsuariEncrypted;
            
            axios.get( CONST_api_web + '/ajaxReservaEspais', { 'params':  {'Accio' : 'getEspaisDisponibles', 'IdSite': this.IdSite } } ).then( X => {                
                this.isFormLoading = false;
                this.EspaisDisponiblesEntitat = X.data;
                console.log(X);
            }).catch( E => { alert(E); });
        }
    },
    template: `            
    <div>

        <form-usuari-auth 
            v-if="IdUsuariEncrypted.length == 0"
            @on-id-usuari-encrypted-loaded="OnUsuariLoaded">
        </form-usuari-auth>
 
        <formulate-form            
            v-if="IdUsuariEncrypted.length > 0"
            v-model="formValues"        
            @submit="submitHandler"             
        >        
        
            <h3>Reserva un espai</h3>
    <!--        
            <div v-if="false">
                <formulate-input type="text" name="RESERVAESPAIS_Nom" label="Títol de l'activitat" validation="required"></formulate-input>
                <formulate-input type="text" name="RESERVAESPAIS_DataActivitat" label="Proposta de dates de l'activitat" validation="required"></formulate-input>
                <formulate-input type="text" name="RESERVAESPAIS_HorariActivitat" label="Proposta d'horaris de l'activitat" validation="required"></formulate-input>
                <formulate-input type="text" name="RESERVAESPAIS_Representacio" label="A quina entitat representa?" validation="required"></formulate-input>
                <formulate-input type="text" name="RESERVAESPAIS_Responsable" label="Nom del coordinador?" validation="required"></formulate-input>
                <formulate-input type="tel" name="RESERVAESPAIS_TelefonResponsable" label="Telèfon del coordinador?" validation="required"></formulate-input>
                <formulate-input type="text" name="RESERVAESPAIS_PersonalAutoritzat" label="Nom i telèfon del personal de suport el dia de l'activitat" validation="required"></formulate-input>
                <formulate-input type="number" name="RESERVAESPAIS_PrevisioAssistents" label="Previsió d'assistents" validation="required"></formulate-input>
                <formulate-input type="select" name="RESERVAESPAIS_EsCicle" label="És un cicle?" validation="required" :options = "OpcionsSiNo" placeholder = "-- Escull una opció -- "></formulate-input>
                <formulate-input type="textarea" name="RESERVAESPAIS_Comentaris" label="Breu descripció de l'acte i comentaris" validation="required"></formulate-input>
                <formulate-input type="select" name="RESERVAESPAIS_Estat" label="Estat actual" validation="required" :options = "OpcionsEstat" placeholder = "-- Escull una opció -- "></formulate-input>
                <formulate-input type="text" name="RESERVAESPAIS_Organitzadors" label="Entitat organitzadora" validation="required"></formulate-input>
                <formulate-input type="text" name="RESERVAESPAIS_TipusActe" label="Tipus d'acte" validation="required"></formulate-input>
                <formulate-input type="select" name="RESERVAESPAIS_IsEnregistrable" label="Cal enregistrar-lo?" validation="required" :options = "OpcionsSiNo" placeholder = "-- Escull una opció -- "></formulate-input>
            </div>
    -->
            <div v-if="true">
                <formulate-input type="checkbox" name="RESERVAESPAIS_EspaisSolicitats" label="Quins espais vols demanar?" validation="required" :options = "EspaisDisponiblesEntitat.map(function(E) { return { 'value': E.ESPAIS_EspaiId, 'label' : E.ESPAIS_Nom } } )" placeholder = "-- Escull una opció -- "></formulate-input>
                <formulate-input type="checkbox" name="RESERVAESPAIS_MaterialSolicitat" label="Material sol·licitat" validation="" :options = "{}" placeholder = "-- Escull una opció -- "></formulate-input>                    
            </div>
    <!--
            <div v-if="false">
                <formulate-input type="textarea" name="RESERVAESPAIS_Compromis" label="Compromís" validation=""></formulate-input>
                <formulate-input type="textarea" name="RESERVAESPAIS_Condicions" label="Condicions" validation="required"></formulate-input>
                <formulate-input type="select" name="RESERVAESPAIS_HasDifusio" label="Cal fer difusió?" validation="required" :options = "OpcionsSiNo" placeholder = "-- Escull una opció -- "></formulate-input>        
                <formulate-input type="textarea" name="RESERVAESPAIS_WebDescripcio" label="Descripció per la web" validation=""></formulate-input>
                <formulate-input type="image" name="RESERVAESPAIS_TmpArxiuImatge" label="Imatge per la web"></formulate-input>
                <formulate-input type="file" name="RESERVAESPAIS_TmpArxiuPdf" label="Arxiu Pdf" validation=""></formulate-input>                                                                                            
            </div>
    -->

            <FormulateErrors />

            <formulate-input #default="{ isFormLoading }"  type="submit" :label="( isFormLoading ? 'Enviant...' : 'Fes la petició')" :disabled = "isFormLoading"></formulate-input>                                                                                                        
                
        </formulate-form>                            
                
    </div>

`
});