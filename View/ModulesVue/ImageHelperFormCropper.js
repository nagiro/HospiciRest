
Vue.component('image-helper-cropper', {
    props: {        
        titol: String,
        UrlAMostrar: String,                
        AccioEsborra: String, //Promocio_Delete
        AccioGuarda: String, //Promocio
        IdElement: Number,  
        MidaImatge: String        
    },          
    data: function() { return { image: null, MostraModal: false, extension: '', img: null, stencilProps: {} }},
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
                      .then( R => { this.MostraModal = false; this.$emit('reload', this.IdElement); } )
                      .catch( E => { alert(E); } );              
            
          },

          GuardaImatge: function() {
            let formData = new FormData();
            formData.append( 'File'     , this.image        );
            formData.append( 'accio'    , this.AccioGuarda  );
            formData.append( 'Tipus'    , this.MidaImatge   );
            formData.append( 'idElement', this.IdElement    );
            formData.append( 'extensio' , this.extension    );            

            this.axios.post('/apiadmin/Upload', formData    )
                      .then( R => { this.MostraModal = false; this.$emit('reload', this.IdElement); } )
                      .catch( E => { alert(E); } );      

          },

          pixelsRestriction({minWidth, minHeight, maxWidth, maxHeight, imageWidth, imageHeight}) {
            return {
				minWidth: minWidth,
				minHeight: minHeight,
				maxWidth: maxWidth,
                maxHeight: maxHeight,
                imageWidth: imageWidth,
                imageHeight: imageHeight
			}
          },
          inputNouArxiu(event, mida) {            
            var FileName = event.target.files[0].name;      
            if (event.target.files[0]) {
              var reader = new FileReader();        
              this.extension = FileName.substr(FileName.lastIndexOf('.') + 1);
              reader.onload = (e) => {
                this.img = e.target.result;                                    
                switch(this.MidaImatge) {
                  case 's': this.stencilProps = { aspectRatio: 16/9 }; break;
                  case 'm': this.stencilProps = { aspectRatio: 4/3 }; break;
                  case 'l': this.stencilProps = { aspectRatio: 16/9 }; break;
                }
                this.MostraModal = true;
              }
              reader.readAsDataURL(event.target.files[0]);
            }      
          },

          change({coordinates, canvas}) {
                this.coordinates = coordinates;
                this.image = canvas.toDataURL();            
          },      
          
          TancaModalImatge: function() {
            this.MostraModal = false;
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
                        <input type="file" class="form-control" id="MidaImatge" @change="inputNouArxiu($event)" >
                        <label class="custom-file-label" for="MidaImatge" >Escull arxiu</label>
                    </div>        

                    <div class="modalbox" v-if="MostraModal">    
                        <table>
                        <tr>
                            <td><div style="width: 400px; height: 400px;">  <cropper :src="img" :restrictions="pixelsRestrictions" :maxHeight="400" :stencil-props="stencilProps" @change="change"></cropper></div></td>
                            <td><div style="width: 400px; height: 400px;">  <img style="width: 100%" :src="image" /> </div> </td>
                        </tr>
                        <tr>
                            <td>
                            <button v-on:click="GuardaImatge()" class="btn btn-success">Guarda</button>
                            <button v-on:click="TancaModalImatge()" class="btn btn-info">Torna</button>
                            </td>
                            <td></td>
                    
                        </tr>
                        </table>            
                    </div>  

                </div>                
            </div>
        </div>
                `,
});


