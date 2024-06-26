
Vue.component('single-image', {
    props: {        
        InputColor: String, 
        InputTitol: String,
        InputDades: Object        
    },          
    data: function() {
        return { ActivitatHome: {} }
    },    
    computed: {
    },
    watch: {              
        InputDades: {
            deep: true,
            immediate: true,
            handler(newVal) {
                this.ActivitatHome = newVal;
            }
        }  
    },
    methods: {
        getTagStyle: function() {
            let R = parseInt(parseInt(this.InputColor.substring(1, 3), 16).toString(10)) + 20;
            let G = parseInt(parseInt(this.InputColor.substring(3, 5), 16).toString(10)) + 20;
            let B = parseInt(parseInt(this.InputColor.substring(5, 7), 16).toString(10)) + 20;
            let NewColor = '#' + R.toString(16) + G.toString(16) + B.toString(16);
        
            let Style = {};
            Style['background-color'] = this.InputColor;
            Style['border-left-color'] = NewColor;
            Style['border-bottom-color'] = NewColor;
            Style['border-top-color'] = this.InputColor;
            Style['border-right-color'] = this.InputColor;
        
            return Style;
        },
        gUrlImatgeCategoriaVinculadaBlanca: function(ActivitatHome) {

            if (ActivitatHome.CategoriaVinculada) {
                return '/WebFiles/Web/img/TipusActivitats/B' + ActivitatHome.CategoriaVinculada.toString() + '.png';
              //  this.UrlImatgeCategoriaVinculadaNegra = '/WebFiles/Web/img/TipusActivitats/N' + this.CategoriaVinculada.toString() + '.png';
              } else {
                return '/WebFiles/Web/img/TipusActivitats/B0.png';
//                this.UrlImatgeCategoriaVinculadaNegra = '/WebFiles/Web/img/TipusActivitats/N0.png';
              }            
        },
        gURLImatge: function( ActivitatHome ) {
            if ( ActivitatHome.idActivitat > 0) {
                return CONST_url_activitats_img + ActivitatHome.idActivitat + '-L.jpg';                
            } else {                
                return CONST_url_cicles_img + ActivitatHome.idCicle + '-L.jpg';                
            }
        },
        getUrl: function( ActivitatHome ) {            
            if( ActivitatHome.idActivitat == 0 ) { return '/cicles/' + ActivitatHome.idCicle + '/' + normalize(ActivitatHome.NomActivitat); }
            else if( ActivitatHome.idActivitat > 0 ) { return '/detall/' + ActivitatHome.idActivitat + '/' + normalize(ActivitatHome.NomActivitat); }
        },
        gTextDies: function( ActivitatHome ) {
            if( ActivitatHome.Dia == ActivitatHome.DiaMax ) {
                return "El " + ConvertirData(ActivitatHome.Dia, 'TDM'); 
            } else {
                return "Del " + ConvertirData(ActivitatHome.Dia, 'TDM') + ' al ' + ConvertirData(ActivitatHome.DiaMax, 'TDM'); 
            }            
        },
        gTextHores: function( ActivitatHome ) {            
            if(this.InputTitol == 'EXPOSICIONS') {
                return "De " + ConvertirHora(ActivitatHome.HoraInici, 'THM') + ' a ' + ConvertirHora(ActivitatHome.HoraFi, 'THM');
            } else {
                return "A les " + ConvertirHora(ActivitatHome.HoraInici, 'THM') + 'h';
            }
        },        
        NoExisteixImatge: function($event) {
            $event.target.style = 'background-color: black;';
            $event.target.src = '/WebFiles/Web/img/NoImage.jpg';
        },
        goTo: function($Where) {
            console.log("GoTo");
            window.location.href = this.getUrl( $Where );
        }
    },
    template: `        

    <!-- REQUADRE VERTICAL -->
    
    <div @click="goTo(ActivitatHome)" class="SingleImage_requadre_imatge" >
        <div class="SingleImage_tag" v-if="ActivitatHome.CategoriaVinculada != '0'" > <!-- :style="getTagStyle( ActivitatHome )" -->
            <i class="fas fa-bookmark"></i>
            <img :src="gUrlImatgeCategoriaVinculadaBlanca( ActivitatHome )" alt="Tag de categoria" />
        </div>
        <img :src="gURLImatge( ActivitatHome )" class="SingleImage_requadre_imatge_img" @error="NoExisteixImatge($event)" :alt="ActivitatHome.NomActivitat.substring(0,100)" />

        <div class="SingleImage_requadre_text">
            <p class="SingleImage_requadre_text_titol"><a :href="getUrl( ActivitatHome )">{{ ActivitatHome.NomActivitat }}</a></p>
            <div class="SingleImage_requadre_text_data">
                <div class="SingleImage_requadre_text_data_calendari">
                    <i class="far fa-calendar"></i> {{ gTextDies( ActivitatHome ) }}
                </div>
            </div>
            <div class="SingleImage_requadre_text_espai">
                <div class="SingleImage_requadre_text_data_horari">
                    <i class="far fa-clock"></i> {{ gTextHores( ActivitatHome ) }}
                </div>
            </div>
        </div>
    </div>    

                `
});