    <style>
        #validador { padding: 2vw; }
        .validador_box { margin-top: 2vw; }
        .validador_resposta { background-color: black; margin-top:2vw; border: 1px solid black; text-align: center; padding: 2vw; color: white; font-size: 2rem; }
        .validador_error { background-color: red; }
        .validador_ok { background-color: green; }
        .validador_codi { font-size:1rem; }
        .validador_arribat { background-color: #ff9d88; }
        .validador_falta_arribar { background-color: #b0ffc3; }
        .validador_falta_arribar_llista_espera { background-color: orange; }
        .validador_falten { margin-top: 2vw; }
    </style>

</head>
<body>
    
    <div id="validador" class="page">                
    
        <h1>Validador d'entrades</h1>        

        <div v-if="CursEscollit > -1">

            <h2>{{CursosAEscollir[CursEscollit]}}</h2>

            <div class="validador_box">
                <label for="EntradaDades">Codi entrada</label>
                <qrcode-stream @init="onInit" @decode="ValidaCodi($event, true)"></qrcode-stream>                        
                <input type="text" v-model="QRText" id="EntradaDades" v-on:keyup.13="ValidaCodi($event, true)" />
                <button @click="ValidaCodi($event, true)">Valida</button>
            </div>

            <div class="validador_resposta" :class="VRC">            
                {{Missatge}}
            </div>
            
            <div class="validador_falten">
                <h1>Llistat d'assistents [{{Entrats}}/{{Total}}] = {{Total - Entrats}}</h1>
                <br /><br />
                <table>
                    <tr v-for="MF of CursosMatricules" :class="EstilFila(MF.data_hora_entrada)">
                        <td style="padding: 2vw 0.5vw; "><button v-if="!MF.data_hora_entrada" @click="ValidaCodi(MF.idMatricules, false)">Valida</button></td>    
                        <td style="padding: 2vw 0.5vw; " v-if="!MF.Comentari">{{MF.Cog1}} {{MF.Cog2}}, {{MF.Nom}}</td>
                        <td style="padding: 2vw 0.5vw; " v-if="MF.Comentari && MF.Comentari.length > 0">{{MF.Comentari}}</td>
                        <td style="padding: 2vw 0.5vw; " v-show="MF.Fila > 0"> F: {{MF.Fila}} | S: {{MF.Seient}}</td>
                        <td style="padding: 2vw 0.5vw; background-color: orange; " v-show="MF.tPagament == TipusPagamentLlistaEspera"> Llista espera </td>                        
                    </tr>
                </table>
            </div>
        
        </div>
        <div v-else>
            <div class="validador_box">
                <div>Escull un curs</div>
                <select v-model="CursEscollit" @change="EsculloCurs" >
                    <option v-for="(C,k) of CursosAEscollir" :value="k">{{C}}</option>
                </select>
            </div>
        </div>

    <div style="margin-bottom: 2vw">&nbsp;</div>
                
  </div>


  <script>
        var vm2 = new Vue({
        
            el: '#validador',        
            data: { 
                Loaded: false,
                Errors: false,
                QRText: '',
                QRTextCopy: '',
                VRC: '',
                Missatge: '',
                CursEscollit: -1,
                CursosMatriculesRaw: [],
                CursosAEscollir: [],
                CursosMatricules: [],                
                Entrats: 0, 
                Totals: 0,
                TipusPagamentLlistaEspera: CONST_PAGAMENT_LLISTA_ESPERA,
                error: ""
            },            
            created: function() {
                
                let T = '';
                this.CursosMatriculesRaw = <?php echo $Data ?>;
                
                for(E of this.CursosMatriculesRaw) {                    
                    if(E.TitolCurs != T) this.CursosAEscollir.push(E.TitolCurs); 
                    T = E.TitolCurs;
                }                                
            },
            computed: {},
            methods: {
                EsculloCurs: function() {                    
                    NomCurs = this.CursosAEscollir[this.CursEscollit];                                        
                    this.CursosMatricules = [];                    
                    this.Entrats = 0;
                    for(E of this.CursosMatriculesRaw) {
                        if(E.TitolCurs == NomCurs) {                            
                            this.CursosMatricules.push(E);                            
                            this.Entrats = ( E['data_hora_entrada'] ) ? this.Entrats + 1 : this.Entrats;
                        }                        
                    }    
                    
                    this.Total = this.CursosMatricules.length;                
                },
                EstilFila: function(HoraEntrada, TipusPagament) {                                        
                    if( HoraEntrada && HoraEntrada.length > 0 ) { return 'validador_arribat'; }
                    else if (TipusPagament == this.TipusPagamentLlistaEspera) { return 'validador_falta_arribar_llista_espera'; }
                    else return 'validador_falta_arribar';
                },
                ValidaCodi(text, fromQR) {
                    let FD = new FormData();
                    if(fromQR) FD.append('QR', this.QRText); 
                    else FD.append('idMatricula', text);

                    this.axios.post('/apiweb/validaCodi', FD )
                    .then( R => {                          
                        if(R.data.estat) {
                            let idMatricula = R.data.idMatricula;
                            this.VRC = ['validador_resposta', 'validador_ok'];                            
                            
                            // Trec la matrícula de la llista de pendents i la poso a l'altra llista
                            let i = this.CursosMatricules.findIndex( E => E.idMatricules == idMatricula);                            
                            if(i == -1) {
                                this.Missatge = "Venuda a taquilla. Actualitza per veure-la.";
                            } else {
                                let E = this.CursosMatricules[i];
                                this.$set(this.CursosMatricules[i], 'data_hora_entrada', 'Arribat');
                                this.Missatge = E.Cog1 + ' ' + E.Cog2 + ' ' + E.Nom ;
                                this.Missatge += (E.Fila > 0) ? ' | F:' + E.Fila + '|S:' + E.Seient : '';
                                this.Entrats++;
                            }                                                                                    
                                                                                                                
                            // Ha anat bé
                        } else {
                            // Hi ha errors
                            this.VRC = ['validador_resposta', 'validador_error'];
                            this.Missatge = R.data.error;                            
                        }
                        this.QRTextCopy = this.QRText;
                        this.QRText = '';
                    }).catch(E => { alert(E); });
                },
                async onInit (promise) {
                    try {
                        await promise
                    } catch (error) {
                        alert(error.name);
                        if (error.name === 'NotAllowedError') {
                        this.error = "ERROR: you need to grant camera access permisson"
                        } else if (error.name === 'NotFoundError') {
                        this.error = "ERROR: no camera on this device"
                        } else if (error.name === 'NotSupportedError') {
                        this.error = "ERROR: secure context required (HTTPS, localhost)"
                        } else if (error.name === 'NotReadableError') {
                        this.error = "ERROR: is the camera already in use?"
                        } else if (error.name === 'OverconstrainedError') {
                        this.error = "ERROR: installed cameras are not suitable"
                        } else if (error.name === 'StreamApiNotSupportedError') {
                        this.error = "ERROR: Stream API is not supported in this browser"
                        }
                    }
                }
            }            
        });

    </script>

</body>
