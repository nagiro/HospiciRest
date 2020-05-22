Vue.component('noticies-carrousel', {        
    props: {        
        InputDades: Array                
    },          
    data: function() {
        return { TotesNoticies: [], Boletes: [], IndexNoticiaActual: 0 }
    },    
    computed: {
    },
    watch: {              
        InputDades: {
            deep: true,
            immediate: true,
            handler(NoticiesArray) {
                NoticiesArray.forEach((item, index) => {                
                    this.TotesNoticies.push(item);
                    this.Boletes.push(index);                                        
                });      
                
                this.VesANoticia( 0 );                                
                this.gMouCarrousel( 0 );
                
            }
        }
    },
    methods: {
        gMouCarrousel: function(index) {
            
            setTimeout(() => {                    
                index = ( index == this.TotesNoticies.length ) ? 0 : index;
                this.VesANoticia( index ); 
                index++;
                this.gMouCarrousel( index );
            }, 5000);
        },
        getClassBola: function( indexNoticia ) {
            if(indexNoticia == this.IndexNoticiaActual) {
                return 'fas fa-circle noticies_bola_blanca';
            } else {
                return 'fas fa-circle noticies_bola_gris';
            }            
        },
        VesANoticia: function(nouIndex) {                        
            if(nouIndex >= 0 && nouIndex < this.TotesNoticies.length) this.IndexNoticiaActual = nouIndex;
            else this.IndexNoticiaActual = 0;            
        }        

    },        
    template: `        

    <!-- REQUADRE VERTICAL -->
    <section class="noticies_section_style" v-if="TotesNoticies.length > 0">
                                
        <article class="noticies_requadre">        
            <h2 class="noticies_titol" v-html="TotesNoticies[ IndexNoticiaActual ].Titol"></h2>
            <div class="noticies_text" v-html="TotesNoticies[ IndexNoticiaActual ].Text"></div>
        </article>
    
        <ul class="noticies_grup_paginacio" v-if="TotesNoticies.length > 1">
            <li class="noticies_paginacio" v-for="(P, index) of TotesNoticies" >                
                <a @click="VesANoticia(index)" ><i :class="getClassBola(index)"></i></a>
            </li>
        </ul>
                        
    </section>


                `
});