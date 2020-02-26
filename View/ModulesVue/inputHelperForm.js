
Vue.component('input-helper', {
    props: {        
        titol: String,
        valorDefecte: String,
        id: String       
    },          
    data: function() { return { valorAutentic: this.valorDefecte }},
    computed: {},
    watch: {},
    methods: {

        OnChange() {
            this.$emit('onchange', valorAutentic)
        }
              
    },
    template: `    
    <tr>
        <td> {{titol}} </td>
        <td> 

            <input  type="text" 
                    class="form-control 
                    form-control-sm" 
                    :id="id" 
                    v-model="valorAutentic"  
                    :placeholder="valorDefecte">     

        </td>
    </tr>
                `,
});


