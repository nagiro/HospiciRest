
<div id="avuiComponent" class="col-10">

  <div class="card border-secondary card-default" style="margin-top:20px;">
    
  <div class="card-header"><h5>Missatges</h5></div>  
  <div class="card-body">      
      <table class="table table-striped table-hover table-sm">
        <thead>
          <tr>
            <th scope="col">Titol</th>
            <th scope="col">Enviant</th>          
          </tr>
        </thead>
        <tbody>        
          <tr v-for="M in Missatges" >
            <td> <a> {{ M.MISSATGES_TITOL }} </a> </td>
            <td style="width: 20%;"> {{M.MISSATGES_USUARI_NOM}} </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>


  <div class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Feines i notificacions</h5></div>    
    <div class="card-body">      
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th scope="col">Tipus</th>
            <th scope="col">Text</th>          
            <th scope="col">Qui</th>          
          </tr>
        </thead>
        <tbody>        
          <tr v-for="I in Incidencies" >
            <td> Incidència </td>
            <td> {{ I.INCIDENCIES_TITOL }} <span> | {{ I.INCIDENCIES_DATAALTA }}</span> </td>
            <td> {{ I.INCIDENCIES_QUIINFORMA }} </td>
          </tr>
          <tr v-for="F in Feines" >
            <td> Feina </td>
            <td> {{ F.PERSONAL_TEXT }} <span style="color:gray;"> | Data alta: {{ F.PERSONAL_DATA_ALTA }}</span> </td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>


  <div class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Activitats per avui</h5></div>      
    <div class="card-body">      
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th scope="col">Títol</th>
            <th scope="col">Hora Inici</th>          
            <th scope="col">Hora Fi</th>          
            <th scope="col">Lloc</th>          
          </tr>
        </thead>
        <tbody>        
          <tr v-for="A in Activitats" >
            <td> 
              {{ A.ACTIVITATS_NOM }}               
                <div v-if="A.HORARIS_AVIS.length > 0" class="tooltip">
                  <i class="fas fa-exclamation-circle"></i>
                  <span class="tooltiptext"> {{ A.HORARIS_AVIS }}</span>
                </div>              
            </td>
            <td style="color: green;"> {{ A.HORARIS_HORAINICI }} </td>
            <td> {{ A.HORARIS_HORAFI }} </td>
            <td style="width: 20%; color: brown;"> {{ A.ESPAIS_NOM }} </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>



</div>


<script>
  var apiUrl = '/'

  var vm = new Vue({
  el: '#avuiComponent',  
  data: { Missatges: [], Incidencies: [], Feines: [], Activitats: [] },
  created: function() {
          this.$http.get('/apiadmin/Avui?accio=C', {} ).then(function(response){          
          this.Missatges = response.body.Missatges;
          this.Incidencies = response.body.Incidencies;
          this.Feines = response.body.Feines;
          this.Activitats = response.body.Activitats;                    
          console.log(this.Missatges);
    }, function() {
          alert('Error!');
    });
  },
  computed: {},
  methods: {}
  });
</script>