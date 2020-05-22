
<div id="horarisComponent" style="margin-bottom: 30px;" class="col-10">

  <div class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Cercador</h5></div>  
    <div class="card-body">    
    
      <div class="row">
                
        <?php echo InputHelper('textCercador',  8,"Paraules", "cercador", "Paraules a buscar...", false); ?>      
        <?php echo ButtonHelper('BotoCerca', 2, "Cerca!", 'btn-success'); ?>                
        
      </div>    
      
    </div>
  </div>

  <!-- LLISTAT D'ACTIVITATS I CALENDARI -->

  <div v-if="!Editant" class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><?php echo TitleWithAdd('AddActivitat()', 'Calendari d\'activitats') ?></div>    
    <div class="card-body">      

      <calendar-helper :horaris = "Horaris" :calendari = "Calendari"></calendar-helper>

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

  <!-- EDICIÓ DE L'ACTIVITAT -->

  <div v-if="Editant" class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Editant la promoció {{PromocioDetall.PROMOCIONS_PROMOCIO_ID}}</h5></div>  
    <div class="card-body">    
    
    <div class="table table-striped table-sm">    
    
      <input-helper             
          :titol = "'Nom'"
          :valor-defecte = "PromocioDetall.PROMOCIONS_NOM"
          :id = "'PROMOCIONS_NOM'"
          @onchange = "PromocioDetall.PROMOCIONS_NOM = $event"
        ></input-helper>
      
        <input-helper             
          :titol = "'Titol'"
          :valor-defecte = "PromocioDetall.PROMOCIONS_TITOL"
          :id = "'PROMOCIONS_TITOL'"
          @onchange = "PromocioDetall.PROMOCIONS_TITOL = $event"
        ></input-helper>

        <input-helper             
          :titol = "'Subtítol'"
          :valor-defecte = "PromocioDetall.PROMOCIONS_SUBTITOL"
          :id = "'PROMOCIONS_SUBTITOL'"
          @onchange = "PromocioDetall.PROMOCIONS_SUBTITOL = $event"
        ></input-helper>

        <select-helper             
          :titol = "'Activa?'"
          :valor-defecte = "PromocioDetall.PROMOCIONS_IS_ACTIVA"
          :id = "'PROMOCIONS_IS_ACTIVA'"
          :options = "OptionsActiuNoActiu"
          @onchange = "PromocioDetall.PROMOCIONS_IS_ACTIVA = $event"
        ></select-helper>

        <input-helper             
          :titol = "'Url'"
          :valor-defecte = "PromocioDetall.PROMOCIONS_URL"
          :id = "'PROMOCIONS_URL'"
          @onchange = "PromocioDetall.PROMOCIONS_URL = $event"
        ></input-helper>

        <image-helper 
          :accio-esborra = "'Promocio_Delete'"
          :accio-guarda="'Promocio'"
          :id-element = "this.PromocioDetall.PROMOCIONS_PROMOCIO_ID"
          :mida-imatge = "'s'"
          :url-a-mostrar = "getUrlImatge('s')"            
          :titol = "'Imatge petita'"
          @reload = "ReloadImatge($event, 'IMATGE_S')"
        ></image-helper>
        <image-helper 
          :accio-esborra = "'Promocio_Delete'"
          :accio-guarda="'Promocio'"
          :id-element = "this.PromocioDetall.PROMOCIONS_PROMOCIO_ID"
          :mida-imatge = "'m'"
          :url-a-mostrar = "getUrlImatge('m')"            
          :titol = "'Imatge mitjana'"
          @reload = "ReloadImatge($event, 'IMATGE_M')"
        ></image-helper>
        <image-helper 
          :accio-esborra = "'Promocio_Delete'"
          :accio-guarda="'Promocio'"
          :id-element = "this.PromocioDetall.PROMOCIONS_PROMOCIO_ID"
          :mida-imatge = "'l'"
          :url-a-mostrar = "getUrlImatge('l')"            
          :titol = "'Imatge gran'"
          @reload = "ReloadImatge($event, 'IMATGE_L')"
        ></image-helper>

        <div class="R">
          <div class="FT">
            <button v-on:click="GuardaPromocio" class="btn btn-success">Guardar</button>
            <button v-on:click="EsborraPromocio" class="btn btn-danger">Eliminar</button>
          </div>
          <div class="FI">              
            <button v-on:click="CancelaEdicio" class="btn btn-info">Tornar</button>
          </div>
        </div>          

      </div>
  
    </div>
  </div>

</div>




<script>  

  var apiUrl = '/'  

  var vm2 = new Vue({
  el: '#horarisComponent',    
  data: { 
    Horaris: [], 
    Calendari: [],
    PromocioDetall: {}, 
    Editant: false, 
    textCercador: "",     
    img: '', 
    image: '',
    midaImatge: '',
    stencilProps: { aspectRatio: 4/3 },
    extensio: '',
    MostraModal: false,
    
  },
  created: function() {
          this.BotoCerca();
          this.Editant = false;  
          this.MostraModal = false;        
  },
  computed: {},
  methods: {    
 
    BotoCerca: function() {
      this.DataInicial = '2018-09-01';
      this.DataFinal = '2018-12-01';
      this.axios.get('/apiadmin/Horaris', { 'params' : { 'accio': 'L', 'q': this.textCercador, 'DataInicial': this.DataInicial, 'DataFinal': this.DataFinal } } )
        .then( R => { this.Horaris = R.data.HORARIS; this.Calendari = R.data.CAL; this.Editant = false; } )
        .catch( E => { alert(E) } );

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
      this.axios.post('/apiadmin/Promocions', fd )
        .then( R => { this.BotoCerca() } )
        .catch( E => alert( E )  )
    },
    
    //TODO
    AddActivitat: function() {
/*      this.axios.get('/apiadmin/Promocions', { 'params' : { 'accio': 'A'}} )
                .then( R => { this.PromocioDetall = R.data; this.Editant = true; } )
                .catch( E => { alert(E) });
*/              
    },
    
    EditaPromocio: function(idPromocio) {
      
      this.axios.get('/apiadmin/Promocions', { params: { 'accio': 'CU', 'idPromocio': idPromocio }} )
                .then(  R => { this.PromocioDetall = R.data; this.Editant = true; } )
                .catch( E => { alert(E); } );      

    },

    ReloadImatge: function(idPromocio, ImatgeTipus) {
      
      console.log(R.data['ImatgeTipus']);

      this.axios.get('/apiadmin/Promocions', { params: { 'accio': 'CU', 'idPromocio': idPromocio }} )
                .then(  R => { this.PromocioDetall = R.data['ImatgeTipus']; this.Editant = true; } )
                .catch( E => { alert(E); } );      
      
    },

    CancelaEdicio: function() { this.Editant = false; this.PromocioDetall = {}; },

    GuardaPromocio: function() {
      let fd = new FormData();
      fd.append('accio', 'U'); 
      fd.append('PromocioDetall', JSON.stringify(this.PromocioDetall));
      this.axios.post('/apiadmin/Promocions', fd )
                .then( R => { this.BotoCerca(); this.Editant = false; } )
                .catch( E => { alert(E); } ); 
    },

    EsborraPromocio: function() {
      let fd = new FormData();
      fd.append('accio', 'D'); 
      fd.append('PromocioDetall', JSON.stringify(this.PromocioDetall));
      
      this.$http.post('/apiadmin/Promocions', fd )
                .then( R => { this.BotoCerca(); this.Editant = false; } )
                .catch( E => { alert(E); } );      

    },

    getUrlImatge: function(mida) {
      let urlbase = '<?php echo IMATGES_URL_PROMOCIONS ?>';            
      let ret = '';      
      switch(mida){
        case 's': ret += (this.PromocioDetall.PROMOCIONS_IMATGE_S) ? urlbase + this.PromocioDetall.PROMOCIONS_IMATGE_S + '?t=' + Date.now() : ''; break;
        case 'm': ret += (this.PromocioDetall.PROMOCIONS_IMATGE_M) ? urlbase + this.PromocioDetall.PROMOCIONS_IMATGE_M + '?t=' + Date.now() : ''; break;
        case 'l': ret += (this.PromocioDetall.PROMOCIONS_IMATGE_L) ? urlbase + this.PromocioDetall.PROMOCIONS_IMATGE_L + '?t=' + Date.now() : ''; break;
      }      
      return ret;
    }

  }
  });
</script>