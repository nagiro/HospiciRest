
Vue.component('form-utils', {
    props: {        
        id: String,
        title: String,
        fieldtype: String,
        value: String,
        helptext: { default: '', type: String },
        placeholder: { default: '', type: String },         
        disabled: { default: false, type: Boolean } ,
        options: { default: () => [], type: Array },        
        groupclass: { default: () => [], type: Array },
        errors: { default: () => [] , type: Array },        
        sterrors: { default: () => [] , type: Array }     //Llistat dels errors més comuns
    },          
    data: function() {
        return {
            HiHaError: false,
            Errors: []
        }
    },    
    computed: {
        elementclass: function() {
            let C = ['form-control'];            
            
            this.Errors = [];
            
            
            for(let VP of this.sterrors)  {               
                if(VP == 'Telefon' && !ValidaTelefon(this.value)) this.Errors.push("El telèfon no és correcte.");                 
                if(VP == 'Required' && this.value.length == 0) this.Errors.push("El camp és obligatori.");
                if(VP == 'Email' && !ValidaEmail(this.value)) this.Errors.push("El correu electrònic no és correcte.");                                    
            };
            
            for(let EN of this.errors)  {                               
                if(EN[0]) this.Errors.push(EN[1]);
            };

            if(this.Errors.length == 0) C.push('is-valid');
            else C.push('is-invalid');
        
            return C;
        }
    },
    watch: {},

    methods: {
        buttonPress: function() {
            this.$emit('onButtonPress', this.id);
        },
        inputChange: function($val) {
            this.$emit('onchange', $val.target.value);
        },
        inputKeyup: function($val) {            
            this.$emit('onkeyup', $val.target.value);
        }
    },
    template: `            

    <div class="FormUtils"  :class="groupclass">        

        <div v-if="fieldtype == 'input'">
            <label :for="id" class="form-label">{{title}}</label>
            <input  type="text" 
                    :class="elementclass" 
                    :placeholder="placeholder" 
                    :aria-label="title" 
                    :aria-describedby="title"
                    :id="id"
                    :value="value"
                    @change="inputChange"
                    @keyup="inputKeyup"
                    >
            <small v-for="E of Errors" class="form-text-error">{{E}}<br /></small>
            <small class="form-text text-muted">{{helptext}}</small>            
        </div>        

        <div v-if="fieldtype == 'button'">
            <label :for="id" class="form-label"></label>
            <button :disabled="disabled" class="form-control btn btn-success" :aria-label="title" :id="id" @click="buttonPress">{{title}}</button>
        </div>

        <div v-if="fieldtype == 'select'">
            <label :for="id" class="form-label">{{title}}</label>
            <select  type="select" 
                    :class="elementclass" 
                    :placeholder="placeholder" 
                    :aria-label="title" 
                    :aria-describedby="title"
                    :id="id"
                    :value="value"
                    @change="inputChange"                    
                    >
                    <option value="">-- Escull una opció --</option>
                    <option v-for="o of options" :value="o.id">{{o.text}}</option>
            </select>
            <small v-for="E of Errors" class="form-text-error">{{E}}<br /></small>
            <small class="form-text text-muted">{{helptext}}</small>            
        </div>        

        <div v-if="fieldtype == 'date'">
            <label :for="id" class="form-label">{{title}}</label>
            <input  type="date" 
                    :class="elementclass" 
                    :placeholder="placeholder" 
                    :aria-label="title" 
                    :aria-describedby="title"
                    :id="id"
                    :value="value"
                    @change="inputChange"
                    @keyup="inputKeyup"
                    >
            <small v-for="E of Errors" class="form-text-error">{{E}}<br /></small>
            <small class="form-text text-muted">{{helptext}}</small>            
        </div>        


                                                    
    </div>


`
});