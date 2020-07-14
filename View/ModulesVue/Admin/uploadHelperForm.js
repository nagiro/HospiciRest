// Component basat en --> https://fengyuanchen.github.io/cropperjs/
// Seguint instruccions de --> https://lobotuerto.com/blog/cropping-images-with-vuejs-and-cropperjs/
// Usant llibreria javascript --> Lodash (https://lodash.com/)

// const { stringify } = require("querystring");

Vue.component('upload-helper', {
    props: {        
        titol: String,
        UrlAMostrar: String,                
        AccioEsborra: String, //Promocio_Delete
        AccioGuarda: String, //Promocio
        IdElement: Number        

    },          
    data: function() { 
        return {
            MostraUrl: ""
        }
    },
    created: function() {
        this.MostraUrl = this.UrlAMostrar;
    },
    computed: {},
    watch: {},
    methods: {

        ReloadImatge() {
            this.$forceUpdate();
        },

        EsborraArxiu() {
            let formData = new FormData();      
            formData.append( 'accio'    , this.AccioEsborra );            
            formData.append( 'idElement', this.IdElement );            
            
            this.axios.post('/apiadmin/Upload', formData )
                      .then( R => { this.MostraModal = 'display: none'; 
                      this.MostraUrl = '';
                    } ).catch( E => { alert(E); } );                          
          },

          GuardaImatge: function(File) {

            let formData = new FormData();
            formData.append( 'File'     , File);
            formData.append( 'accio'    , this.AccioGuarda  );
            formData.append( 'Tipus'    , 'PDF'   );
            formData.append( 'idElement', this.IdElement    );                

            this.axios.post('/apiadmin/Upload', formData    )
                        .then( R => { 
                            this.MostraModal = 'display: none'; 
                            this.$emit('update', R.data.Filename);
                            if(R.data.Url) this.MostraUrl = R.data.Url;                        
                            else this.MostraUrl = '';
                        } ).catch( E => { alert(E); } );      

          },

          CarregaArxiu(Event) {    
            
            let File = Event.target.files;            
            this.GuardaImatge(File[0]);                        
          }      
    },
    template: `    
        <div class="R">
            <div class="FT"> {{titol}} </div>
            <div class="FI"> 

                <div class="custom-file">
                                
                    <div style="height: 50px" v-if="MostraUrl.length > 0">
                        [<a target="_NEW" :href="MostraUrl">Baixa el pdf</a>]
                        <i @click="EsborraArxiu()" class="withHand fas fa-trash-alt"></i>
                    </div>                         
                    <div v-else>
                        <input type="file" accept="application/pdf" class="form-control" id="file" @change="CarregaArxiu($event)">                        
                        <label class="custom-file-label" for="file" >Escull arxiu</label>                        
                    </div>                    

                </div>                
            </div>
        </div>
                `,
});


