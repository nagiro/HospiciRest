    <style>
        #detall_bloc { width: 100%; padding: 2vw; padding-top: 2vw; }
        #detall_franja_titol { text-align: center; width: 100%; }    
        #detall_dates { font-size: 1.5rem; }
        #detall_requadre_detall { border: 1px solid black; padding: 2vw; margin-top: 4vw; font-size: 1rem }        
        #detall_requadre_info { border: 1px solid black; padding: 2vw; margin-top: 2vw; font-size: 1rem; }
        #detall_bloc h1 { font-size: 3rem; margin-bottom: 2vw; margin-top: 2vw; }
        #detall_bloc h2 { font-size: 1.5rem; margin-bottom: 1vw; margin-top: 3vw; }
        #detall_bloc h3 { font-size: 1.2rem; margin-bottom: 1vw; margin-top: 3vw; }
        #detall_bloc td { padding: 1vw; border-bottom: 1px solid black; }
        #detall_bloc ul { list-style: outside; padding-left: 3vw; }
        #detall_bloc ol { list-style: decimal; padding-left: 3vw; }
        #detall_bloc li { margin-bottom: 1vw; }

    </style>

</head>

<body>

    <div id="pagina" class="page">    
  
        <barra-superior></barra-superior>

        <banner-carrousel v-if="Loaded" :input-dades="WebStructure.Promocions" :input-menu="WebStructure.Menu" :with-title="false"></banner-carrousel>  

        <breadcumb-div v-if="Loaded && !Errors" :breadcumb-data = 'WebStructure.Breadcumb'></breadcumb-div>

        <show-errors v-if="Errors" :errors="WebStructure.Errors"></show-errors>

        <section id="detall_bloc" v-if="Loaded && !Errors && WebStructure.Pagina.Nodes_Html && WebStructure.Pagina.Nodes_Html.length > 5">
            
            <article id="detall_requadre_detall">
                <div class="text" v-html="WebStructure.Pagina.Nodes_Html">  </div>
            </article>

        </section>                

        <list-nodes v-if="Loaded && !Errors" :fills="WebStructure.Fills"></list-nodes>

        <div style="margin-bottom: 2vw">&nbsp;</div>
                
  </div>

  <script>
        var vm2 = new Vue({
        
            el: '#pagina',        
            data: { 
                Loaded: false,
                Errors: false,
                WebStructure: <?php echo $Data ?>                            

            },            
            created: function() {
    
                if(this.WebStructure.Errors && this.WebStructure.Errors.length > 0) {
                    this.Errors = true;
                    this.Loaded = true;
                } else {
                    if(this.WebStructure.Pagina.Nodes_Url.length > 0) {                        
                        window.location.replace( this.WebStructure.Pagina.Nodes_Url );
                    } else {
                        this.Loaded = true;
                    }
                }
            },
            computed: {},
            methods: {}
        });

    </script>


</body>