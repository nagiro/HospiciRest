
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

      <calendar-helper :horaris = "Horaris" :calendari = "Calendari" v-on:mostra-dia = "mostraDia($event)"></calendar-helper>
      
      <h2 style="margin: 3vw 0vw 2vw 0vw;">Llistat d'activitats</h2>

      <llistat-activitats-helper :horaris = "Horaris" :data-dia = "DiaEscollit" :resum = "false"></llistat-activitats-helper>

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
    Horaris: {}, 
    Calendari: [],
    DiaEscollit: '',


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
      this.axios.get('/apiadmin/Horaris', { 'params' : { 'accio': 'L', 'q': this.textCercador, 'DataInicial': this.DataInicial } } )
        .then( R => { this.Horaris = R.data.HORARIS; this.Calendari = R.data.CAL; this.Editant = false; } )
        .catch( E => { alert(E) } );

    },
    mostraDia: function($DiaEscollit) {      
      this.DiaEscollit = $DiaEscollit;
    }

    


  }
  });
</script>