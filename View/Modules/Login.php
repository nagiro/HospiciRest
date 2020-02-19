
<div id="loginComponent" class="col-10">

    <div class="">Usuari: </div>
    <div class=""><input v-model="dni" placeholder="Usuari..." type="text" /></div>
    <div class="">Contrasenya: </div>
    <div class=""><input v-model="password"  placeholder="Contrasenya..."  type="password" /></div>  
    <div class="">Site: </div>
    <div class=""><input v-model="idsite" type="text" /></div>  
    <div class="" v-if="error">L'usuari o contrasenya són incorrectes</div>
    <div><button class="btn btn-success" v-on:click="BotoIdentificat">Identifica't</button></div>
        
</div>




<script>

var apiUrl = '/'

var vm = new Vue({
  el: '#loginComponent',
  data: { dni: '40359575A', password: '40359575A' , idsite: 1, promocions: {}, usuari: {}, error: false },
  created: function() {},
  computed: {},
  methods: {
    BotoIdentificat: function(event){
      this.$http.get('/apiadmin/Auth', {                
                'params' : {
                  'accio': 'A',
                  'login': this.dni,
                  'password': this.password,
                  'idsite': this.idsite } } ).then(function(response){                            
              window.location.href = "/admin/avui";
        }, function(response) {            
              alert(response.body);
              window.location.href = "/admin/login";
        });
    }
  }
});
</script>