<style>

        .detall_bloc { width: 100%; padding: 2vw; padding-top: 4vw; padding-left: 4vw; }        
        .detall_bloc h1, h2 { padding-bottom: 2vw; padding-top: 2vw; padding-right: 4vw;}
        /* .detall_bloc p { } */
        /* .detall_bloc ul { } */
        #detall_titol { padding-bottom: 4vw; }        
        /* #detall_descripcio {  } */
        
        #detall_imatge_entitat img { width: 20vw; margin-bottom: 3vw; }

        .Detall_Llistat_Imatges { display: flex; list-style: none; width: 100%; justify-content: left; flex-flow: wrap; }
        .Detall_Llistat_Imatges li { padding:2vw; }
        .Detall_Llistat_Imatges img { transition: transform .2s; width: 10vw; }
        .Detall_Llistat_Imatges img:hover { cursor: pointer;  }
        .Detall_Llistat_Imatges .imatge_gran { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) scale(5); z-index: 99; }
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
                <a v-for="Espai of LlistatEspais"  target="_new" :href="'/espais/detall/' + Espai.ESPAIS_EspaiId">
                    <span>{{Espai.ESPAIS_Nom}}</span>                    
                    <img :src="Espai.Imatges.ImatgesS[0]" />
                </a>
            </nav>
                            
        </section>  


        <!-- Petició -->
        <section class="detall_bloc" v-if="Loaded && !Errors && EspaiDetall">            
            
            <h1 id="detall_titol"> {{ EspaiDetall.ESPAIS_Nom }} </h1>            
            <h2>Descripció de l'espai</h2>
            <p>
                <ul class="Detall_Llistat_Imatges">
                    <li v-for="(I, index) of EspaiImatges.ImatgesL">
                        <img    v-if="( IndexImatgeVisible == index && getClassImatge == 'imatge_gran' ) || getClassImatge == 'imatge_petita' " 
                                :class="getClassImatge" 
                                @click="ClicaImatge(index)" :src="I" 
                        />
                    </li>
                </ul>
            </p>
            <p v-html = "EspaiDetall.ESPAIS_Descripcio">
                {{ EspaiDetall.ESPAIS_Descripcio }}
                {{DiaEscollit}}
            </p>
            <h2>Ocupació prevista</h2>
            <p> 

                <v-calendar @update:to-page="getOcupacio" :attributes="AtributsCalendari" locale="ca"></v-calendar>                                
            </p>            
                        
            <form-inscripcio-espai 
                :formdata = "FormulariReservaEspai"
                :estats = "FormulariReservaEspaiEstats"
                :espaisdisponibles = "LlistaEspaisDisponiblesForm"
            >
            </form-inscripcio-espai>

            
            
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
                DiaEscollit: null,     
                AtributsCalendari: [],    
                LlistaEspaisDisponiblesForm: [],   // Carrega quan carrega la pàgina en detall
                IdSite: 0, 
                IndexImatgeVisible: 0,

                DetallSite: {},
                LlistatEspais: null,  //Només apareix quan enviem el llistat dels cursos. Sinó apareix la resta                                                
                MostraDetall: false,                         
                UrlActual: '',         //Url actual de la finestra

                FormulariReservaEspai: {},
                FormulariReservaEspaiEstats: [],

            },            
            filters: {},
            created: function() {
                
                this.DiaEscollit = new Date();                

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
                        this.IdSite = this.DetallSite.SITES_SiteId;
                        
                        this.FormulariReservaEspai = this.WebStructure.FormulariReservaEspai.FORM;
                        this.FormulariReservaEspaiEstats = JSON.parse(this.WebStructure.FormulariReservaEspai.ESTATS);

                        this.LlistaEspaisDisponiblesForm = this.WebStructure.LlistaEspaisDisponiblesForm;                                             
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
                ClicaImatge(index) {
                    this.ImatgeGran = !this.ImatgeGran;
                    this.IndexImatgeVisible = index;
                },
                
                getOcupacio(DataCalendari) {
                    
                    this.MesActual = DataCalendari.month;
                    this.AnyActual = DataCalendari.year;                                                                                           

                    let FD = new FormData();
                    FD.append('MesActual', this.MesActual);
                    FD.append('AnyActual', this.AnyActual);
                    FD.append('IdEspai', this.EspaiDetall.ESPAIS_EspaiId);                                                            
                    FD.append('Accio', 'OcupacioEspai');                                                            
                    axios.post( CONST_api_web + '/ajaxReservaEspais', FD ).then( X => {
                        this.EspaiHorarisOcupats = X.data;                        
                        for(HO of this.EspaiHorarisOcupats){

                            let D = HO.HORARIS_Dia.split('-');                            
                            const HoraInici = String(HO.HORARIS_HoraPre).slice(0,-3);
                            const HoraFi = String(HO.HORARIS_HoraPost).slice(0,-3);
                            
                            this.AtributsCalendari.push(
                                { 
                                    key: String(HO.HORARIS_Dia) + String(HO.HORARIS_HoraPost), 
                                    dot: true, 
                                    popover: { 
                                        label: 'De ' + HoraInici + ' a ' + HoraFi
                                    }, 
                                    dates: new Date(D[0],(D[1]-1),D[2],0,0,0,0)
                                }
                            ); 

                        }                        

                    }).catch( E => { alert(E); });
                    

                    
                }
            }
        });        

    </script>

</body>
