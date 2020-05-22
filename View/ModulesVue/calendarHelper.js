
Vue.component('calendar-helper', {
    props: {        
        horaris: Array,
        calendari: Array        // Year -> Month -> Day -> PROPIETATS -> { DIA_SETMANA, DIA }
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
    <div>        
        
        <table v-for="Mesos of calendari">
            <tr><td>{{Mesos.AnyMes}}</td></tr>
            <tr v-for="Setmanes of Mesos.D">
                <td>{{Setmanes.Setmana}}</td>
                <td v-for="Dies of Setmanes.D">
                    {{Dies.Dia}}
                </td>                
            </tr>
        </table>
        {{calendari}} 
    </div>
                `,
});