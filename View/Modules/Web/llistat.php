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

        <breadcumb-div v-if="Loaded" :breadcumb-data = 'WebStructure.Breadcumb'></breadcumb-div>

        <filters-div v-if="Loaded"></filters-div>

        <!-- LLISTAT DE CICLES -->

        <section id="llistat_seccio_cicles" v-if="WebStructure.Cicles">

            <div v-for="ActivitatHome of WebStructure.Cicles">
                             
                <article class="llistat_franja_titol">
                    <h1 class="llistat_titol"> {{ ActivitatHome.NomActivitat }} </h1>
                    <time class="llistat_dates" v-html="ResumDates()"></time>                
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
        </section>                

        <!-- LLISTAT D'ACTIVITATS -->

        <section id="llistat_seccio_activitats" v-if="WebStructure.TipusActivitats">
                             
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
        <list-nodes v-if="WebStructure.Nodes" :input-titol="'Pàgines amb contingut relacionat'" :fills="WebStructure.Nodes"></list-nodes>

        <div style="margin-bottom: 2vw">&nbsp;</div>
                
  </div>

  <script>
        var vm2 = new Vue({
        
            el: '#llistat',        
            data: { 
                Loaded: true,
                WebStructure: <?php echo $Data ?>,
                LlistatAMostrar: [],
                MostraDetall: false,         
                Horaris_i_llocs: String,
                Anys: [], 
                MesosAny: [],
                Dies: []

            },            
            created: function() {            
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
