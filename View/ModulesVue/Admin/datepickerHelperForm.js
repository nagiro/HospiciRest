
Vue.component('datepicker-helper', {
    props: {        
        titol: String,
        valorDefecte: String,
        id: String        
    },          
    data: function() { return { valorAutentic: new Date(this.valorDefecte) }},
    computed: {},
    watch: {},
    methods: {

        OnChange() {                        
            const D = this.valorAutentic.getFullYear() + '-' 
                        + (this.valorAutentic.getMonth() + 1) + '-' 
                        + this.valorAutentic.getDate();
            this.$emit('onchange', D)
        }
              
    },
    template: `        
    <div class="R">
        <div class="FT"> {{titol}} </div>
        <div class="FI"> 

            <v-date-picker v-model="valorAutentic" @input="OnChange"  />    

        </div>
    </div>
                `,
});