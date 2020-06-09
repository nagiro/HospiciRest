    <style>
        #detall_bloc { width: 100%; padding: 2vw; padding-top: 2vw; }
        #detall_franja_titol { text-align: center; width: 100%; }    
        #detall_dates { font-size: 1.5rem; }
        #detall_requadre_detall { border: 1px solid black; padding: 2vw; margin-top: 4vw; font-size: 1rem }        
        #detall_requadre_info { border: 1px solid black; padding: 2vw; margin-top: 2vw; font-size: 1rem; }
        #detall_bloc h1 { font-size: 3rem; margin-bottom: 2vw; margin-top: 2vw; }
        #detall_bloc h2 { font-size: 1.5rem; margin-bottom: 1vw; margin-top: 3vw; }

    </style>

</head>

<body>

    <div id="pagina" class="page">
  
        <barra-superior></barra-superior>

        <banner-carrousel v-if="Loaded" :input-dades="WebStructure.Promocions" :input-menu="WebStructure.Menu" :with-title="false"></banner-carrousel>  

        <breadcumb-div v-if="Loaded && !Errors" :breadcumb-data = 'WebStructure.Breadcumb'></breadcumb-div>

        <show-errors v-if="Errors" :errors="WebStructure.Errors"></show-errors>

        <section id="detall_bloc" v-if="Loaded && !Errors">
            
            <article id="detall_requadre_detall">
                <div class="text" v-html="WebStructure.Pagina.Nodes_Html">  </div>
            </article>

        </section>                

        
        <list-nodes v-if="Loaded && !Errors" :fills="WebStructure.Fills"></list-nodes>
<!--
        <section id="links_bloc" v-if="WebStructure.Fills.length > 0">

            <h1> Enllaços relacionats </h1>
            
            <nav class="links_requadre">
                <a :href="getLink(N)" v-for="N of WebStructure.Fills" class="link_enllac">
                    <img class="link_imatge" :src="getUrlImatge(false, N.Nodes_idNodes)" @error="getUrlImatge(true, 0, $event)" />
                    <span class="link_text" href="">{{N.Nodes_TitolMenu}}</span>
                </a>
                
            </nav>

        </section>                
-->
        <div style="margin-bottom: 2vw">&nbsp;</div>
                


<!--

        <app-banners *ngIf="WebStructure.Mode.banners"></app-banners>
        <div *ngIf="WebStructure.Mode.banners">&nbsp;</div>

        <app-footer></app-footer>

        <div style="height:30px; clear:both;"></div>
    </div>
-->
  </div>

  <script>
        var vm2 = new Vue({
        
            el: '#pagina',        
            data: { 
                Loaded: false,
                WebStructure: <?php echo $Data ?>                            

            },            
            created: function() {
                if(this.WebStructure.Errors && this.WebStructure.Errors.length > 0) {
                    this.Errors = true;
                    this.Loaded = true;
                } else {
                    this.Loaded = true;
                }
            },
            computed: {},
            methods: {}
        });

    </script>


</body>
