Vue.component('banner-carrousel', {        
    props: {        
        InputDades: Array,
        InputMenu: Array,
        WithTitle: Boolean        
    },          
    data: function() {
        return { TotesPromocions: [], Boletes: [], PromocioActual: {}, IndexPromocioActual: 1, Menu: [], MenuObert: false }
    },    
    computed: {
    },
    watch: {              
        InputDades: {
            deep: true,
            immediate: true,
            handler(PromocionsArray) {
                PromocionsArray.forEach((item, index) => {                
                    item['ImageUrl'] = this.gURLImatge(item);
                    item['LinkPromocio'] = this.gUrlLink(item);                    
                    item['BannerImageID'] = this.setImageStyle(item);
                    item['VisibleBackground'] = this.getBackground(item);
                    item['TextBannerID'] = 'bc_text_banner';
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
        setImageStyle: function(PromocioActual) {                            
            
            PromocioActual.BannerImageID = 'bc_banner_image';

            let cssid = PromocioActual.BannerImageID.split('_');
            let bannerid = PromocioActual.BannerImageID;
                        
            if(this.WithTitle) { return (cssid[cssid.length - 1] != 'o') ? bannerid + '_o' : bannerid ; }
            else { return (cssid[cssid.length - 1] != 'o') ? bannerid : cssid.pop().join('_'); } 

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
        NoExisteixImatge: function($event, IndexPromoActual) {                        
            
            let img = '/WebFiles/Web/img/NoImage.jpg';
            $event.target.src = img;
            Vue.set(this.TotesPromocions[IndexPromoActual], 'ImageUrl', img);
            
            if(this.TotesPromocions.length == 1) {            
                Vue.set(this.TotesPromocions[IndexPromoActual], 'BannerImageID', 'bc_banner_image_small');            
                Vue.set(this.TotesPromocions[IndexPromoActual], 'TextBannerID', 'bc_text_banner_small');
            } else {
                Vue.set(this.TotesPromocions[IndexPromoActual], 'BannerImageID', 'bc_banner_image_no_image');
            }   
            Vue.set(this.TotesPromocions[IndexPromoActual], 'VisibleBackground', this.getBackground(this.TotesPromocions[IndexPromoActual]));
            Vue.set(this.TotesPromocions, IndexPromoActual, this.TotesPromocions[IndexPromoActual]);
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
        getBackground: function( PromocioIndex ) { 
            return { 'background-image': 'url(' + PromocioIndex.ImageUrl + ')' };                         
        },
        ClickMenu: function($Obert = null) {           
            
            if($Obert === null) this.MenuObert = !this.MenuObert;
            else this.MenuObert = false;                        
            
        },
        gUrlLink: function( PromocioHome ) {
            return (PromocioHome.PROMOCIONS_URL) ? PromocioHome.PROMOCIONS_URL : '#';
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
                <div class="bc_text_menu">menú</div>
            </button>

            <div class="bc_menu" v-show="MenuObert">

                <div class="bc_menu_text">
                    <ul  v-if="Menu.length > 0" class="bc_menu_primer_nivell">
                        <li v-for="M0 of Menu">                        
                            
                            <a  :href="ObreMenu(M0, true)" 
                                class="bc_menu_enllac" 
                                @click.prevent="ObreMenu(M0, false)"> 
                                <i :class="IconaEnllac(M0)"></i>&nbsp; {{M0.Node.Nodes_TitolMenu}} 
                            </a>    
                                                        
                            <ul v-if="M0.Fills.length > 0" class="bc_menu_altres_nivells">
                                <li v-for=" M1 of M0.Fills" :hidden="!M0.Obert">
                                    <a  :href="ObreMenu(M1, true)" 
                                        class="bc_menu_enllac" 
                                        @click.prevent="ObreMenu(M1, false)"> 
                                        <i :class="IconaEnllac(M1)"></i>&nbsp; {{M1.Node.Nodes_TitolMenu}} 
                                    </a>    
                                    
                                    <ul  v-if="M1.Fills.length > 0">
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

                <a class="bc_close_menu" @click="MenuObert = false">
                    <i class="fas fa-times"></i>
                </a>

            </div>
                    
            <a class="bc_img_logo_anchor" href="/" aria="Enllaç a inici">
                <img src="/WebFiles/Web/img/LogoCCG.jpg" class="bc_img_logo" alt="Logo de la Casa de Cultura" height="50%" style="margin-top: 20px;" />
            </a>           

            <div class="bc_roller" v-for="(PromocioHome, index) of TotesPromocions">

                <img style="display: none" :src="PromocioHome.ImageUrl" @error="NoExisteixImatge($event, index)" :alt="PromocioHome.PROMOCIONS_TITOL" />

                <div v-show="index == IndexPromocioActual" :style="PromocioHome.VisibleBackground" :class="PromocioHome.BannerImageID">
                    
                    <a v-if="WithTitle" :href="PromocioHome.LinkPromocio" :class="PromocioHome.TextBannerID">                        
                        <h1 v-if="WithTitle" class="bc_text_banner_titol"> {{ PromocioHome.PROMOCIONS_TITOL }} </h1>
                        <h2 v-if="WithTitle" class="bc_text_banner_subtitol"> {{ PromocioHome.PROMOCIONS_SUBTITOL }} </h2>                        
                    </a>
                </div>
            </div>
                
            <ul class="bc_grup_paginacio" v-show="TotesPromocions.length > 1">
                <li class="paginacio" v-for="(P, index) of TotesPromocions" >
                    <i :class="getClassBola(index)"></i>
                </li>
            </ul>
        </div>
    </section>


                `
});