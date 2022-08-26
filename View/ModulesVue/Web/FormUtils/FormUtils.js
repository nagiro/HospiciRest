
Vue.component('form-utils', {
    props: {        
        id: String,
        title: String,
        fieldtype: String,
        value: { default: () => "", type: String },         // Quan és un valor únic i no objecte
        valorinicial: { default: () => [], type: Array },   // Sempre usem aquest si és un valor múltiple
        valuefile: { default: () => {}, type: Object},      // Sempre usem aquest si és un arxiu que carreguem
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
            checklist: this.valorinicial,
            HiHaError: false,
            Errors: [],            
            ImageLoading: false,
            ImageLoaded: false,
            ImageShow: false,
            ImageURL: {'url': '', 'hexfile': '', 'name': ''},
            ImageName: "",

            /* Variables per al cropping */
            cropper: null, 
            objectUrl: null,            
            MostraModal: false, 
            ImageData: {},
            debouncedUpdatePreview: _.debounce(this.UpdatePreview, 257)

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
                        if(!this.checklist) this.Errors.push('El camp és obligatori');
                        else if(this.checklist.length == 0 ) this.Errors.push("El camp és obligatori.");                    
                    
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
                this.$emit('onchange', this.checklist);
            } else {                                
                this.$emit('onchange', $val.target.value);
            }
            
        },
        inputKeyup: function($val) {            
            this.$emit('onkeyup', $val.target.value);
        },
        fileChange: function($val) {            

            const files = $val.target.files            
            const fileReader = new FileReader();
            
            this.ImageName = files[0].name

            fileReader.addEventListener('load', () => {                 
                this.ImageURL = fileReader.result                
                this.ImageLoading = false;
                this.ImageLoaded = true;
                this.$emit('onchange', {'url': '', 'hexfile':this.ImageURL, 'name': this.ImageName})
            })
            this.ImageLoading = true;
            fileReader.readAsDataURL(files[0])            
        },
        ReiniciaImatge: function(val) {
            this.ImageURL = "";
            this.ImageLoaded = false;
        },
        CarregaImatge: function(selectedFile) {
            if(this.cropper) this.cropper.destroy();
            if(this.objectUrl) { window.URL.revokeObjectURL(this.objectUrl); }
            if(!selectedFile) { this.cropper = null; this.objectUrl = null; this.ImageURL = null; return; }
            
            let File = selectedFile.target.files[0];            
            this.ImageName = File.name;
            if (File) {
                var reader = new FileReader();                        
                reader.onload = (e) => {
                this.objectUrl = e.target.result;                
                this.MostraModal = true;
                this.$nextTick(this.setupCropperInstance);            // Executa després d'un cicle de DOM. Primer carrego la imatge i després hi carrego el canvas
                }

                reader.readAsDataURL(File);
            }                                         
        },
        setupCropperInstance() {            
            let AspectRatio = 1;            
            this.cropper = new Cropper(this.$refs.imatge, 
                { 
                    aspectRatio: AspectRatio, 
                    crop: this.debouncedUpdatePreview 
                }
            ); 
        },

        UpdatePreview(event) {                     
            this.ImageData = event.detail;
            const canvas = this.cropper.getCroppedCanvas();            
            this.ImageURL = canvas.toDataURL('image/png');                                     
        },          

        TancaModalImatge: function() {
            this.MostraModal = false;
        },
        SaveCropImatge: function() {

            const s = {width: 161, height: 90, minWidth: 200, minHeight: 200, maxWidth: 1096, maxHeight: 1096, fillColor: '#fff', imageSmoothingEnabled: false, imageSmoothingQuality: 'high' };
            const m = {width: 161, height: 90, minWidth: 400, minHeight: 400, maxWidth: 2096, maxHeight: 2096, fillColor: '#fff', imageSmoothingEnabled: false, imageSmoothingQuality: 'high' };
            const l = {width: 161, height: 90, minWidth: 400, minHeight: 400, maxWidth: 4096, maxHeight: 4096, fillColor: '#fff', imageSmoothingEnabled: false, imageSmoothingQuality: 'high' };
            let parametresCropper = {};
            parametresCropper = 'm';            
            this.MostraModal = false;
            this.ImageLoaded = true;
            this.$emit('onchange', {'url': '', 'hexfile':this.ImageURL, 'name': this.ImageName})            
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
                    v-model="checklist" >
                <label class="form-check-label" for="flexCheckDefault">{{o.text}}</label>
            </div>
            <small v-for="E of Errors" class="form-text-error">{{E}}<br /></small>
            <small class="form-text text-muted">{{helptext}}</small>                                                   
            {{checklist}}
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
            <label :for="id" v-if="ImageLoaded" class="form-label">{{title}}</label>            
            <label :for="id" v-if="!ImageLoaded" class="custom-file-label">{{title}}</label>            

            <div v-if="ImageLoaded" style="display: block; margin-top: 0vw;">
                
                <div class="alert alert-success" style="margin-top: 1vw;" role="alert" v-if="ImageLoaded">
                    Arxiu carregat correctament.    
                </div>    
                <a  style="display: block; cursor: pointer" @click="ReiniciaImatge" aria-label="Esborra arxiu"> Esborra </a>
            </div>            

            <div class="form-control" v-if="ImageLoading">Carregant l'arxiu</div>            
            <input  v-if="!ImageLoading && !ImageLoaded"
                    type="file" 
                    accept="document/pdf, document/word"
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


        <!-- ********************* -->
        <!-- ****** CROPPED ****** -->
        <!-- ********************* -->

        <div v-if="fieldtype == 'crop'">
            <div style="height: 50px" v-if="ImageLoaded">
                <img :src="ImageURL" style="height: 50px">
                <i @click="ReiniciaImatge()" class="withHand fas fa-trash-alt"></i>                
            </div>                         
            <div v-else>
                <input type="file" accept="image/png, image/jpeg" class="form-control" id="MidaImatge" @change="CarregaImatge($event)" >
                <label class="custom-file-label" for="MidaImatge" >{{title}}</label>  
                <small v-for="E of Errors" class="form-text-error">{{E}}<br /></small>
                <small class="form-text text-muted">{{helptext}}</small>                                    
            </div>        

            <div class="modalbox" v-if="MostraModal">
                <table>
                <tr><td><div style="border:1 px solid blue; background-color: black;">                        
                            <img style="display: block;" :src="objectUrl" ref="imatge" /> 
                        </div>                                            
                    </td><td>
                        <div style="border:1 px solid blue; background-color: black;">                        
                            <img style="display: block;" :src="ImageURL" /> 
                        </div>                                            
                    </td>
                </tr><tr>
                    <td>
                        <button v-on:click="TancaModalImatge()" class="btn btn-info">Torna</button>
                        <button v-on:click="SaveCropImatge()" class="btn btn-success">Retalla-la!</button>
                    </td>
                    <td> &nbsp;</td>
                </tr>                            
                </table>
            </div>  
        </div>





              
    </div>


`
});