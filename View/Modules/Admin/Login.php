<div id="loginComponent" class="col-10">

    <div class="">Usuari: </div>
    <div class=""><input v-model="dni" placeholder="Usuari..." type="text" /></div>
    <div class="">Contrasenya: </div>
    <div class=""><input v-model="password"  placeholder="Contrasenya..."  type="password" /></div>  
    <div class="">Site: </div>
    <div class="">          
      <Select v-model="idsite">
        <option v-for="A in all_sites" :value="A.SITES_SiteId">{{A.SITES_Nom}}</option>
      </Select>
    </div>      
    <div class="" v-if="error">L'usuari o contrasenya són incorrectes</div>
    <div><button class="btn btn-success" v-on:click="BotoIdentificat">Identifica't</button></div>
        
</div>


<script>

var apiUrl = '/'

var vm = new Vue({
  el: '#loginComponent',
  data: { 
    dni: '', 
    password: '' , 
    idsite: 1, 
    promocions: {}, 
    usuari: {}, 
    error: false,
    all_sites: [],
    loading: false,
  },
  created: function() {

    this.loading = true;
    this.axios.get('/apiadmin/Sites', { 'params' : { 'accio': 'ALL_SITES', } } )
              .then( response => ( this.all_sites = response.data ))
              .catch(error => ( alert(error) ))
              .finally(() => this.loading = false );              

  },  
  computed: {},
  methods: {
    BotoIdentificat: function(event){
      
      this.axios.get('/apiadmin/Auth', {
                'params' : {
                  'accio': 'A',
                  'login': this.dni,
                  'password': this.password,
                  'idsite': this.idsite } } )
                .then( R  => ( window.location.href = "/admin/avui" ) )
                .catch( E => ( window.location.href = "/admin/login"  ) );
    }

  }
});
</script>