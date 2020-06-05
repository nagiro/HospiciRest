
Vue.component('image-helper-cropper', {
    props: {        
        titol: String,
        UrlAMostrar: String,                
        AccioEsborra: String, //Promocio_Delete
        AccioGuarda: String, //Promocio
        IdElement: Number,  
        MidaImatge: String        
    },          
    data: function() { 
        return { 
            cropper: null, 
            objectUrl: null, 
            previewCropped: null,             
            debouncedUpdatePreview: _.debounce(this.UpdatePreview, 257),
            MostraModal: 'display: none;',                                     
            ImageData: {},
        }
    },
    created: function() {},
    computed: {        
        getUrlAMostrar: function() {
            return (this.UrlAMostrar);
        }

    },
    watch: {},
    methods: {

        MostraUrl: function() {            
            return (this.UrlAMostrar.length > 0)
        },        
        
        EsborraImatge() {
            let formData = new FormData();      
            formData.append( 'accio'    , this.AccioEsborra );            
            formData.append( 'idElement', this.IdElement );
            formData.append( 'Tipus'    , this.MidaImatge );                  
            
            this.axios.post('/apiadmin/Upload', formData )
                      .then( R => { this.MostraModal = 'display: none'; this.$emit('reload', this.IdElement); } )
                      .catch( E => { alert(E); } );              
            
          },

          GuardaImatge: function() {

            const s = {width: 161, height: 90, minWidth: 200, minHeight: 200, maxWidth: 1096, maxHeight: 1096, fillColor: '#fff', imageSmoothingEnabled: false, imageSmoothingQuality: 'high' };
            const m = {width: 161, height: 90, minWidth: 400, minHeight: 400, maxWidth: 2096, maxHeight: 2096, fillColor: '#fff', imageSmoothingEnabled: false, imageSmoothingQuality: 'high' };
            const l = {width: 161, height: 90, minWidth: 800, minHeight: 800, maxWidth: 4096, maxHeight: 4096, fillColor: '#fff', imageSmoothingEnabled: false, imageSmoothingQuality: 'high' };
            let parametresCropper = {};

            if(this.MidaImatge == 's') parametresCropper = s;
            if(this.MidaImatge == 'm') parametresCropper = m;
            if(this.MidaImatge == 'l') parametresCropper = l;

            this.cropper.getCroppedCanvas( parametresCropper ).toBlob( (blob) => {                
                let formData = new FormData();
                formData.append( 'File'     , blob              , 'imatge.png');
                formData.append( 'accio'    , this.AccioGuarda  );
                formData.append( 'Tipus'    , this.MidaImatge   );
                formData.append( 'idElement', this.IdElement    );                
    
                this.axios.post('/apiadmin/Upload', formData    )
                          .then( R => { this.MostraModal = 'display: none'; this.$emit('reload', this.IdElement); } )
                          .catch( E => { alert(E); } );      

              });

          },

          CarregaImatge(selectedFile) {    

            if(this.cropper) this.cropper.destroy();
            if(this.objectUrl) { window.URL.revokeObjectURL(this.objectUrl); }
            if(!selectedFile) { this.cropper = null; this.objectUrl = null; this.previewCropped = null; return; }
            
            let FileName = event.target.files[0].name;                  
            if (event.target.files[0]) {
                var reader = new FileReader();                        
                reader.onload = (e) => {
                  this.objectUrl = e.target.result;
                  this.MostraModal = 'display: block';
                  this.$nextTick(this.setupCropperInstance);            // Executa després d'un cicle de DOM. Primer carrego la imatge i després hi carrego el canvas
                }

                reader.readAsDataURL(event.target.files[0]);
              }                                                    
          },
          setupCropperInstance() {
            
            let AspectRatio = 1;
            if(this.MidaImatge == 's') AspectRatio = 1;
            if(this.MidaImatge == 'm') AspectRatio = 1;
            if(this.MidaImatge == 'l') AspectRatio = 16 / 9;

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
            this.previewCropped = canvas.toDataURL('image/png');
          },          
          
          TancaModalImatge: function() {
            this.MostraModal = 'display: none; ';
          }      
      
    },
    template: `    
        <div class="R">
            <div class="FT"> {{titol}} </div>
            <div class="FI"> 

                <div class="custom-file">
                                
                    <div style="height: 50px" v-if="MostraUrl()">
                        <img :src="UrlAMostrar" style="height: 50px">
                        <i @click="EsborraImatge()" class="withHand fas fa-trash-alt"></i>
                    </div>                         
                    <div v-else>
                        <input type="file" accept="image/png, image/jpeg" class="form-control" id="MidaImatge" @change="CarregaImatge($event)">                        
                        <label class="custom-file-label" for="MidaImatge" >Escull arxiu</label>                        
                    </div>        

                    <div class="modalbox" :style="MostraModal">                                                    
                        <table>
                        <tr>
                            <td>
                                <div style="width: 400px; height: 400px; border:1 px solid blue; background-color: black;">                        
                                    <img style="width: 100%; display: block;" :src="objectUrl" ref="imatge" /> 
                                </div>                                            
                            </td>
                            <td>
                                <div style="width: 400px; height: 400px; border:1 px solid blue; background-color: black;">                        
                                    <img style="width: 100%; display: block;" :src="previewCropped" /> 
                                </div>                                            
                            </td>
                        </tr>                                
                        <tr>
                            <td>
                                <button v-on:click="GuardaImatge()" class="btn btn-success">Guarda</button>
                                <button v-on:click="TancaModalImatge()" class="btn btn-info">Torna</button>                        
                            </td>
                            <td>
                                {{ImageData}}
                            </td>
                        </tr>                            
                        </table>
                    </div>  

                </div>                
            </div>
        </div>
                `,
});


