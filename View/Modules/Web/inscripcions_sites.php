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

        <section class="detall_bloc" v-if="Loaded && !Errors && LlistatCursos">

            <div id="detall_imatge_entitat">                
                <img :src="DetallSite.SITES_LogoUrl" alt="Logo de l'entitat" />
            </div>

            <h1>{{DetallSite.SITES_Nom}}</h1>            

            <table id="Taula_Llistat_Cursos">                
                <tr>
                    <th>Activitat</th>
                    <th>Inici</th>
                    <th>Horaris</th>
                    <th>Preu</th>
                </tr>
                <tr v-for="Curs of LlistatCursos" >
                    <td><a target="_new" :href="'/inscripcions/' + Curs.CURSOS_IdCurs">{{Curs.CURSOS_TitolCurs}}</a></td>
                    <td>{{Curs.CURSOS_DataInMatricula | DateSwap }}</td>
                    <td>{{Curs.CURSOS_Horaris}}</td>
                    <td>{{Curs.CURSOS_Preu}}€</td>
                </tr>
                <tr v-if="LlistatCursos.length == 0">
                    <td colspan="4">Actualment no hi ha cap inscripció activa.</td>
                </tr>
            </table>            
            
        </section>  


        <!-- INSCRIPCIÓ -->
        <section class="detall_bloc" v-if="Loaded && !Errors && DetallCurs">            
            <div id="detall_franja_titol">
                <h1 id="detall_titol"> {{ DetallCurs.CURSOS_TitolCurs }} </h1>                                
                <h2>Organitzat per {{ DetallSite.SITES_Nom }}</h2>
                <h3>{{ DetallCurs.CURSOS_Horaris }}</h3>
                
            </div>
            <article id="detall_requadre_detall">            
                <h2 class="titol_text">DESCRIPCIÓ DE L'ACTIVITAT</h2>
                <p v-if="DetallCurs.CURSOS_Pdf.length > 0">
                    [<a :href="DetallCurs.CURSOS_Pdf" target="_NEW">{{NomEnllacPDFCurs}}</a>]
                </p>
                <div class="text" v-html="DetallCurs.CURSOS_Descripcio">  </div>                
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
            </article>
            
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
                DetallActivitat: {},    //Objecte activitat
                DetallCurs: null,         //Objecte inscripció                
                DetallDescomptes: {},
                DetallTeatre: {},
                DetallSite: {},
                LlistatCursos: null,  //Només apareix quan enviem el llistat dels cursos. Sinó apareix la resta                                                
                SeientsOcupats: [],
                MostraDetall: false,         
                Horaris_i_llocs: '',
                Anys: [], 
                MesosAny: [],
                Dies: [],
                DiesMes: [],
                UrlActual: ''         //Url actual de la finestra

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

                    //Paràmetre usat per mostrar un curs                    
                    if(this.WebStructure.Curs && this.WebStructure.Curs.length > 0) {
                        this.DetallCurs = this.WebStructure.Curs[0];    
                        this.DetallDescomptes = this.WebStructure.Descomptes;            
                        this.DetallTeatre = this.WebStructure.Teatre;                                                                
                        this.SeientsOcupats = this.WebStructure.SeientsOcupats;
                        this.DetallSite = this.WebStructure.Site;
                    }
                    
                    //Paràmetre usat per llistar cursos
                    if(this.WebStructure.LlistatCursos) {
                        this.LlistatCursos = this.WebStructure.LlistatCursos;                        
                        this.DetallSite = this.WebStructure.Site;                        
                    }                    
                                     
                    this.UrlActual = window.location.href;  
                    this.Token = this.WebStructure.Token;                                          
                    this.Loaded = true;   

                }
                
            },
            computed: {
                NomEnllacPDF: function() {
                    if(this.DetallActivitat) {
                        let Ret = " Descarrega el pdf ";
                        for(C of this.DetallActivitat.ACTIVITATS_Categories.split("@")) {

                            switch(C) {
                                case '56': Ret = " Descarrega el programa de mà "; break;
                                case '46': Ret = " Descarrega el catàleg "; break;                            
                            }
                            
                        }
                        return Ret;
                    }
                },
                NomEnllacPDFCurs: function() {
                    let Ret = " Descarrega el pdf ";
                    for(C of this.DetallCurs.CURSOS_Categoria) {

                        switch(C) {
                            case '56': Ret = " Descarrega el programa de mà "; break;
                            case '46': Ret = " Descarrega el catàleg "; break;                            
                        }
                        
                    }
                    return Ret;
                }                
            },
            methods: {}
        });

    </script>

</body>
