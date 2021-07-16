    <style>
        #llistat { width: 100%; }
        #llistat_seccio_cicles { padding: 2vw; }
        #llistat_seccio_activitats { padding: 2vw; }
        .llistat_franja_titol { text-align: left; width: 100%; margin-bottom: 6vw; }    
        .llistat_titol { font-size: 3rem; margin-bottom: 2vw; }
        .llistat_dates { font-size: 1.5rem; }
        .llistat_summary_text { margin-top: 2vw; } 
        .llistat_requadre_detall {}  
        
    </style>
</head>

<body>

    <div id="llistat" class="page">
  
        <barra-superior></barra-superior>

        <banner-carrousel v-if="Loaded" :input-dades="WebStructure.Promocions" :input-menu="WebStructure.Menu" :with-title="true"></banner-carrousel> 

        <show-errors v-if="Errors" :errors="WebStructure.Errors"></show-errors>

        <breadcumb-div v-if="Loaded && !Errors" :breadcumb-data = 'WebStructure.Breadcumb'></breadcumb-div>

        <filters-div v-if="Loaded && !Errors"></filters-div>

        <!-- LLISTAT DE CICLES -->        

        <section id="llistat_seccio_cicles" v-if="Loaded  && !Errors">
        
            <h1 v-if="WebStructure.Cicles && WebStructure.Cicles.length > 0">Llistat de cicles</h1>

            <div v-for="ActivitatHome of WebStructure.Cicles">
                             
                <article class="llistat_franja_titol">
                    <h2 class="llistat_titol"> {{ ActivitatHome.NomActivitat }} </h2>
                    <time class="llistat_dates" v-html="ResumDates()"></time>
                    <a target="_NEW" v-if="ActivitatHome.tmp_PDF.length > 0" :href="ActivitatHome.tmp_PDF">[Baixa't el pdf]</a>                                    
                    <article class="llistat_summary_text" v-html="ActivitatHome.Descripcio"></article>
                
                            
                    <nav class="llistat_requadre_detall">                    
                        <single-list    :input-titol="''" 
                                        :input-color="'#FFFFFF'" 
                                        :input-dades="getActivitatsDelCicle( ActivitatHome.idCicle )"
                                        :amb-titol="false">
                        </single-list>
                    </nav>

                </article>
            </div>
            <div v-if="WebStructure.Cicles && WebStructure.Cicles.length == 0">
                <h2>Actualment no hi ha cap cicle per mostrar.</h2>
            </div>
        </section>                

        <!-- LLISTAT D'ACTIVITATS -->

        <section id="llistat_seccio_activitats" v-if="Loaded && !Errors && WebStructure.TipusActivitats">
            
            <h1 v-if="WebStructure.Activitats && WebStructure.Activitats.length > 0">Llistat d'activitats</h1>
                                         
            <article class="llistat_franja_titol">
                <!-- <h1 class="llistat_titol"> {{ WebStructure.FiltresAplicats[0].Text }} </h1> -->
                        
                <nav class="llistat_requadre_detall" v-if="WebStructure.Activitats.length > 0 ">                    
                    <single-list    :input-titol="''" 
                                    :input-color="'#FFFFFF'" 
                                    :input-dades="WebStructure.Activitats"
                                    :amb-titol="false">
                    </single-list>
                </nav>
                <h2 v-if="WebStructure.Activitats.length == 0"> No hi ha cap activitat per mostrar. </h2>

            </article>

        </section>                


        <!-- LLISTAT DE NODES -->
        <list-nodes v-if="Loaded && !Errors && WebStructure.Nodes" :input-titol="'Pàgines amb contingut relacionat'" :fills="WebStructure.Nodes"></list-nodes>

        <div style="margin-bottom: 2vw">&nbsp;</div>
                
  </div>

  <script>
        var vm2 = new Vue({
        
            el: '#llistat',        
            data: { 
                Loaded: false,
                Errors: false,
                WebStructure: <?php echo $Data ?>,
                LlistatAMostrar: [],
                MostraDetall: false,         
                Horaris_i_llocs: String,
                Anys: [], 
                MesosAny: [],
                Dies: []

            },            
            created: function() {       
                
                if(this.WebStructure.Errors && this.WebStructure.Errors.length > 0) {
                    this.Errors = true;
                    this.Loaded = true; 
                } else {
                    this.Errors = false;
                    this.Loaded = true;                                         
                }
            },
            computed: {},
            methods: {            
                getActivitatsDelCicle( idC ) {
                    let AF = this.WebStructure.Activitats.filter( ActivitatHome => { return ActivitatHome.idCicle == idC } );                
                    return AF;
                },
                ResumDates: function() {                
                }
            }
        });

    </script>

</body>
