<style>
        .detall_bloc { width: 100%; padding: 2vw; padding-top: 4vw; }        
        #detall_titol { padding-bottom: 4vw; }        
        #detall_descripcio {  }
        
        .Detall_Llistat_Imatges { display: flex; list-style: none; width: 100%; justify-content: left; flex-flow: wrap; }
        .Detall_Llistat_Imatges li { padding:2vw; }
        .Detall_Llistat_Imatges img { transition: transform .2s; width: 10vw; }
        .Detall_Llistat_Imatges .imatge_gran {  transform: scale(5); position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); }
        .Detall_Llistat_Imatges .imatge_petita {  transform: scale(1);  }

        .LlistatHoritzontal { display: flex; list-style: none; width: 100%; justify-content: space-between; flex-flow: wrap; }
        .LlistatHoritzontal a { display: inline-block; padding: 2vw; font-size: 2rem; }
        .LlistatHoritzontal img { width: 20vw; height: 20vw; }
        .LlistatHoritzontal span { display:block; width: 20vw; margin-bottom: 1vw; }

        @media only screen and (max-width: 800px) {                        
            
            .LlistatHoritzontal img { width: 40vw; height: 40vw; }
        }

    </style>

</head>
<body>
    
    <main id="detall" class="page">
                  
        <show-errors style="padding-top:2vw;" v-if="Errors" :errors="WebStructure.Errors"></show-errors>
            
        <!-- LListat de cursos disponibles -->

        <section class="detall_bloc" v-if="Loaded && !Errors && LlistatEspais">

            <div id="detall_imatge_entitat">                
                <img :src="DetallSite.SITES_LogoUrl" alt="Logo de l'entitat" />
            </div>

            <h1>Espais disponibles de {{DetallSite.SITES_Nom}}</h1>                        

            <nav class="LlistatHoritzontal">                                    
                <a  v-for="Espai of LlistatEspais"  target="_new" :href="'/espais/detall/' + Espai.ESPAIS_EspaiId">
                    <span>{{Espai.ESPAIS_Nom}}</span>                    
                    <img :src="Espai.Imatges.ImatgesS[0]" />
                </a>                                
            </nav>
                            
        </section>  


        <!-- Petició -->
        <section class="detall_bloc" v-if="Loaded && !Errors && EspaiDetall">            
            
            <h1 id="detall_titol"> {{ EspaiDetall.ESPAIS_Nom }} </h1>
            <ul class="Detall_Llistat_Imatges">
                <li v-for="(I, index) of EspaiImatges.ImatgesL">
                    <img :class="getClassImatge" @click="ClicaImatge()" :src="I" />                    
                </li>
            </ul>
            <p id="detall_descripcio" v-html = "EspaiDetall.ESPAIS_Descripcio">
                {{ EspaiDetall.ESPAIS_Descripcio }}
            </p>    
                <button @click="getOcupacio('-')"> <  </button><button @click="getOcupacio('=')">Mes actual</button><button @click="getOcupacio('+')"> > </button>
                <div v-for="EHO of EspaiHorarisOcupats">
                    El dia {{EHO.HORARIS_Dia}} de {{EHO.HORARIS_HoraPre}} a {{EHO.HORARIS_HoraPost}}                    
                    <v-calendar></v-calendar>
                    <!-- <v-date-picker v-model='selectedDate' /> -->
                </div>
                //Aquí hi va l'ocupació de l'espai
            </p>
            

            <div>
                AQUÍ HI VA EL FORMULARI
            </div>            
<!--                
            <p>
            <div v-if="DetallCurs">
                <form-inscripcio-simple 
                    :activitat-id="'0'" 
                    :cicle-id="'0'"
                    :curs-id = "DetallCurs.CURSOS_IdCurs"
                    :detall-curs = "DetallCurs"
                    :detall-descomptes = "DetallDescomptes"
                    :detall-teatre = "DetallTeatre"
                    :detall-site = "DetallSite"
                    :seients-ocupats = "SeientsOcupats"
                    :url-actual = "UrlActual"
                    :token = "Token"                        
                >
                </form-inscripcio-simple>
            </div>

            </p>
-->                                          
            
        </section>        

        <!-- Aquí hi va el llistat amb altres cursos disponibles per l'entitat -->
        <!-- <single-list v-if="Loaded && !Errors && DetallActivitat" :input-titol="'ACTIVITATS RELACIONADES'" :input-color="'#F4A261'" :input-dades="WebStructure.ActivitatsRelacionades" :amb-titol="true"></single-list> -->

        <div style="margin-bottom: 2vw">&nbsp;</div>
                
  </main>


  <script>
        var vm2 = new Vue({
        
            el: '#detall',        
            data: { 
                Loaded: false,
                Errors: false,
                WebStructure: <?php echo $Data ?>,                                
                EspaiDetall: null,         //Objecte inscripció                                                
                EspaiHorarisOcupats: [],
                EspaiImatges: [],
                ImatgeGran: false,  
                MesActual: 0,  
                AnyActual: 0,           

                DetallSite: {},
                LlistatEspais: null,  //Només apareix quan enviem el llistat dels cursos. Sinó apareix la resta                                                
                MostraDetall: false,                         
                UrlActual: ''         //Url actual de la finestra

            },            
            filters: {},
            created: function() {
                console.log(window);
                if(this.WebStructure.Errors && this.WebStructure.Errors.length > 0) {

                    this.Loaded = true;                
                    this.Errors = true;                    

                } else {                                                                       
                    
                    //Paràmetre usat per llistar espais                    
                    if(this.WebStructure.EspaisDisponibles && this.WebStructure.EspaisDisponibles.length > 0) {
                        this.LlistatEspais = this.WebStructure.EspaisDisponibles;
                        this.DetallSite = this.WebStructure.Site;                        
                    }                    
                                     

                    //Paràmetre usat per llistar espais             
                    
                    if(this.WebStructure.EspaiDetall && this.WebStructure.EspaiDetall.Detall) {
                        this.EspaiDetall = this.WebStructure.EspaiDetall.Detall;                                                
                        this.EspaiImatges = this.WebStructure.EspaiDetall.Imatges;                        
                        this.DetallSite = this.WebStructure.Site;                                                                        
                    }                    

                    this.UrlActual = window.location.href;  
                    this.Token = this.WebStructure.Token;                                          
                    this.Loaded = true;   

                }
                

            },
            computed: {
                getClassImatge() {
                    if(this.ImatgeGran) return 'imatge_gran';
                    return 'imatge_petita';
                }
            },
            methods: {
                ClicaImatge() {
                    this.ImatgeGran = !this.ImatgeGran;
                },
                // Accio = '-', '+', '='
                getOcupacio(Accio) {
                    
                    switch(Accio) {
                        case '=': 
                            let $D = new Date();        
                            this.MesActual = $D.getMonth()+1;
                            this.AnyActual = $D.getFullYear();                            
                        break;
                        case '-':
                            this.MesActual--;
                            if(this.MesActual == 0){ this.MesActual = 12; this.AnyActual--; }
                        break;
                        case '+':
                            this.MesActual++;
                            if(this.MesActual == 13){ this.MesActual = 1; this.AnyActual++; }
                        break;
                    }

                    console.log(this.EspaiDetall);

                    let FD = new FormData();
                    FD.append('MesActual', this.MesActual);
                    FD.append('AnyActual', this.AnyActual);
                    FD.append('IdEspai', this.EspaiDetall.ESPAIS_EspaiId);                                                            
                    FD.append('Accio', 'OcupacioEspai');                                                            
                    axios.post( CONST_api_web + '/ajaxReservaEspais', FD ).then( X => {
                        this.EspaiHorarisOcupats = X.data;
                    }).catch( E => { alert(E); });

                    //Consulto el mes escollit      

                    
                }
            }
        });        

    </script>

</body>
