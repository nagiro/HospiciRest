

Vue.component('filters-div', {
    props: {},          
    data: function() {
        return { 
            FiltresOberts: false,
            TagsDisponibles: [  ['1','Artística'], 
                                ['2','Curs'], 
                                ['3','Familiar'], 
                                ['4','Científica'], 
                                ['5','Exposició'], 
                                ['6','Humanitats'], 
                                ['7','Música'], 
                                ['8','Conferència'], 
                                ['11','Tecnològica']                                
                            ],
            MesText: '',
            MesNumero: 0,
            AnyText: '',
            AnyNumero: 0,
            QuantsDiesMes: 0,
            DiesMes: [], 
            DiesMesFiles: [],
            TextCerca: ''
        }
    },    
    created: function() {
        this.carregaMes();
    },
    computed: {

    },
    watch: {},
    methods: {
        carregaMes: function(M = '', Y = '') {
            let D = new Date();            
            let Mes = (M.length == 0) ? D.getMonth() : M;
            let Any = (Y.length == 0) ? D.getFullYear() : Y;
            
            this.MesNumero = Mes;
            this.MesText = this.Mesos(this.MesNumero);
            this.AnyNumero = Any;
            this.AnyText = this.AnyNumero.toString();
            this.QuantsDiesMes = this.QuantsDiesTeElMes(this.AnyNumero, this.MesNumero);
            this.DiesMes = [];
            this.DiesMesFiles = [];
        
            let Fila = 0;
            for (let i = 1; i < this.QuantsDiesMes + 1; i++) {
              let DiaSetmana = new Date(this.AnyNumero, this.MesNumero, i).getDay();
              let DiaText = this.DiaSetmana(DiaSetmana);
              let Classe = [];
              Classe.push('filtres_cal-dia');
              if (DiaSetmana == 6 || DiaSetmana == 0) Classe.push('filtres_cal-dia-festa');
              else Classe.push('filtres_cal-dia-normal');
              let D = { DiaText: DiaText, DiaNumero: i, C: Classe };
              this.DiesMes.push(D);
        
              // Carrego l'array de Dies per files pel calendari
              if (i == 1) {
                let DiesASumar = DiaSetmana == 0 ? 7 : DiaSetmana;
                for (let i = DiesASumar; i > 1; i--) {
                  if (!this.DiesMesFiles[Fila]) this.DiesMesFiles[Fila] = [];
                  this.DiesMesFiles[Fila].push({ DiaText: '', DiaNumero: '', C: Classe });
                }
              }
        
              if (!this.DiesMesFiles[Fila]) this.DiesMesFiles[Fila] = [];
              this.DiesMesFiles[Fila].push(D);
              if (DiaSetmana == 0) Fila++;
            }                        
        },
        
        QuantsDiesTeElMes(any, mes) {
            return new Date(any, mes + 1, 0).getDate();
        },

        DiaSetmana(DiaSetmana) {
            switch (DiaSetmana) {
            case 0:
                return 'Dg';
                break;
            case 1:
                return 'Dl';
                break;
            case 2:
                return 'Dt';
                break;
            case 3:
                return 'Dc';
                break;
            case 4:
                return 'Dj';
                break;
            case 5:
                return 'Dv';
                break;
            case 6:
                return 'Ds';
                break;
            }
        },

        Mesos(Mes) {
            switch (Mes) {
            case 0:
                return 'Gener';
            case 1:
                return 'Febrer';
            case 2:
                return 'Març';
            case 3:
                return 'Abril';
            case 4:
                return 'Maig';
            case 5:
                return 'Juny';
            case 6:
                return 'Juliol';
            case 7:
                return 'Agost';
            case 8:
                return 'Setembre';
            case 9:
                return 'Octubre';
            case 10:
                return 'Novembre';
            case 11:
                return 'Desembre';
            }
        },
        GoMes: function( Menys = false ) {
            let M = this.MesNumero;
            let Y = this.AnyNumero;
            if (Menys && M == 0) {
              Y--;
              M = 11;
            } else if (!Menys && M == 11) {
              Y++;
              M = 0;
            } else if (Menys) {
              M--;
            } else {
              M++;
            }
            
            this.carregaMes(M, Y);
        },
        cercaText: function() {                                
            window.location.href = "/activitats/text/" + encodeURIComponent(this.TextCerca);
        },
        executaFiltre: function($eventTipus) {                    
            const T = this.TagsDisponibles.find( X => X[0] == $eventTipus.target.value );
            window.location.href= this.getLinkTipus( T );        
        },
        getLinkTipus: function(Tipus = '', Dia = '') {                                                
            if(typeof Tipus == 'object') {
                return '/activitats/tipus/' + Tipus[0] + '/' + normalize(Tipus[1]);
            } else if(typeof Dia == 'number') {
                return '/activitats/data/' + this.AnyNumero + '-' + (this.MesNumero + 1) + '-' + Dia ;
            }            
        }
    },
    template: `        
<section>
    <div class="filtres_requadre_filtres">
        <div class="filtres_titol_filtres" @click="FiltresOberts = !FiltresOberts">Filtres</div>
        <div class="filtres_requadre_tots_filtres" v-if="FiltresOberts">
            <div class="filtres_requadre_calendari">
                <div class="filtres_cal-mesos-fletxa"> 
                    <a  @click="GoMes(true)" > &lt; </a>
                </div>
                <div class="filtres_requadre-taula-calendari">
                    <div class="filtres_control-mesos">{{ MesText }} {{ AnyText }}</div>
                    
                    <table class="filtres_taula-calendari">
                        <tr>
                            <td class="filtres_cal-dia-title">Dl</td>
                            <td class="filtres_cal-dia-title">Dt</td>
                            <td class="filtres_cal-dia-title">Dc</td>
                            <td class="filtres_cal-dia-title">Dj</td>
                            <td class="filtres_cal-dia-title">Dv</td>
                            <td class="filtres_cal-dia-title">Ds</td>
                            <td class="filtres_cal-dia-title">Dg</td>
                        </tr>
                        <tr v-for="Row of DiesMesFiles">
                            <td v-for="D of Row" :class="D.C" @click="executaFiltre('DATA_INICIAL', D.DiaNumero)">
                                {{ D.DiaNumero }}
                            </td>
                        </tr>
                    </table>                
                </div>

                <div class="filtres_cal-mesos-fletxa" @click="GoMes(false)">
                    <a  @click="GoMes(false)" > &gt; </a>
                </div>
            </div>
            <div class="filtres_AltresFiltresBlock form-group">
                <select id="TagsSelect" class="filtres_form-control" @change="executaFiltre($event)">
                    <option :value="0">-- ESCULL UN TIPUS D'ACTIVITAT --</option>
                    <option v-for="T of TagsDisponibles" :value="T[0]">{{ T[1] }}</option>
                </select>

                <div class="filtres_input_search">
                    <label for="input_cerca" style="display:none">Cerca un text</label>
                    <input type="text" id="input_cerca" v-model="TextCerca" class="filtres_form-control_input_text" placeholder="Escriu un text per buscar..." />
                    <button @click="cercaText()">Cerca!</button>
                </div>
            </div>
        </div>
    </div>
  
    
    <nav class="filtres_calendari">
        <div class="filtres_control-mesos">
            <a class="filtres_cal-mesos-fletxa" @click="GoMes(true)">
                <span> < </span>
            </a>
            <div class="filtres_cal-mesos">{{ MesText }}<br />{{ AnyText }}</div>
            <a class="filtres_cal-mesos-fletxa" @click="GoMes(false)">
                <span> > </span>
            </a>
        </div>
        <div class="filtres_control-dies">
            <a :class="D.C" v-for="D of DiesMes" :href="getLinkTipus('', D.DiaNumero)">
                <div class="filtres_cal-dia-text">{{ D.DiaText }}</div>
                <div class="filtres_cal-dia-numero">{{ D.DiaNumero }}</div>
            </a>
        </div>
    </nav>
  
    <div class="filtres_box-mes-filtres">
        <div class="filtres_mes-filtres">Més filtres</div>
        <div class="filtres_mes-filtres-resta">
            <a class="filtres_mes-filtres-resta-element" v-for="T of TagsDisponibles" :href="getLinkTipus(T)">
                {{ T[1] }}
            </a>
            <label for="InputTextCerca" style="display:none">Cerca un text</label>
            <input type="text" v-model="TextCerca" id="InputTextCerca" placeholder="Cerca..." />
            <button class="btn btn-xs" id="InputTextBoto" @click="cercaText()">Cerca!</button>                                  
        </div>      
    </div>
</section>
                `
});