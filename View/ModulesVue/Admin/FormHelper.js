
Vue.component('form-helper', {
    props: {        
        Formulari: Object       // Entra un objecte tipus FormulariClass.php        
    },          
    data: function() { 
        return { 
            
        }
    },
    computed: {},
    watch: {},
    methods: {},
    template: `        
    <div>
        <input-helper        
        v-if="Formulari.Tipus == 'input-helper'"
        :titol = "Formulari.Titol"
        :valor-defecte = "Formulari.ValorDefecte"
        :id = "Formulari.Id"
        @onchange = "Formulari.Id = $event"
        ></input-helper>

        <select-helper             
        v-else-if="Formulari.Tipus == 'select-helper'"
        :titol = "Formulari.Titol"
        :valor-defecte = "Formulari.ValorDefecte"
        :id = "Formulari.Id"
        :options = "Formulari.Options"
        @onchange = "Formulari.Id = $event"                
        ></select-helper>

        <image-helper-cropper
            v-else-if="Formulari.Tipus == 'image-helper-cropper'"
            :accio-esborra = "Formulari.Imatge.Accio_Esborra"
            :accio-guarda="Formulari.Imatge.Accio_Guarda"
            :id-element = "Formulari.Imatge.Imatge_Id_Element"
            :mida-imatge = "Formulari.Imatge.Mida"
            :url-a-mostrar = "Formulari.Imatge.Url_a_mostrar"            
            :titol = "Formulari.Titol"
            @update = "Formulari.Id = $event"
        ></image-helper-cropper>


    </div>
                `,
});