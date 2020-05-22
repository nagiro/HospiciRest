

Vue.component('breadcumb-div', {
    props: {        
        BreadcumbData: Array         
    },          
    data: function() {
        return {  }
    },    
    computed: {
    },
    watch: {},
    methods: {
        getColor( Nivell ) {
            let TotalNivells = this.BreadcumbData.length - 1;
            switch (TotalNivells - Nivell) {
              case -1: return "#EEEEEE";
              case 0: return "#EEEEEE";
              case 1: return "#DDDDDD";
              case 2: return "#CCCCCC";
              case 3: return "#BBBBBB";
              case 4: return "#AAAAAA";
              case 5: return "#888888";
            }
        }        
    },
    template: `        

    <!-- REQUADRE VERTICAL -->
    
    <nav class="breadcumb">
        <a class="breadcumb_element" v-for="(B, index) of BreadcumbData" :href="B.Link" :style = "{'background-color': getColor(index)}">
            <div class="breadcumb_element_text"> <span>{{B.Titol}}</span></div> 
            <div class="breadcumb_element_fletxa" :style="{ 'border-left-color':  getColor(index) , 'border-top-color':  getColor(index+1), 'border-bottom-color':  getColor(index+1) }"></div>
        </a>
    </nav>

                `
});