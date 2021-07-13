Vue.component('list-nodes', {
    props: {
        InputTitol: {type: String, default: 'Enllaços relacionats'}, 
        InputColor: {type: String, default: '#FFFFFF'}, 
        Fills: Array                
    },          
    data: function() {
        return { }
    },    
    computed: {},
    watch: {},
    methods: {
        getUrlImatge: function(fail = false, idNode, $eventFail) {
            if(!fail) return CONST_url_front_img + idNode + "-L.jpg";
            else event.target.src = "/WebFiles/Web/img/NoImage.jpg";
        },
        getLink: function(Node) {                
            return '/pagina/' + Node.Nodes_idNodes + '/' + normalize(Node.Nodes_TitolMenu);
        }
    },
    template: `        

        <!-- REQUADRE VERTICAL -->

        <section id="links_bloc" v-if="Fills.length > 0">

            <h1> {{InputTitol}} </h1>
            
            <nav class="links_requadre">
                <a :href="getLink(N)" v-for="N of Fills" class="link_enllac">
                    <img class="link_imatge" :src="getUrlImatge(false, N.Nodes_idNodes)" @error="getUrlImatge(true, 0, $event)" :alt="'Imatge de ' + N.Nodes_TitolMenu" />
                    <span class="link_text" href="">{{N.Nodes_TitolMenu}}</span>
                </a>
                
            </nav>

        </section>                

                `
});