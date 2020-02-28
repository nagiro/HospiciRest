
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
            this.$emit('onchange', this.valorAutentic)
        }
              
    },
    template: `    
    <div class="R">
        <div class="FT"> {{titol}} </div>
        <div class="FI"> 

            <input  type="text" 
                    class="form-control form-control-sm" 
                    :id="id" 
                    v-model="valorAutentic"
                    @keyup="OnChange"
                    :placeholder="valorDefecte">     

        </div>
    </div>
                `,
});