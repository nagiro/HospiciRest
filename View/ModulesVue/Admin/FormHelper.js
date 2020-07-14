
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
    methods: {
        onchange: function($event) {
            this.$emit('onchange', $event);
        }
    },
    template: `        
    <div>
        <input-helper        
        v-if="Formulari.Tipus == 'input-helper'"
        :titol = "Formulari.Titol"
        :valor-defecte = "Formulari.ValorDefecte"
        :id = "Formulari.Id"
        @onchange = "onchange($event)"
        ></input-helper>

        <ckeditor-helper
        v-if="Formulari.Tipus == 'textarea-helper'"
        :titol = "Formulari.Titol"
        :valor-defecte = "Formulari.ValorDefecte"
        :id = "Formulari.Id"
        @onchange = "onchange($event)"
        ></ckeditor-helper>

        <select-helper             
        v-else-if="Formulari.Tipus == 'select-helper'"
        :titol = "Formulari.Titol"
        :valor-defecte = "Formulari.ValorDefecte"
        :id = "Formulari.Id"
        :options = "Formulari.Options"
        @onchange = "onchange($event)"                
        ></select-helper>

        <image-helper-cropper
            v-else-if="Formulari.Tipus == 'image-helper-cropper'"
            :accio-esborra = "Formulari.Imatge.Accio_Esborra"
            :accio-guarda="Formulari.Imatge.Accio_Guarda"
            :id-element = "Formulari.Imatge.Imatge_Id_Element"
            :mida-imatge = "Formulari.Imatge.Mida"
            :url-a-mostrar = "Formulari.Imatge.Url_a_mostrar"            
            :titol = "Formulari.Titol"
            @update = "onchange($event)"
        ></image-helper-cropper>

        <upload-helper
            v-else-if="Formulari.Tipus == 'upload-helper'"
            :accio-esborra = "Formulari.Imatge.Accio_Esborra"
            :accio-guarda="Formulari.Imatge.Accio_Guarda"
            :id-element = "Formulari.Imatge.Imatge_Id_Element"            
            :url-a-mostrar = "Formulari.Imatge.Url_a_mostrar"            
            :titol = "Formulari.Titol"
            @update = "onchange($event)"
        ></upload-helper>        

    </div>
                `,
});