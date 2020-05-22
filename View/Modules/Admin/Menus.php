
<div id="menuComponent" class="col-2">

  <div v-for="cat in menuItemsAgrupats">
    <div class="menuTitol"> <span class="label label-primary"> {{ cat.Tipus }} </span> </div>
    <div v-for="m in cat.Dades" class="menuItem">      
      <a :href="m.MENUS_Url"> <i class="fas fa-angle-double-right"></i>  {{ m.MENUS_Titol }}</a>
    </div>
  </div>
    
</div>


<script>
  var apiUrl = '/'

  var vm = new Vue({
  el: '#menuComponent',
  data: { menuItemsAgrupats: [] },
  created: function() {
          
    this.axios.get('/apiadmin/Menus', { 
                'params' : { 
                  'accio': 'UM', 
                  'IdUsuari': 1,
                  'IdSite': 1} } )
              .then( R => ( this.menuItemsAgrupats = R.data ) )
              .catch( E => ( alert(E) ) );

  },
  computed: {},
  methods: {}
  });
</script>