Vue.component('single-list', {
    props: {
        InputTitol: String, 
        InputColor: String, 
        InputDades: Array,
        GenLink: String,
        AmbTitol: Boolean,        
    },          
    data: function() {
        return { MostroInfo: true, PaginaActual: 0, MaxPaginaActual: 0, TotesDadesPaginades: [], DadesFiltrades: [] }
    },    
    computed: {
    },
    watch: {              
        InputDades: {
            deep: true,
            immediate: true,
            handler(newVal) {
                this.TotesDadesPaginades = [];
                this.MaxPaginaActual = 0;
                this.PaginaActual = 0;
                newVal.forEach((item, index) => {                
                    const i = Math.floor((index) / this.gElementsPerPagina() );
                    if(!this.TotesDadesPaginades[i]) this.TotesDadesPaginades[i] = [];
                    this.TotesDadesPaginades[i].push(item);
                    this.MaxPaginaActual = i;                    
                });      
                this.getDadesActivitatsFiltrades();
            }
        }  
    },
    methods: {
        gElementsPerPagina: function() { return (this.AmbTitol) ? 3 : 3; },
        gQuantesActivitatsHaTrobat: function() {
            return (this.InputDades) ? this.InputDades.length : 0;
        },
        gFletxaQueMostro: function() {
            return (!this.MostroInfo) ? '/WebFiles/Web/img/FletxaAvallDesplegable.png' : '/WebFiles/Web/img/FletxaAmuntDesplegable.png';
        },
        getDadesActivitatsFiltrades: function($sumant = false) {            
                        
            if($sumant){
                let RetArray = [];
                for(let i = 0; i <= this.MaxPaginaActual; i++) {                    
                    RetArray = RetArray.concat(this.TotesDadesPaginades[i]);
                }                               
                this.DadesFiltrades = RetArray;                
            } else {
                this.DadesFiltrades = this.TotesDadesPaginades[this.PaginaActual];                
            }
            
        },
        NoExisteixImatge: function($event) {
            console.log('La Imatge no existeix' + $event);
        },
        PaginaEndavant: function($sumant = false) {            
            if (this.PaginaActual < this.MaxPaginaActual) {
                this.PaginaActual++;
                this.getDadesActivitatsFiltrades($sumant);                
              }
        },
        PaginaEnrrera: function() {            
            if (this.PaginaActual > 0) {
                this.PaginaActual--;
                this.getDadesActivitatsFiltrades();                
              }
        },        
        ChangeMostroInfo() {            
            this.MostroInfo = !this.MostroInfo;
        },
        ClassFletxa($Endavant) {                        
            if($Endavant){
              return (this.PaginaActual < this.MaxPaginaActual) ? 'SingleList_fletxa-endavant' : 'SingleList_fletxa-endavant-disabled';
            } else {
              return (this.PaginaActual > 0) ? 'SingleList_fletxa-enrrera' : 'SingleList_fletxa-enrrera-disabled';
            }    
          },
          SectionStyle: function() {
              if(this.AmbTitol) return 'SingleList_section_style'
              else return 'SingleList_section_style_list';
          },
        MostroBloc(PagActual) {
            return ((this.AmbTitol && this.PaginaActual == PagActual) || (!this.AmbTitol))
        }
    },
    template: `        

    <!-- REQUADRE VERTICAL -->
    <section :class="SectionStyle()">

        <div class="SingleList_requadre" :style="{ 'background-color': InputColor }">
            <h1  v-if="AmbTitol" class="SingleList_titol_box">        
                <a :href="GenLink">{{ InputTitol }}</a>        
            </h1>        
        
            <article class="SingleList_quadricula" v-if="gQuantesActivitatsHaTrobat == 0">
                <div class="SingleList_NoHiHaActivitatText">No hi ha cap activitat que concordi amb els filtres establerts.</div>
            </article>
        
            <nav class="SingleList_quadricula" v-if="!gQuantesActivitatsHaTrobat == 0">
                <div v-for="(LlistatActivitatsHome, PagActual) of TotesDadesPaginades" class="SingleList_ImatgeBlock" v-if="MostroBloc(PagActual)">
                    <div v-for="ActivitatHome of LlistatActivitatsHome" class="SingleList_Imatge">
                        <single-image :InputColor="InputColor" :InputDades="ActivitatHome" :InputTitol="InputTitol"></single-image>
                    </div>
                </div>
            </nav>
        
            <!-- Add Arrows -->
            <div class="SingleList_fletxa-avall" v-if=" (( PaginaActual < MaxPaginaActual ) && AmbTitol)">
                <a class="SingleList_fletxa" @click="PaginaEndavant(true)">Veure més activitats</a>            
            </div>
            <div v-if="AmbTitol" :class="ClassFletxa(false)" @click = "PaginaEnrrera()"></div>
            <div v-if="AmbTitol" :class="ClassFletxa(true)" @click = "PaginaEndavant()"></div>
        </div>
    </section>


                `
});