<style>
        #home_requadres { width: 100%; padding: 2vw 2vw; }
        #home_requadres_nav {  justify-content: space-between; display: flex; flex-wrap: wrap; }    
        .home_requadres_nav_a { display: block; width: 13vw; height: 13vw; }
        .home_requadres_nav_a > span  { padding: 2vw; display: block; font-size:1.3rem; }
            
        #home_footer { height: auto; padding-bottom: 2vw; margin: 2vw 2vw; display: flex; background-color: black; justify-content: space-around; }    
        #home_footer > div {  color: white; font-size:1.5rem; margin-top: 2vw; margin-left: 3vw;  }
        #home_footer > div > p { color: white; font-size:1.5rem; line-height: 1.7rem; }
        #home_footer > nav {  display: flex; flex-direction: column; padding-top: 2vw; }
        #home_footer > nav > a { color: white; font-size:1.5rem; line-height: 1.7rem; }

        @media screen and (min-width: 300px) and (max-width: 1199px) {
            .home_requadres_nav_a { width: 17vw; height: 17vw; }        
        }


    </style>

</head>
<body>    
    <div id="home" class="page">

        <barra-superior></barra-superior>

        <banner-carrousel v-if="Loaded" :input-dades="WebStructure.Promocions" :input-menu="WebStructure.Menu" :with-title="true"></banner-carrousel>        

        <breadcumb-div v-if="false" :breadcumb-data = 'WebStructure.Breadcumb'></breadcumb-div>

        <filters-div v-if="Loaded"></filters-div>

        <noticies-carrousel v-if="Loaded" :input-dades="WebStructure.Noticies"></noticies-carrousel>

        <single-list v-if="Loaded && WebStructure.Cicles && WebStructure.Cicles.length > 0" 
            :input-titol="'DIVULGACIÓ'" 
            :input-color="'#F4A261'" 
            :input-dades="WebStructure.Cicles" 
            :gen-link="'/cicles/0/TotsElsCicles'"
            :amb-titol="true">
        </single-list>
        
        <single-list v-if="Loaded && WebStructure.Exposicions && WebStructure.Exposicions.length > 0" 
            :input-titol="'EXPOSICIONS'" 
            :input-color="'#C95E49'" 
            :input-dades="WebStructure.Exposicions" 
            :gen-link="'/activitats/categoria/46/Exposicions'"
            :amb-titol="true">
        </single-list>

        <single-list v-if="Loaded && WebStructure.Musica && WebStructure.Musica.length > 0" 
            :input-titol="'MÚSICA'" 
            :input-color="'#2A9D8F'" 
            :input-dades="WebStructure.Musica" 
            :gen-link="'/activitats/categoria/56/Musica'"
            :amb-titol="true">
        </single-list>

        <single-list v-if="Loaded && WebStructure.Petita && WebStructure.Petita.length > 0" 
            :input-titol="'PETITA CASA DE CULTURA'" 
            :input-color="'#cc6699'" 
            :input-dades="WebStructure.Petita" 
            :gen-link="'/activitats/categoria/59/PetitaCasaDeCultura'"
            :amb-titol="true">
        </single-list>

        <single-list v-if="Loaded" 
            :input-titol="'ALTRES ACTIVITATS'" 
            :input-color="'#E9C46A'" 
            :input-dades="WebStructure.ProperesActivitats" 
            :gen-link="'/activitats'"
            :amb-titol="true">
        </single-list>
    
        <div id="home_requadres">            
            <nav id="home_requadres_nav">
                <a class="home_requadres_nav_a" href="/pagina/168/cursos" style="background-color: #A0C3CB"> <span>Cursos</span> </a>
                <a class="home_requadres_nav_a" href="/pagina/141/reserva-d-espais" style="background-color: #B06A17"> <span>Reserva d'espais</span> </a>
                <a class="home_requadres_nav_a" href="/pagina/200/escoles" style="background-color: #E8D131"> <span>Programes<br/>per a escoles</span> </a>
                <a class="home_requadres_nav_a" href="/pagina/199/municipis" style="background-color: #9A7EB1"> <span>Recursos<br />per a muncipis</span> </a>
                <a class="home_requadres_nav_a" href="/pagina/137/la-casa" style="background-color: #9FB86B"> <span>La casa</span> </a>
            </nav>
        </div>        

        <footer id="home_footer">            
            
            <div>
                <p>Plaça de l'Hospital 6 <br/> 17002 Girona</p>
                <p>casacultura@ddgi.cat <br/> 972202013 </p> 
            </div>           
            <nav>
                <a href="/pagina/186/intranet-hospici">Intranet</a>
                <a href="https://seu.ddgi.cat/web/transparencia">Portal de transparència</a>                
                <a href="/pagina/140/tramits">Tràmits i espais</a>
                <a href="https://seu.ddgi.cat/web/nivell/755/s-1/avis-legal">Informació legal</a>                
            </nav> 
        </footer>


        <div style="margin-bottom: 2vw"> &nbsp; </div>

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
        
            el: '#home',        
            data: { 
                Loaded: false,  
                FormulariButlleti: false,          
                WebStructure: {}
            },            
            created: function() {
                this.Loaded = true;                        
                this.WebStructure = <?php echo $Data ?>;
                document.title = 'Casa de Cultura de Girona (Inici)';
            },
            computed: {},
            methods: {}
        });

    </script>

</body>
