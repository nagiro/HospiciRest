
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

      <llistat-activitats-helper @edita_activitat = "editaActivitat($event)" :horaris = "Horaris" :data-dia = "DiaEscollit" :resum = "false"></llistat-activitats-helper>

    </div>
  </div>  

  <!-- EDICIÓ DE L'ACTIVITAT -->

  <div v-if="Editant" class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Editant l'activitat {{ActivitatDetall.ACTIVITAT_ActivitatId}}</h5></div>  
    <div class="card-body">    
    
    <div class="table table-striped table-sm">    

      <div v-for="FA of FormulariActivitat">    
        <form-helper :formulari = "FA"></form-helper>        
      </div>

<!--
        <div class="R">
          <div class="FT">
            <button v-on:click="GuardaPromocio" class="btn btn-success">Guardar</button>
            <button v-on:click="EsborraPromocio" class="btn btn-danger">Eliminar</button>
          </div>
          <div class="FI">              
            <button v-on:click="CancelaEdicio" class="btn btn-info">Tornar</button>
          </div>
        </div>          
-->
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
    idSite: 1,
    DiaEscollit: '',
    ActivitatDetall: {}, 
    FormulariActivitat: [],
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
    },
    editaActivitat: function($idActivitat) {
      this.axios.get('/apiadmin/Horaris', { 'params' : { 'accio': 'GetEditActivitat', 'idA': $idActivitat, 'idS': this.idSite } } )
        .then( R => { this.FormulariActivitat = R.data; console.log(this.FormulariActivitat); this.Editant = true; } )
        .catch( E => { alert(E) } );
    }

    


  }
  });
</script>