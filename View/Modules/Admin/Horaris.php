
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
    
      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">General</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Descripció</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">Horaris</a>
        </li>
      </ul>
      <div class="tab-content" id="pills-tabContent">
        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">

          <!-- AQUÍ COMENÇA EL FORMULARI GENERAL -->
          
          <div class="table table-striped table-sm">    

            <div v-for="FA of FormulariActivitat.FormFields">    
              <form-helper :formulari = "FA" @onchange="FormulariActivitat.ModelObject[FA.Id] = $event"></form-helper>        
            </div>

            <div class="R">
              <div class="FT">
                <button v-on:click="GuardaActivitat" class="btn btn-success">Guardar</button>
                <button v-on:click="EsborraActivitat" class="btn btn-danger">Eliminar</button>
              </div>
              <div class="FI">              
                <button v-on:click="CancelaEdicio" class="btn btn-info">Tornar</button>
              </div>
            </div>          

          </div>

          <!-- ACABA FORMULARI GENERAL -->

        </div>
        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">

          <!-- AQUÍ COMENÇA EL FORMULARI DESCRIPCIÓ -->
          
          <div class="table table-striped table-sm">    

            <div v-for="FA of FormulariDescripcioActivitat.FormFields">    
              <form-helper :formulari = "FA" @onchange="FormulariActivitat.ModelObject[FA.Id] = $event"></form-helper>
            </div>

            <div class="R">
              <div class="FT">
                <button v-on:click="GuardaActivitat" class="btn btn-success">Guardar</button>
                <button v-on:click="EsborraActivitat" class="btn btn-danger">Eliminar</button>
              </div>
              <div class="FI">              
                <button v-on:click="CancelaEdicio" class="btn btn-info">Tornar</button>
              </div>
            </div>          

          </div>

          <!-- ACABA FORMULARI DESCRIPCIÓ -->


        </div>
        <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">

          <!-- AQUÍ COMENÇA EL FORMULARI HORARIS -->

          <div class="table table-striped table-sm">    

            <div v-for="FA of FormulariActivitat.F3">    
              <form-helper :formulari = "FA"></form-helper>        
            </div>

            <div class="R">
              <div class="FT">
                <button v-on:click="GuardaActivitat" class="btn btn-success">Guardar</button>
                <button v-on:click="EsborraActivitat" class="btn btn-danger">Eliminar</button>
              </div>
              <div class="FI">              
                <button v-on:click="CancelaEdicio" class="btn btn-info">Tornar</button>
              </div>
            </div>          

          </div>

          <!-- ACABA FORMULARI HORARIS -->


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
    idSite: 1,
    DiaEscollit: '',
    ActivitatDetall: {}, 
    FormulariActivitat: {},
    FormulariDescripcioActivitat: {},
    FormulariHoraris: {},
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
        .then( R => { 
          this.FormulariActivitat = R.data.FormGeneral; 
          this.FormulariDescripcioActivitat = R.data.FormDescripcio;
          this.FormulariHoraris = R.data.FormHoraris;
          console.log(this.FormulariActivitat); 
          this.Editant = true; 
        } )
        .catch( E => { alert(E) } );
    },    
    GuardaActivitat: function() {
      let fd = new FormData();
      fd.append('accio', 'UA'); 
      fd.append('ActivitatDetall', JSON.stringify(this.FormulariActivitat.ModelObject));
      this.axios.post('/apiadmin/Horaris', fd )
                .then( R => { console.log(R); } )
                .catch( E => { alert(E); } ); 
    },
    EsborraActivitat: function() {
      let fd = new FormData();
      fd.append('accio', 'DA'); 
      fd.append('ActivitatDetall', JSON.stringify(this.FormulariActivitat.ModelObject));
      this.axios.post('/apiadmin/Horaris', fd )
                .then( R => { console.log(R); } )
                .catch( E => { alert(E); } ); 

    },
    CancelaEdicio: function() {

    }
    
  }
  });
</script>