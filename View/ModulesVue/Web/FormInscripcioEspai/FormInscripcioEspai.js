
Vue.component('form-inscripcio-espai', {
    components: {"vue-form-generator": VueFormGenerator.component},    
    props: {    
        EspaisDisponiblesEntitat: [],
        ModelInicial: {}
    },          
    data: function() {
        return {
            model: {},
            schema: {
                fields: [
                    { type: "input", inputType: "text", label: "Nom de l'activitat", model: "RESERVAESPAIS_Nom", placeholder: "", validator: VueFormGenerator.validators.string }, 
                    { type: "input", inputType: "text", label: "Proposta de data", model: "RESERVAESPAIS_Data",  placeholder: "", validator: VueFormGenerator.validators.string }, 
                    { type: "input", inputType: "text", label: "Proposta d'hores", model: "RESERVAESPAIS_Hores", placeholder: "", validator: VueFormGenerator.validators.string }, 
                    { type: "checklist", label: "Espais", model: "RESERVAESPAIS_Espais", multi: true, required: true, multiSelect: true, values: this.EspaisDisponiblesEntitat.map(function(E) { return E.ESPAIS_Nom } ) }, 
                    { type: "textArea", label: "Breu descripció", model: "bio", hint: "Max 500 characters", max: 500, placeholder: "User's biography", rows: 4, validator: VueFormGenerator.validators.string },
                    { type: "select", label: "Espais", model: "RESERVAESPAIS_Enregistrable", values: ["Sí", "No"] }

                    
                ]  
            },
            formOptions: {
                validateAfterLoad: true,
                validateAfterChanged: true
            }
        }
    },    
    computed: {
    },
    watch: {              
    },    
    methods: {        
    },
    template: `            

    <div>
        <form>

            <vue-form-generator :schema="schema" :model="model" :options="formOptions"></vue-form-generator>

            <div class="form-group">
                <button name="submit" type="submit" class="btn btn-primary">Demana la reserva!</button>
            </div>
        
        </form>
        
    </div>

`
});