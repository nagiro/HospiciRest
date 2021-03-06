
Vue.component('form-helper', {
    props: {        
        Formulari: Object       // Entra un objecte tipus FormulariClass.php        
    },
    components: {
      Multiselect: window.VueMultiselect.default
    },         
    data: function() { 
        return { 
            multiselectvalue: []
        }
    },
    created: function() {
        if( this.Formulari.Tipus == 'multiple-select-helper' ) {
            let V = this.Formulari.ValorDefecte.toString();            
            if(V.length > 0){
                if(V.includes('@')) {                    
                    V.split('@').forEach(E => {                      
                        let OptionSelected = this.Formulari.Options.find( Option => Option.id == E );  
                        this.multiselectvalue.push( OptionSelected );                        
                    });
                } else {                    
                    let OptionSelected = this.Formulari.Options.find( Option => Option.id == V );  
                    this.multiselectvalue.push( OptionSelected );                        
                }
            }                                                
        }             
    },  
    computed: {},
    watch: {},
    methods: {
        onchange: function($event) {
            
            if( this.Formulari.Tipus == 'multiple-select-helper' ) {
                let R = $event.map( X => { return X.id }).join('@');                                                
                this.$emit('onchange', R);
            } else {
                this.$emit('onchange', $event);
            }
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
        
        <div class="R" v-else-if="Formulari.Tipus == 'multiple-select-helper'">
            <div class="FT"> {{Formulari.Titol}} </div>
            <div class="FI"> 

                <multiselect                    
                    :options="Formulari.Options"                                    
                    :multiple="true"
                    :searchable="true"
                    :close-on-select="false"
                    :clear-on-select="false"
                    :limit="10"
                    v-model="multiselectvalue"            
                    @input="onchange($event)"
                    name="Formulari.Id"
                    label="nom"
                    key="id"
                    track-by="id" 
                ></multiselect>

            </div>
        </div>

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