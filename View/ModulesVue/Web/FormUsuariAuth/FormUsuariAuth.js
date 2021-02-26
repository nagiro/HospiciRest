
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
            Loading: false,
            EntraDades: false,
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
        
        // $event: {getFormValues, getGroupValues, name, value}
        dnikeymonitor: function($event) {
            this.DNI = $event.value;            
            return ValidaDNI(this.DNI);
        },
        submitDNI: function(Form) {
            this.DNI = Form.DNI;
            this.Loading = true;
            axios.get( CONST_api_web + '/ExisteixDNI', {'params': {'DNI': this.DNI, 'Origen': 'FormUsuariAuth' }}).then( X => {                    
                this.Loading = false;
                if(X.data.ExisteixDNI) this.throwIdUsuari({'DNI': this.DNI, 'IdUsuariEncrypted' : X.data.IdUsuariEncrypted });
                else this.EntraDades = true;
            }).catch( E => { alert(E); });
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
                            
        <formulate-form v-if="!EntraDades" @submit="submitDNI">                                        
            <formulate-input 
                type="text" 
                name="DNI" 
                label="DNI/NIE" 
                validation="ValidaDNI" 
                :validation-rules="{ValidaDNI: dnikeymonitor}"
                :validation-messages="{'ValidaDNI': 'El DNI/NIE no és correcte i és obligatori'}"
                help="Entri el seu DNI per apuntar-se."></formulate-input>            
            <formulate-input #default="{ Loading }"  type="submit" :label="( Loading ? 'Enviant...' : 'Valida el DNI')" :disabled = "Loading"></formulate-input>                                                                                                        
            <FormulateErrors />                                        
        </formulate-form>                            
                            
        <formulate-form v-show="EntraDades" v-model="formDataValues" @submit="doAltaUsuari">                                        
            <div class="alert alert-warning">No hem trobat el seu usuari a la nostra base de dades. Per poder continuar hauria d'entrar les seves dades personals.</div>            
            <formulate-input type="text" name="Nom" label="Nom" validation="required" help="El seu nom."></formulate-input>            
            <formulate-input type="text" name="Cog1" label="Primer cognom" validation="required" help="El seu primer cognom."></formulate-input>                        
            <formulate-input type="text" name="Cog2" label="Segon cognom" help="(Opcional) El seu segon cognom."></formulate-input>            
            
            <formulate-input type="text" name="Telefon" label="Telèfon" validation="required|number" help="El seu número de telèfon mòbil."></formulate-input>            
            <formulate-input type="text" name="Email" label="Correu electrònic" validation="required|email"  help="El seu correu electrònic."></formulate-input>                        
            
            <formulate-input type="text" name="Municipi" label="Municipi" validation="" help="(Opcional) El nom del municipi on viu."></formulate-input>            
            <formulate-input type="select" name="Genere" label="Gènere" validation="" :options = "{'M':'Masculí', 'F': 'Femení', 'A': 'Altres'}" placeholder = "-- Escull una opció -- "  help="(Opcional) El seu gènere."></formulate-input>
            <formulate-input type="text" name="AnyNaixement" label="Any de naixement" validation="date:DD/MM/YYYY" help="(Opcional) La seva data de naixement."></formulate-input>


            <formulate-input #default="{ Loading }"  type="submit" :label="( Loading ? 'Enviant...' : 'Envia les dades')" :disabled = "Loading"></formulate-input>                                                                                                        
            <FormulateErrors />                                        
        </formulate-form>                            

                                                    
    </div>

`
});