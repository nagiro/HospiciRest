
Vue.component('ckeditor-helper', {
    props: {
        titol: String,
        valorDefecte: String,
        id: String        
    },
    data: function() { return {
        editor: ClassicEditor,
        editorData: '<p>Content of the editor.</p>',
        editorConfig: {
            height: '300px'
            // The configuration of the editor.
        },
        valorAutentic: this.valorDefecte
     }},
    computed: {    
    },
    watch: {},
    methods: {

        OnChange() {                     
            this.$emit('onchange', this.valorAutentic)
        }
   
    },

    template: `    
        <div class="R">
            <div class="FT"> {{titol}} </div>
            <div class="FI"> 

                <ckeditor :editor="editor" v-model="valorAutentic" @input="OnChange" :config="editorConfig"></ckeditor>    
            
            </div>
        </div>
        `,
});


