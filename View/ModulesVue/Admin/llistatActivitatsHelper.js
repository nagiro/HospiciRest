
Vue.component('llistat-activitats-helper', {
    props: {        
        horaris: Object,  
        resum: Boolean,
        DataDia: String,
    },          
    data: function() { return {  }},
    computed: {},
    watch: {},
    methods: {        
        getActivitats() {
            return ( this.horaris[ this.DataDia ] ) ? this.horaris[ this.DataDia ] : [];            
        },
        Ordena(Camp) {
            let A = this.getActivitats();
            A.sort( ( X, Y ) => { let O = X[Camp] > Y[Camp]; return O ? 1 : -1; });            
        },
        editaActivitat: function($idActivitat) {            
            this.$emit('edita_activitat', $idActivitat);
        },
    },
    filters: {
        TreuSegons: function(Text) {
            return Text.split(":").splice(0,2).join(':');
        }
    },
    template: `        
    <div class="LlistatActivitatsHelper_Div">        
        
        <table class="LlistatActivitatsHelper_Table" v-if="!resum">
            <tr>
                <th colspan="4" @click="Ordena('ACTIVITATS_Nom')">Títol</th>
            </tr>
            <tr>
                <th @click="Ordena('HORARIS_HoraInici')">Inici</th>
                <th @click="Ordena('HORARIS_HoraFi')">Fi</th>
                <th @click="Ordena('ESPAIS_Nom')">Espai</th>
                
                <th @click="Ordena('ACTIVITATS_Organitzador')">Organitzador</th>
            </tr>
            <tbody v-for="Activitat of getActivitats()">
                
                <tr>
                    <td colspan="4" style="background-color: whitesmoke; border-top: 1px solid gray;">&nbsp;</td>
                </tr>    
                <tr style="margin-top: 1vw;">
                    <td colspan="4" style="width: 100%">                    
                        <b><a href="#" @click.prevent="editaActivitat(Activitat.ACTIVITATS_ActivitatId)">{{Activitat.ACTIVITATS_Nom}}</a></b> <span style="font-size: 0.6rem; color: gray;">{{Activitat.ACTIVITATS_Organitzador}}</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 10%; color: green; font-weight: bold;">{{Activitat.HORARIS_HoraInici | TreuSegons}}</td>
                    <td style="width: 10%; color: gray;">{{Activitat.HORARIS_HoraFi | TreuSegons}}</td>
                    <td style="width: 40%; color: crimson; font-weight: bold">{{Activitat.ESPAIS_Nom}}</td>                                        
                    <td></td>
                </tr>
            </tbody>
        </table>                                                        

        <table class="LlistatActivitatsHelper_Table" v-if="resum">            
            <tbody v-for="Activitat of getActivitats()">                                
                <tr style="margin-top: 1vw;">
                    <td colspan="4" style="width: 100%">
                        <b>{{Activitat.ACTIVITATS_Nom}}</b> <span style="font-size: 0.6rem; color: gray;">{{Activitat.ACTIVITATS_Organitzador}}</span>
                    </td>
                </tr>
                <tr>
                    <td style="width: 10%; color: green; font-weight: bold;">{{Activitat.HORARIS_HoraInici | TreuSegons}}</td>
                    <td style="width: 10%; color: gray;">{{Activitat.HORARIS_HoraFi | TreuSegons}}</td>
                    <td style="width: 40%; color: crimson; font-weight: bold">{{Activitat.ESPAIS_Nom}}</td>                                        
                    <td></td>
                </tr>
            </tbody>
        </table>                                                        
        
    </div>
    
                `,
});