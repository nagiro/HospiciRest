
<div id="menuComponent" class="col-2">

  <div v-for="cat in menuItemsAgrupats">
    <div class="menuTitol"> <span class="label label-primary"> {{ cat.Tipus }} </span> </div>
    <div v-for="m in cat.Dades">
      <a :href="m.MENUS_Url"> {{ m.MENUS_Titol }}</a>
    </div>
  </div>
    
</div>


<script>
  var apiUrl = '/'

  var vm = new Vue({
  el: '#menuComponent',
  data: { menuItemsAgrupats: [] },
  created: function() {
          this.$http.get('/apiadmin/Menus?accio=UM&IdUsuari=1&IdSite=1', { 
            'params' : { 
              'accio': 'UM', 
              'IdUsuari': 1,
              'IdSite': 1} } ).then(function(response){                
          this.menuItemsAgrupats = response.body;          
          
    }, function() {
          alert('Error!');
    });
  },
  computed: {},
  methods: {}
  });
</script>