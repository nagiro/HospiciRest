
Vue.component('form-usuari-auth', {
    props: {},          
    data: function() {
        return {            
            formDataValues: {                                
                Nom: '',
                Cog1: '',
                Cog2: '',
                Telefon: '',
                Email: '',
                Municipi: '',
                Genere: '',
                AnyNaixement: ''
            },
            formErrors: {                                
                Nom: true,
                Cog1: true,
                Cog2: true,
                Telefon: true,
                Email: true,
                Municipi: true,
                Genere: true,
                AnyNaixement: true
            },
            Loading: false,
            DNIFormVisible: true,
            DadesFormVisible: false,
            DNI: ''            
        }
    },    
    computed: {},
    watch: {},

    methods: {                

        // Retorna l'Idusuari... per seguir amb el procés.
        throwIdUsuari(IdUsuariEncrypted) {
            this.$emit('on-id-usuari-encrypted-loaded', IdUsuariEncrypted);
        },        
        validaDNILocal: function() {
            return !ValidaDNI(this.DNI);
        },
        submitDNI: function() {                      
            this.Loading = true;
            axios.get( CONST_api_web + '/ExisteixDNI', {'params': {'DNI': this.DNI, 'Origen': 'FormUsuariAuth' }}).then( X => {                    
                this.Loading = false;
                if(X.data.ExisteixDNI) { this.throwIdUsuari({'DNI': this.DNI, 'IdUsuariEncrypted' : X.data.IdUsuariEncrypted }); this.DNIFormVisible = false; }
                else { this.DadesFormVisible = true; this.DNIFormVisible = false; }
            }).catch( E => { this.DNIFormVisible = true; this.DadesFormVisible = false; alert(E); });
        },                
        isValidFormUsuari: function($camp, $E) {

            this.formErrors[$camp] = $E;
            for(E of Object.keys(this.formErrors)) {
                if(!this.formErrors[E]) { this.isFormValid = false; return; }
            }
            this.isFormValid = true;                        
            return;
            
        },
        doAltaUsuari: function() {
            $FD = new FormData();
            $FDV = this.formDataValues;
            $FD.append('DNI', this.DNI);
            $FD.append('Nom', $FDV.Nom);
            $FD.append('Cog1', $FDV.Cog1);
            $FD.append('Cog2', $FDV.Cog2);
            $FD.append('Email', $FDV.Email);
            $FD.append('Telefon', $FDV.Telefon);            
            $FD.append('Municipi', $FDV.Municipi);
            $FD.append('Genere', $FDV.Genere);
            $FD.append('AnyNaixement', $FDV.AnyNaixement);            
            
            this.Loading = true;
            axios.post( CONST_api_web + '/NouUsuari', $FD ).then( X => {
                this.Loading = false;                
                // Si s'ha creat un nou usuari correctament, mirem el DNI
                if(X.data.ExisteixDNI) this.throwIdUsuari({'DNI': this.DNI, 'IdUsuariEncrypted' : X.data.IdUsuariEncrypted });
                else this.EntraDades = true;
            }).catch( E => { alert(E); });
        }
    },
    template: `            

    <div class="FormUsuariAuth">        
                                                
        <div v-if="DNIFormVisible">                                        
            <div class="row">
                <form-utils 
                    :fieldtype="'input'" :id = "'DNI'" :title = "'DNI/NIE'" :value = "DNI" :helptext = "'Entreu el DNI/NIE'"                    
                    @onkeyup="DNI = $event" 
                    :errors = "[[validaDNILocal(DNI), 'El DNI/NIE és incorrecte.']]" :sterrors = "['Required']" :groupclass="['col-lg-6', 'col-6']"
                ></form-utils>                
                <form-utils 
                    :fieldtype="'button'" :id = "'BDNI'" :title = "'Valida'"  :value = "''" :disabled = " ( validaDNILocal(DNI) || DNI.length == 0 )"
                    :groupclass="['col-lg-2', 'col-3']"                    
                    @onButtonPress = "submitDNI()"
                    ></form-utils>                
            </div>
        </div>    
                
        <div class="row" v-if="DadesFormVisible">        
            <div class="col-lg-12 alert alert-warning">No hem trobat el seu usuari a la nostra base de dades. Per poder continuar hauria d'entrar les seves dades personals.</div>            
        </div>
        <div class="row" v-if="DadesFormVisible">
            <form-utils 
                :fieldtype="'input'" 
                :id = "'Nom'" 
                :title = "'Nom'" 
                :value = "formDataValues.Nom" 
                :helptext = "'(Obligatori) El seu nom de pila'"                                    
                :sterrors = "['Required']"                
                :groupclass="['col-lg-4']"
                @onkeyup="formDataValues.Nom = $event"
                @isvalid="isValidFormUsuari('Nom', $event)"
            ></form-utils>
            <form-utils 
                :fieldtype="'input'" 
                :id = "'Cog1'" 
                :title = "'Primer cognom'" 
                :value = "formDataValues.Cog1" 
                :helptext = "'(Obligatori) El seu primer cognom'"                                    
                :sterrors = "['Required']" 
                :groupclass="['col-lg-4']"
                @onkeyup="formDataValues.Cog1 = $event"                
                @isvalid="isValidFormUsuari('Cog1', $event)"
            ></form-utils>
            <form-utils 
                :fieldtype="'input'" 
                :id = "'Cog2'" 
                :title = "'Segon cognom'" 
                :value = "formDataValues.Cog2" 
                :helptext = "'(Opcional) El segon cognom'"                                 
                :groupclass="['col-lg-4']"
                @onkeyup="formDataValues.Cog2 = $event"
                @isvalid="isValidFormUsuari('Cog2', $event)"
            ></form-utils>                    

        </div>
        <div class="row" v-if="DadesFormVisible">
            
            <form-utils 
                :fieldtype="'input'" 
                :id = "'Telefon'" 
                :title = "'Telèfon'" 
                :value = "formDataValues.Telefon" 
                :helptext = "'(Obligatori) El seu número de mòbil.'"                                    
                :sterrors = "['Required', 'Telefon']"
                :groupclass="['col-lg-6']"
                @onkeyup="formDataValues.Telefon = $event"
                @isvalid="isValidFormUsuari('Telefon', $event)"
            ></form-utils>
            <form-utils 
                :fieldtype="'input'" 
                :id = "'Email'" 
                :title = "'Correu electrònic'" 
                :value = "formDataValues.Email" 
                :helptext = "'(Obligatori) El seu correu electrònic.'"                                    
                :sterrors = "['Required', 'Email']" 
                :groupclass="['col-lg-6']"
                @onkeyup="formDataValues.Email = $event"                
                @isvalid="isValidFormUsuari('Email', $event)"
            ></form-utils>
                          
        </div>
        <div class="row" v-if="DadesFormVisible">

            <form-utils 
                :fieldtype="'input'" 
                :id = "'Municipi'" 
                :title = "'Municipi'" 
                :value = "formDataValues.Municipi" 
                :helptext = "'(Opcional) El nom del municipi on viu.'"                                                    
                :groupclass="['col-lg-4']"            
                @onkeyup="formDataValues.Municipi = $event"
                @isvalid="isValidFormUsuari('Municipi', $event)"
            ></form-utils>

            <form-utils 
                :fieldtype="'select'" 
                :id = "'Genere'" 
                :title = "'Gènere'" 
                :value = "formDataValues.Genere" 
                :helptext = "'(Opcional) El seu gènere.'"                                                    
                :groupclass="['col-lg-4']"
                :options="[{ id: 'M', text: 'Masculí'}, { id: 'F', text: 'Femení'}, { id: 'A', text: 'Altres'}]"
                @onchange="formDataValues.Genere = $event"
                @isvalid="isValidFormUsuari('Genere', $event)"
            ></form-utils>            

            <form-utils 
                :fieldtype="'date'" 
                :id = "'AnyNaixement'" 
                :title = "'Any de naixement'" 
                :value = "formDataValues.AnyNaixement" 
                :helptext = "'(Opcional) La seva data de naixement.'"                                                    
                :groupclass="['col-lg-4']"
                @onkeyup="formDataValues.AnyNaixement = $event"
                @isvalid="isValidFormUsuari('AnyNaixement', $event)"
            ></form-utils>                        
                   
        </div>
        <div class="row" v-if="DadesFormVisible">
            <form-utils 
                :fieldtype="'button'" 
                :id = "'BSEGUEIX'" 
                :title = "'Segueix...'" 
                :value = "''" 
                :disabled = "!isFormValid"
                :groupclass="['col-lg-2']"                    
                @onButtonPress = "doAltaUsuari()"
            ></form-utils>
        </div>        
                                                    
    </div>

`
});