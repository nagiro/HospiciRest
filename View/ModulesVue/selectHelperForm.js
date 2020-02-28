
Vue.component('select-helper', {
    props: {        
        titol: String,
        valorDefecte: '',
        id: String, 
        options: Array,
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

            <select class="form-control form-control-sm" 
                    :id="id" 
                    v-model="valorAutentic"
                    @change="OnChange"
                    >
                <option v-for="O in options" :value="O.id" :key="O.id">{{O.nom}}</option>
            </select>
        </div>
    </div>
                `,
});


