<style>
        .detall_bloc { width: 100%; padding: 2vw; padding-top: 4vw; }
        #detall_franja_titol { text-align: center; width: 100%; display: block; cursor: default; }    
        #detall_dates > p { font-size: 1.5rem; }
        #detall_requadre_detall { border: 1px solid black; padding: 2vw; margin-top: 4vw; font-size: 1rem }        
        #detall_requadre_info { border: 1px solid black; padding: 2vw; margin-top: 2vw; font-size: 1rem; }
        .detall_bloc h1 { font-size: 3rem; margin-bottom: 2vw; margin-top: 4vw; }
        .detall_bloc h2 { font-size: 1.5rem; margin-bottom: 1vw; margin-top: 3vw; }
        .detall_bloc h3 { font-size: 1.3rem; margin-bottom: 1vw; margin-top: 3vw; }        
            
        #detall_horaris { display: relative; margin-top: 1.5vw; text-align: center; }
        #detall_horaris > summary { margin-bottom:2vw; }
        .detall_horaris_mesos { display: inline-flex; width: 53vw; }
        .detall_horaris_mesos_titol { font-weight: bold; width: 7vw; padding: 0.5vw 0vw;  }
        .detall_horaris_dia, .detall_horaris_dia_festa { display: block; width: 1.5vw; padding: 0.5vw 0vw; }
        .detall_horaris_dia_festa { background-color: grey; }
        .detall_horaris_dia_A { text-decoration: underline; font-weight: bold; }
        .detall_horaris_dia_A:hover { cursor: pointer; }
        .detall_horaris_dia_NA { color: rgba(0,0,0,0.2); }
        .cartellLlocHora { padding: 1vw; width: 20vw; background-color: black; color: white; margin: 2vw auto; }
        
        .cal-info-text { margin-top: 2vw; }

        #detall_imatge_entitat > img { width: 20vw; }
        #Taula_Llistat_Cursos { width: 100%; border-collapse: collapse;  }
        #Taula_Llistat_Cursos td { padding: 0.5vw; border-bottom: 1px solid black;  }
        #Taula_Llistat_Cursos th { padding: 0.5vw; font-size: 1.5rem; font-weight: bold; border-bottom: 1px solid gray; }

    </style>

</head>
<body>
    
    <main id="detall" class="page">
          
        <!-- Possible, puc afegir que es mostrin altres cursos de l'entitat. -->        

        <show-errors style="padding-top:2vw;" v-if="Errors" :errors="WebStructure.Errors"></show-errors>
            
        <!-- LListat de cursos disponibles -->

        <section class="detall_bloc" v-if="Loaded && !Errors && LlistatActivitats">

            <div id="detall_imatge_entitat">                
                <img :src="DetallSite.SITES_LogoUrl" alt="Logo de l'entitat" />
            </div>

            <h1>{{DetallSite.SITES_Nom}}</h1>            

            <table id="Taula_Llistat_Cursos">                
                <tr>
                    <th>Activitat</th>
                    <th>Data</th>                    
                    <th>Hora</th>                    
                    <th>Lloc</th>                    
                </tr>

                <template v-for="A of LlistatActivitats">
                    <tr>                    
                        <td>                                                                    
                            <a style="cursor: pointer" @click="A.MostraDetall = !A.MostraDetall">{{A.NomActivitat}}</a>
                            <br /><span style="font-color: gray; font-size: 10px;">{{A.Organitzador}}</span>
                        </td>

                        <td>
                            <span v-if="A.Dia != A.DiaMax">
                                De <i>{{getDataFormatada(A.Dia, true)}}</i> a <i>{{getDataFormatada(A.DiaMax, true)}}</i> 
                            </span>
                            <span v-if="A.Dia == A.DiaMax">El {{getDataFormatada(A.Dia, true)}}</span>
                        </td>                    
                        
                        <td>{{A.HoraInici}}</td>                    

                        <td>{{A.NomEspai}}</td>                    
                    </tr>
                    <tr v-if="A.MostraDetall">
                        <td colspan="4">                            
                            <div v-html="A.DescripcioActivitat"></div>
                            <hr>
                            <div v-html="A.InfoPractica"></div>                                                        
                        </td>
                    </tr>

                </template>

                <tr v-if="LlistatActivitats.length == 0">
                    <td colspan="4">Actualment no hi ha cap activitat pública per consultar.</td>
                </tr>

            </table>            
            
        </section>  

        <div style="margin-bottom: 2vw">&nbsp;</div>
                
  </main>


  <script>
        var vm2 = new Vue({
        
            el: '#detall',        
            data: { 
                Loaded: false,
                Errors: false,
                WebStructure: <?php echo $Data ?>,                
                DetallActivitat: {},    //Objecte activitat                
                DetallSite: {},
                LlistatActivitats: null,  //Només apareix quan enviem el llistat dels cursos. Sinó apareix la resta                
                MostraDetall: false,         
                Horaris_i_llocs: '',
                Anys: [], 
                MesosAny: [],
                Dies: [],
                DiesMes: [],
                UrlActual: '',         //Url actual de la finestra
                MostroImatge: true

            },            
            filters: {
                DateSwap: function (date) {
                    const D = date.split('-');
                    return D[2] + '-' + D[1] + '-' + D[0];
                }
            },
            created: function() {
                if(this.WebStructure.Errors && this.WebStructure.Errors.length > 0) {
                    this.Loaded = true;                
                    this.Errors = true;                    
                } else {                                                                                           
                    
                    //Paràmetre usat per llistar cursos
                    if(this.WebStructure.Activitats) {                        
                        this.LlistatActivitats = this.WebStructure.Activitats;                        
                        this.LlistatActivitats.forEach(element => {
                            Vue.set(element, 'MostraDetall', false);  
                        });                        
                        this.DetallSite = this.WebStructure.Site;                        
                    }                    
                                     
                    this.UrlActual = window.location.href;  
                    this.Token = this.WebStructure.Token;                                          
                    this.Loaded = true;   

                }
                
            },
            computed: {},
            methods: {
                EsOberta: function(DataInici) {
                    let D = ConvertirData( DataInici, 'Javascript' );
                    DataActual = new Date();
                    return (DataActual > D);                    
                },
                getDataFormatada: function(DataInici, textFormat) {
                    return (!textFormat) ? ConvertirData( DataInici, 'TDM' ) : ConvertirData( DataInici, 'Text' );
                },
                onImgError: function() {
                    this.MostroImatge = false;
                }

            }
        });

    </script>

</body>
