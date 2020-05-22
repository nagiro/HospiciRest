Vue.component('banner-carrousel', {        
    props: {        
        InputDades: Array,
        InputMenu: Array,
        WithTitle: Boolean        
    },          
    data: function() {
        return { BannerImageID: "bc_banner_image", TextBannerID: 'bc_text_banner', TotesPromocions: [], Boletes: [], PromocioActual: {}, IndexPromocioActual: 0, Menu: [], MenuObert: false }
    },    
    computed: {
    },
    watch: {              
        InputDades: {
            deep: true,
            immediate: true,
            handler(PromocionsArray) {
                PromocionsArray.forEach((item, index) => {                
                    this.TotesPromocions.push(item);
                    this.Boletes.push(index);                                        
                });      
                                               
                this.VesAPromocio( 0 );                                
                this.gMouCarrousel( 0 );
                
            }
        },
        InputMenu: {
            deep: true,
            immediate: true,
            handler(MenuArray) {

                MenuArray.forEach((item, index) => {
                    let ReturnMenu = {'Node':{}, 'Fills': [], 'Obert': false};
                    if(item.Nodes_idPare == null) {
                        this.Menu.push(this.gDoMenu(item, MenuArray));                        
                    }                                    
                });      
                                               
                this.VesAPromocio( 0 );                                
                this.gMouCarrousel( 0 );
                
            }
        }          
    },
    methods: {
        gDoMenu: function(Node, Menu) {
            let ReturnMenu = {'Node':{}, 'Fills': [], 'Obert': false};
            ReturnMenu.Node = Node;
            Menu.forEach((item, index) => {                
                if(item.Nodes_idPare == Node.Nodes_idNodes){
                    const B = this.gDoMenu(item, Menu);                    
                    ReturnMenu.Fills.push(B);
                }
            });
            return ReturnMenu;            
        },
        gMouCarrousel: function(index) {
            
            setTimeout(() => {                    
                index = ( index == this.TotesPromocions.length ) ? 0 : index;
                this.VesAPromocio( index ); 
                index++;
                this.gMouCarrousel( index );
            }, 5000);

        },
        gURLImatge: function( PromocioHome ) {
            
            if ( PromocioHome && PromocioHome.PROMOCIONS_IMATGE_L && PromocioHome.PROMOCIONS_IMATGE_L.length > 0) {
                let UrlArray = PromocioHome.PROMOCIONS_IMATGE_L.split('/');
//                UrlArray.splice(0,3);     // Trec el domini del servidor                                           
                return UrlArray.join('/'); 
            } else {                
                return null;                
            }
        },
        NoExisteixImatge: function($event) {                        
            $event.target.src = '/WebFiles/Web/img/NoImage.jpg';
            if(this.TotesPromocions.length == 1) {
                this.BannerImageID = 'bc_banner_image_small';            
                this.TextBannerID = 'bc_text_banner_small';
            } else {
                this.BannerImageID = 'bc_banner_image_no_image';
            }   
        },        
        getClassBola: function( indexPromocio ) {
            if(indexPromocio == this.IndexPromocioActual) {
                return 'fas fa-circle bc_bola_blanca';
            } else {
                return 'fas fa-circle bc_bola_gris';
            }            
        },
        VesAPromocio: function(nouIndex) {            
            if(nouIndex >= 0) this.IndexPromocioActual = nouIndex;
            this.PromocioActual = (this.TotesPromocions.length >= nouIndex) ? this.TotesPromocions[ this.IndexPromocioActual ] : { 'NoPromocions': true };              
        },
        IsVisible: function( index ) {
            if( index != this.IndexPromocioActual ) return 'display: none';
            else return '';
        },
        ClickMenu: function($Obert = null) {           
            
            if($Obert === null) this.MenuObert = !this.MenuObert;
            else this.MenuObert = false;                        
            
        },
        gUrlLink: function( PromocioHome ) {

        },
        ObreMenu: function( N, IsLink = false ) {            
            if( IsLink ){
                if( N.Node.Nodes_TitolMenu.length > 0) return '/pagina/' + N.Node.Nodes_idNodes + '/' + normalize(N.Node.Nodes_TitolMenu);
                else return '/';
            } else {
                if(N.Fills.length == 0) location.href = '/pagina/' + N.Node.Nodes_idNodes + '/' + normalize(N.Node.Nodes_TitolMenu);
                else N.Obert = !N.Obert;                
            }
        },
        IconaEnllac: function(Node) {
            if( Node.Fills.length == 0 ) return 'fas fa-angle-double-right';
            else return 'fas fa-plus-square';
        }

    },
    events: {
        nameOfCustomEventToCall: function (event) {                 
        }
    },
    template: `        

    <!-- REQUADRE VERTICAL -->
    <section class="bc_section_style">
        <div class="bc_carrousel" @mouseleave="ClickMenu(false)">
            <button class="bc_menu_button"                     
                    @click="ClickMenu()"                                        
            >
                <i class="bc_img_menu fas fa-bars"></i>
                <div class="bc_text_menu">menu</div>
            </button>

            <div class="bc_menu" v-if="MenuObert">
                <a class="bc_close_menu" @click="MenuObert = false">
                    <i class="fas fa-times"></i>
                </a>

                <div class="bc_menu_text">
                    <ul class="bc_menu_primer_nivell">
                        <li v-for="M0 of Menu">                        
                            
                            <a  :href="ObreMenu(M0, true)" 
                                class="bc_menu_enllac" 
                                @click.prevent="ObreMenu(M0, false)"> 
                                <i :class="IconaEnllac(M0)"></i>&nbsp; {{M0.Node.Nodes_TitolMenu}} 
                            </a>    
                                                        

                            <ul class="bc_menu_altres_nivells">
                                <li v-for=" M1 of M0.Fills" :hidden="!M0.Obert">
                                    <a  :href="ObreMenu(M1, true)" 
                                        class="bc_menu_enllac" 
                                        @click.prevent="ObreMenu(M1, false)"> 
                                        <i :class="IconaEnllac(M1)"></i>&nbsp; {{M1.Node.Nodes_TitolMenu}} 
                                    </a>    
                                    
                                    <ul>
                                        <li v-for="M2 of M1.Fills" :hidden="!M1.Obert">
                                            <a  :href="ObreMenu(M2, true)" 
                                                class="bc_menu_enllac" 
                                                @click.prevent="ObreMenu(M2, false)"> 
                                                <i :class="IconaEnllac(M2)"></i>&nbsp; {{M2.Node.Nodes_TitolMenu}} 
                                            </a>    
                                        
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
                    
            <img src="/WebFiles/Web/img/LogoCCG.jpg" class="bc_img_logo" alt="Logo de la Casa de Cultura" />            

            <div class="bc_roller" v-for="(PromocioHome, index) of TotesPromocions" :style="IsVisible(index)">
                
                <img :class="BannerImageID" :src="gURLImatge(PromocioHome)" @error="NoExisteixImatge($event)" :alt="PromocioHome.PROMOCIONS_TITOL" />
                <a href="gUrlLink(PromocioHome)" :class="TextBannerID">
                    <h1 v-if="WithTitle" class="bc_text_banner_titol"> {{ PromocioHome.PROMOCIONS_TITOL }} </h1>
                    <h2 v-if="WithTitle" class="bc_text_banner_subtitol"> {{ PromocioHome.PROMOCIONS_SUBTITOL }} </h2>
                </a>

            </div>
                
            <ul class="bc_grup_paginacio" v-if="TotesPromocions.length > 1">
                <li class="paginacio" v-for="(P, index) of TotesPromocions" >
                    <i :class="getClassBola(index)"></i>
                </li>
            </ul>
        </div>
    </section>


                `
});