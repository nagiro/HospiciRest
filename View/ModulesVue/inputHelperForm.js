
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

        OnIntro() {            
            this.$emit('onintro', this.valorAutentic)
        },

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
                    @keyup.enter = "OnIntro"
                    :placeholder="'Entra un text...'">     

        </div>
    </div>
                `,
});