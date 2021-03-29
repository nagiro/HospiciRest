
Vue.component('form-utils', {
    props: {        
        id: String,
        title: String,
        fieldtype: String,
        value: String,
        helptext: { default: '', type: String },
        placeholder: { default: '', type: String }, 
        required: { default: false, type: Boolean },
        disabled: { default: false, type: Boolean } ,
        options: { default: () => [], type: Array },        
        groupclass: { default: () => [], type: Array },
        errornumber: { default: -1, type: Number },
        errortexts: { default: () => [] , type: Array },
    },          
    data: function() {
        return {}
    },    
    computed: {
        elementclass: function() {
            let C = ['form-control'];            
            if(this.value.length > 0 ){
                if(this.errornumber == -1) C.push('is-valid');
                else C.push('is-invalid');
            }
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

    <div class="FormUtils">        

        <div v-if="fieldtype == 'input'" :class="groupclass">
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
            <small v-if="errornumber > -1" class="form-text-error">{{errortexts[errornumber]}}</small>
            <small class="form-text text-muted">{{helptext}}</small>            
        </div>        
        <div v-if="fieldtype == 'button'" :class="groupclass">
            <label :for="id" class="form-label"></label>
            <button :disabled="disabled" class="form-control btn btn-success" :aria-label="title" :id="id">Valida</button>
        </div>

                                                    
    </div>

`
});