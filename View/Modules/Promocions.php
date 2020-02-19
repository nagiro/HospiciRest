
<div id="promocionsComponent" style="margin-bottom: 30px;" class="col-10">

  <div class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Cercador</h5></div>  
    <div class="card-body">    
    
      <div class="row">
        
        <?php echo InputHelper('textCercador',  8,"Paraules", "cercador", "Paraules a buscar...", false); ?>
        <?php echo SelectHelper('tipusCercador', 2, "Tipus", 'OptionsActiuNoActiu', false); ?>        
        <?php echo ButtonHelper('BotoCerca', 2, "Cerca!", 'btn-success'); ?>        
        
      </div>    
      
    </div>
  </div>


  <div v-if="!Editant" class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><?php echo TitleWithAdd('AddPromocio()', 'Llistat de promocions') ?></div>    
    <div class="card-body">      
      <table class="table table-hover table-sm">
        <thead>
          <tr>
            <th scope="col">Tipus</th>
            <th scope="col">Accions</th>            
          </tr>
        </thead>
        <tbody>
          <tr v-for="(P, index) in Promocions" >
            <td class="withHand" @click="EditaPromocio(P.PROMOCIONS_PROMOCIO_ID)"> {{ P.PROMOCIONS_NOM }} </td>            
            <td> 
              <i class="fas fa-arrow-up withHand" @click="MouPromocio(index, 'MU')"></i>
              <i class="fas fa-arrow-down withHand" @click="MouPromocio(index, 'MD')"></i> 
            </td>
          </tr>                  
        </tbody>
      </table>
    </div>
  </div>  




  <div v-if="Editant" class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Editant la promoció {{PromocioDetall.PROMOCIONS_PROMOCIO_ID}}</h5></div>  
    <div class="card-body">    
    
    <table class="table table-striped table-sm">        
        <tbody>
          <?php echo InputHelper('PromocioDetall.PROMOCIONS_NOM', 0, "Nom", "PROMOCIONS_NOM", "", true); ?>
          <?php echo InputHelper('PromocioDetall.PROMOCIONS_TITOL', 0,"Títol", "PROMOCIONS_TITOL", "", true); ?>
          <?php echo InputHelper('PromocioDetall.PROMOCIONS_SUBTITOL',0, "Subtítol", "PROMOCIONS_SUBTITOL", "", true); ?>
          <?php echo SelectHelper('PromocioDetall.PROMOCIONS_ORDRE', 0, "Ordre", 'OptionsOrdre', true); ?>                            
          <?php echo SelectHelper('PromocioDetall.PROMOCIONS_IS_ACTIVA', 0, "Està activa?", 'OptionsActiuNoActiu', true); ?>                  
          <?php echo InputHelper('PromocioDetall.PROMOCIONS_URL', 0, "URL", "PROMOCIONS_URL", "", true); ?>           
          <?php echo ImageHelper('inputNouArxiu($event, \'S\')', 0, "Imatge S", "imatge_s", 'getUrlImatge(\'s\')'); ?>
          <?php echo ImageHelper('inputNouArxiu($event, \'M\')', 0, "Imatge M", "imatge_m", 'getUrlImatge(\'m\')'); ?>
          <?php echo ImageHelper('inputNouArxiu($event, \'L\')', 0, "Imatge L", "imatge_l", 'getUrlImatge(\'l\')'); ?>

          <tr>
            <td>
              <button v-on:click="GuardaPromocio" class="btn btn-success">Guardar</button>
              <button v-on:click="EsborraPromocio" class="btn btn-danger">Eliminar</button>
            </td>
            <td>              
              <button v-on:click="CancelaEdicio" class="btn btn-info">Tornar</button>
            </td>
          </tr>                                                                  
        </tbody>
      </table>
  
    </div>
  </div>


  <div class="modalbox" v-if="MostraModal">    
    <table>
      <tr>
        <td><div style="width: 400px; height: 400px;">  <cropper :src="img" :stencil-props="stencilProps" @change="change"></cropper></div></td>
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




<script>
  
  var apiUrl = '/'  

  var vm2 = new Vue({
  el: '#promocionsComponent',  
  data: { 
    Promocions: [], 
    PromocioDetall: {}, 
    Editant: false, 
    textCercador: "", 
    tipusCercador: 1, 
    img: '', 
    image: '',
    midaImatge: '',
    stencilProps: { aspectRatio: 4/3 },
    extensio: '',
    MostraModal: false,
    OptionsActiuNoActiu: [{'id':'1', 'nom':'Activa'}, {'id':'0', 'nom':'Inactiva'}],
    OptionsOrdre: [{'id':'1', 'nom':'1'}, {'id':'2', 'nom':'2'}]
  },
  created: function() {
          this.BotoCerca();
          this.Editant = false;  
          this.MostraModal = false;        
  },
  computed: {},
  methods: {    
    inputNouArxiu(event, mida) {
      var FileName = event.target.files[0].name;      
      if (event.target.files[0]) {
        var reader = new FileReader();        
        this.extension = FileName.substr(FileName.lastIndexOf('.') + 1);
        reader.onload = (e) => {
          this.img = e.target.result;                    
          this.midaImatge = mida;
          switch(this.midaImatge) {
            case 'S': this.stencilProps = { aspectRatio: 16/9 }; break;
            case 'M': this.stencilProps = { aspectRatio: 4/3 }; break;
            case 'L': this.stencilProps = { aspectRatio: 16/9 }; break;
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
    BotoCerca: function() {
      this.$http.get('/apiadmin/Promocions?accio=L', { params: { 'q': this.textCercador, 't': this.tipusCercador }} ).then(function(response){          
          this.Promocions = response.body;                    
          this.Editant = false;
      }, function() {
            alert('Error!');
      });
    },
    MouPromocio(index, direccio) {

      //Canviem l'ordre de la promocio
      switch(direccio) {
        case 'MD': 
          if( index < this.Promocions.length -1 ) {
            this.Promocions[ index ].PROMOCIONS_ORDRE += 1; 
            this.Promocions[ index + 1 ].PROMOCIONS_ORDRE -= 1; 
          } 
          break;
        case 'MU': 
          if( index > 0 ) {
            this.Promocions[ index - 1 ].PROMOCIONS_ORDRE += 1; 
            this.Promocions[ index ].PROMOCIONS_ORDRE -= 1; 
          } 
          break;
      }
      
      let fd = new FormData();
      fd.append('accio', 'UO');
      fd.append('Promocions', JSON.stringify(this.Promocions));
      this.$http.post('/apiadmin/Promocions', fd ).then(function(response){ 
         this.BotoCerca();
      }, function() {
        alert('Url: ' + response.url + ' \n Error: '  + response.body);            
      });
    },
    AddPromocio: function() {
      this.$http.get('/apiadmin/Promocions?accio=A').then(function(response){          
          this.PromocioDetall = response.body;
          this.Editant = true;
      }, function() {
            alert('Error!');
      });
    },
    EditaPromocio: function(idPromocio) {
      this.$http.get('/apiadmin/Promocions?accio=CU', { params: { 'idPromocio': idPromocio }} ).then(function(response){          
          this.PromocioDetall = response.body;                          
          this.Editant = true;    
      }, function() {
            alert('Error!');
      });
    },
    CancelaEdicio: function() { this.Editant = false; this.PromocioDetall = {}; },
    GuardaPromocio: function() {
      let fd = new FormData();
      fd.append('accio', 'U'); 
      fd.append('PromocioDetall', JSON.stringify(this.PromocioDetall));
      this.$http.post('/apiadmin/Promocions', fd ).then(function(response){                    
          this.BotoCerca();
          this.Editant = false;
      }, function(response) {
        console.log(response);          
          alert('Url: ' + response.url + ' \n Error: '  + response.body);            
      });
    },
    EsborraPromocio: function() {
      let fd = new FormData();
      fd.append('accio', 'D'); 
      fd.append('PromocioDetall', JSON.stringify(this.PromocioDetall));
      
      this.$http.post('/apiadmin/Promocions', fd ).then(function(response){                              
          this.BotoCerca();
          this.Editant = false;
      }, function() {
            alert('Error!');
      });
    },
    GuardaImatge: function() {
      let formData = new FormData();
      formData.append( 'File'     , this.image );
      formData.append( 'accio'    , 'Promocio' );
      formData.append( 'Tipus'    , this.midaImatge);
      formData.append( 'idElement', this.PromocioDetall.PROMOCIONS_PROMOCIO_ID);
      formData.append( 'extensio' , this.extension);
      this.$http.post('/apiadmin/Upload', formData ).then(function(response){
          this.MostraModal = false;
          this.EditaPromocio(this.PromocioDetall.PROMOCIONS_PROMOCIO_ID);
      }, function(response) {
        alert('Url: ' + response.url + ' \n Error: '  + response.body);            
      });
    },
    getUrlImatge: function(mida) {
      let urlbase = '<?php echo IMATGES_URL_PROMOCIONS ?>';            
      let ret = '';      
      switch(mida){
        case 's': ret += (this.PromocioDetall.PROMOCIONS_IMATGE_S) ? urlbase + this.PromocioDetall.PROMOCIONS_IMATGE_S : ''; break;
        case 'm': ret += (this.PromocioDetall.PROMOCIONS_IMATGE_M) ? urlbase + this.PromocioDetall.PROMOCIONS_IMATGE_M : ''; break;
        case 'l': ret += (this.PromocioDetall.PROMOCIONS_IMATGE_L) ? urlbase + this.PromocioDetall.PROMOCIONS_IMATGE_L : ''; break;
      }      
      return ret;
    },
    TancaModalImatge: function() {
      this.MostraModal = false;
    }
  }
  });
</script>