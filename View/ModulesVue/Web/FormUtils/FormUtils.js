
Vue.component('form-utils', {
    props: {        
        id: String,
        title: String,
        fieldtype: String,
        value: { default: () => "", type: String },
        valuemultiple: { default: () => [], type: Array },                
        helptext: { default: '', type: String },
        placeholder: { default: '', type: String },         
        disabled: { default: false, type: Boolean } ,
        options: { default: () => [], type: Array },        
        groupclass: { default: () => [], type: Array },
        errors: { default: () => [] , type: Array },        
        sterrors: { default: () => [] , type: Array },     //Llistat dels errors més comuns
        rows: { default: () => 10, type: Number }
    },          
    data: function() {        
        return {
            HiHaError: false,
            Errors: [],
            CheckList: [],            
            ImageLoading: false,
            ImageLoaded: false,
            ImageShow: false,
            ImageURL: "",

        }
    },        
    computed: {
        elementclass: function() {
            let C = ['form-control'];            
            
            this.Errors = [];            
            for(let VP of this.sterrors)  {               
                if(VP == 'Telefon' && !ValidaTelefon(this.value)) this.Errors.push("El telèfon no és correcte.");                                 
                if(VP == 'Email' && !ValidaEmail(this.value)) this.Errors.push("El correu electrònic no és correcte.");                                    
                if(VP == 'Number' && isNaN(this.value)) this.Errors.push("El valor ha de ser numèric.");                   

                if(VP == 'Required') {
                    if(this.fieldtype == 'multipleselect') 
                        if(!this.valuemultiple) this.Errors.push('El camp és obligatori');
                        else if(this.valuemultiple.length == 0 ) this.Errors.push("El camp és obligatori.");                    
                    
                    if(this.fieldtype !== 'multipleselect')
                        if(!this.value) this.Errors.push("El camp és obligatori.");                                                
                        else if( this.value.length == 0 ) this.Errors.push("El camp és obligatori.");                                                
                }
                
            };
            
            for(let EN of this.errors)  {                               
                if(EN[0]) this.Errors.push(EN[1]);
            };            

            if(this.Errors.length == 0) C.push('is-valid');
            else C.push('is-invalid');
        
            // Emitim si és o no vàlid            
            if(this.fieldtype !== 'button') this.$emit('isvalid', (this.Errors.length == 0) );

            return C;
        }
    },
    watch: {},

    methods: {
        buttonPress: function() {
            this.$emit('onButtonPress', this.id);
        },
        inputChange: function($val) {
            if(this.fieldtype == 'multipleselect'){                
                this.$emit('onchange', this.CheckList);
            } else {                                
                this.$emit('onchange', $val.target.value);
            }
            
        },
        inputKeyup: function($val) {            
            this.$emit('onkeyup', $val.target.value);
        },
        fileChange: function($val) {            

            const files = $val.target.files
            let filename = files[0].name
            
            const fileReader = new FileReader()
            fileReader.addEventListener('load', () => { 
                this.ImageURL = fileReader.result                
                this.ImageLoading = false;
                this.ImageLoaded = true;
                this.$emit('onchange', this.ImageURL)
            })
            this.ImageLoading = true;
            fileReader.readAsDataURL(files[0])            
        },
        ReiniciaImatge: function(val) {
            this.ImageURL = "";
            this.ImageLoaded = false;
        }
    },
    template: `            

    <div :class="[groupclass, 'FormUtils']">        

    <!-- ****************** -->
    <!-- ***** INPUT ****** -->
    <!-- ****************** -->

        <div v-if="fieldtype == 'input'" >
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

        <!-- ****************** -->
        <!-- ***** BUTTON ****** -->
        <!-- ****************** -->

        <div v-if="fieldtype == 'button'">
            <label :for="id" class="form-label"></label>
            <button :disabled="disabled" class="form-control btn btn-success" :aria-label="title" :id="id" @click="buttonPress">{{title}}</button>
        </div>

        <!-- ****************** -->
        <!-- ***** SELECT ****** -->
        <!-- ****************** -->

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

        <!-- *************************** -->
        <!-- ***** MULTIPLESELECT ****** -->
        <!-- *************************** -->

        <div v-if="fieldtype == 'multipleselect'">
            <label :for="id" :class="[elementclass, 'form-label']">{{title}}</label>
            <div v-for="o of options" class="form-check">
                <input                     
                    :class="['form-check-input']"
                    type="checkbox" 
                    :value="o.id" 
                    :id="o.id" 
                    :placeholder="placeholder" 
                    :aria-label="o.text" 
                    :aria-describedby="o.text"
                    @change="inputChange" 
                    v-model="CheckList" >
                <label class="form-check-label" for="flexCheckDefault">{{o.text}}</label>
            </div>
            <small v-for="E of Errors" class="form-text-error">{{E}}<br /></small>
            <small class="form-text text-muted">{{helptext}}</small>                                        
        </div>        


        <!-- ****************** -->
        <!-- ***** DATE ****** -->
        <!-- ****************** -->

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

        <!-- ********************* -->
        <!-- ***** TEXTAREA ****** -->
        <!-- ********************* -->

        <div v-if="fieldtype == 'textarea'">
            <label :for="id" class="form-label">{{title}}</label>
            <textarea   
                    :class="elementclass" 
                    :placeholder="placeholder" 
                    :aria-label="title" 
                    :aria-describedby="title"
                    :id="id"
                    :value="value"
                    :rows="rows"
                    @change="inputChange"
                    @keyup="inputKeyup"
                    >
            </textarea>
            <small v-for="E of Errors" class="form-text-error">{{E}}<br /></small>
            <small class="form-text text-muted">{{helptext}}</small>            
        </div>       

        <!-- ********************* -->
        <!-- ******** FILE ******* -->
        <!-- ********************* -->

        <div v-if="fieldtype == 'file' || fieldtype == 'image'">
            <label :for="id" class="form-label">{{title}}</label>            

            <div v-if="ImageLoaded"
                style="display: block; margin-top: 1vw;"
                :class="elementclass" 
            >
                
                <span v-if="ImageURL.length > 0">Arxiu carregat</span>
                <a  style="display: block; cursor: pointer" 
                    @click="ReiniciaImatge"
                    aria-label="Esborra arxiu"                    
                    >
                    Esborra
                </a>
            </div>

            <div class="form-control" v-if="ImageLoading">Carregant l'arxiu</div>

            <input  v-if="!ImageLoading && !ImageLoaded"
                    type="file" 
                    :class="elementclass" 
                    :placeholder="placeholder" 
                    :aria-label="title" 
                    :aria-describedby="title"
                    :id="id"                    
                    @change="fileChange"                                        
                    >
            <small v-for="E of Errors" class="form-text-error">{{E}}<br /></small>
            <small class="form-text text-muted">{{helptext}}</small>                                    
        </div>        


                                                    
    </div>


`
});