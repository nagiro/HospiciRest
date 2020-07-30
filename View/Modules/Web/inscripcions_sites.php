<style>
        #detall_bloc { width: 100%; padding: 2vw; padding-top: 4vw; }
        #detall_franja_titol { text-align: center; width: 100%; display: block; cursor: default; }    
        #detall_dates > p { font-size: 1.5rem; }
        #detall_requadre_detall { border: 1px solid black; padding: 2vw; margin-top: 4vw; font-size: 1rem }        
        #detall_requadre_info { border: 1px solid black; padding: 2vw; margin-top: 2vw; font-size: 1rem; }
        #detall_bloc h1 { font-size: 3rem; margin-bottom: 2vw; margin-top: 4vw; }
        #detall_bloc h2 { font-size: 1.5rem; margin-bottom: 1vw; margin-top: 3vw; }
        #detall_bloc h3 { font-size: 1.3rem; margin-bottom: 1vw; margin-top: 3vw; }
            
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

        .detall_imatge_entitat {}

    </style>

</head>
<body>
    
    <div id="detall" class="page">
          
        <!-- Possible, puc afegir que es mostrin altres cursos de l'entitat. -->        

        <show-errors style="padding-top:2vw;" v-if="Errors" :errors="WebStructure.Errors"></show-errors>
            
        <!-- INSCRIPCIÓ -->
        <section id="detall_bloc" v-if="Loaded && !Errors && !DetallActivitat">
            <div id="detall_franja_titol">
                <h1 id="detall_titol"> {{ DetallCurs.CURSOS_TitolCurs }} </h1>                                
                <h2>Organitzat per {{ SiteNom }}</h2>
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
                
  </div>


  <script>
        var vm2 = new Vue({
        
            el: '#detall',        
            data: { 
                Loaded: false,
                Errors: false,
                WebStructure: <?php echo $Data ?>,                
                DetallActivitat: {},    //Objecte activitat
                DetallCurs: {},         //Objecte inscripció                
                DetallDescomptes: {},
                DetallTeatre: {},
                SiteNom: {},
                SeientsOcupats: [],
                MostraDetall: false,         
                Horaris_i_llocs: '',
                Anys: [], 
                MesosAny: [],
                Dies: [],
                DiesMes: [],
                UrlActual: ''         //Url actual de la finestra

            },            
            created: function() {
                if(this.WebStructure.Errors && this.WebStructure.Errors.length > 0) {
                    this.Loaded = true;                
                    this.Errors = true;                    
                } else {                    
                    this.Loaded = true;                    
                    this.DetallActivitat = (this.WebStructure.Activitat.length > 0) ? this.WebStructure.Activitat[0] : null                     
                    this.DetallCurs = (this.WebStructure.Curs.length > 0) ? this.WebStructure.Curs[0] : null;    
                    this.DetallDescomptes = this.WebStructure.Descomptes;            
                    this.DetallTeatre = this.WebStructure.Teatre;                                        
                    this.SiteNom = this.WebStructure.SiteNom;
                    this.SeientsOcupats = this.WebStructure.SeientsOcupats;
                    this.UrlActual = window.location.href;  
                    this.Token = this.WebStructure.Token;                                          
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
