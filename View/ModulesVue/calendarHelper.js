
Vue.component('calendar-helper', {
    props: {        
        horaris: Object,
        calendari: Array        // Year -> Month -> Day -> PROPIETATS -> { DIA_SETMANA, DIA }
    },          
    data: function() { 
        return { 
            valorAutentic: new Date(this.valorDefecte), 
            DiaEscollit: ''            
        }
    },
    computed: {},
    watch: {},
    methods: {
        getMesNom: function(DataAnyMes) {
            const D = ConvertirData(DataAnyMes, 'Object');
            return MesNom( D , true) + ' ' + D.any;
        },
        OnChange() {                        
            const D = this.valorAutentic.getFullYear() + '-' 
                        + (this.valorAutentic.getMonth() + 1) + '-' 
                        + this.valorAutentic.getDate();
            this.$emit('onchange', D);
        },
        mostraDia: function($Dia) {
            this.DiaEscollit = $Dia;
            this.$emit('mostra-dia', $Dia);
        },
        getEstilDia: function(Dia) {            
            
            return (this.horaris[Dia] && this.horaris[Dia].length > 0 ) ? { "font-weight": "bold" } : { "font-weight": "normal" };
        },
        EditaActivitat: function($idA) {}
              
    },
    template: `        
    <div class="CalendarHelper_Div">                
        <table class="CalendarHelper_Table" v-for="Mesos of calendari">
            <tr><td colspan="8" class="CalendarHelper_Table_TitolAny">{{getMesNom(Mesos.DataAnyMes)}}</td></tr>
            <tr v-for="Setmanes of Mesos.D">
                <td class="CalendarHelper_Table_Setmana">{{Setmanes.Setmana}}</td>
                <td v-for="Dies of Setmanes.D">
                    <a class="mytooltip" v-if="Dies.Dia > 0" @click="mostraDia(Dies.Propietats.DIA)">
                        <div v-bind:style="getEstilDia(Dies.Propietats.DIA)">{{Dies.Dia}}</div>
                        <div class="mytooltiptext">                            
                            <llistat-activitats-helper 
                                :horaris = "horaris" 
                                :data-dia = "Dies.Propietats.DIA" 
                                :resum="true" 
                                @edita_activitat = "EditaActivitat"
                            ></llistat-activitats-helper>
                        </div>
                    </a>                
                </td>                
            </tr>
        </table>        
    </div>    
                `,
});