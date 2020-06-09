
Vue.component('show-errors', {
    props: {
        Errors: Array
    },          
    data: function() { return {}},
    computed: {},
    watch: {},
    methods: {},
    template: `        
    
    <section>
        <article class="detall_requadre_error" v-for="E of Errors">
            <div class="text" v-html="E"></div>
        </article>
    </section>
    

                `,
});