    <style>
        #validador { padding: 2vw; }
        .validador_box { margin-top: 2vw; }
        .validador_resposta { background-color: black; margin-top:2vw; border: 1px solid black; text-align: center; padding: 2vw; color: white; font-size: 2rem; }
        .validador_error { background-color: red; }
        .validador_ok { background-color: green; }
        .validador_codi { font-size:1rem; }
        .validador_arribat { background-color: #ff9d88; }
        .validador_falta_arribar { background-color: #b0ffc3; }
        .validador_falten { margin-top: 2vw; }
    </style>

</head>
<body>
    
    <div id="validador" class="page">

        <h1>Validador d'entrades</h1>

        <div class="validador_box">
            <input type="text" v-model="QRText" v-on:keyup.13="ValidaCodi($event, true)" />
            <button @click="ValidaCodi($event, true)">Valida</button>
        </div>

        <div class="validador_resposta" :class="VRC">            
            {{Missatge}}
        </div>
        
        <div class="validador_falten">
            <h1>Llistat d'assistents</h1>
            <table>
                <tr v-for="MF of CursosMatricules" :class="EstilFila(MF.data_hora_entrada)">
                    <td style="padding: 0.5vw;"><button v-if="!MF.data_hora_entrada" @click="ValidaCodi(MF.idMatricules, false)">Valida</button></td>    
                    <td style="padding: 0.5vw;">{{MF.Cog1}} {{MF.Cog2}}, {{MF.Nom}}</td>
                    <td style="padding: 0.5vw;"> F: {{MF.Fila}} | S: {{MF.Seient}}</td>
                </tr>
            </table>
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
                CursosMatricules: []                
            },            
            created: function() {
                let CursosMatriculesRaw = [];
                this.CursosMatricules = <?php echo $Data ?>;                
            },
            computed: {},
            methods: {
                EstilFila: function(HoraEntrada) {                                        
                    if( HoraEntrada && HoraEntrada.length > 0 ) { return 'validador_arribat'; }
                    else { return 'validador_falta_arribar'; }
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
                                this.Missatge = "L'entrada s'ha venut a taquilla.";
                            } else {
                                let E = this.CursosMatricules[i];
                                this.$set(this.CursosMatricules[i], 'data_hora_entrada', 'Arribat');                            
                                // Munto el missatge OK. 
                                this.Missatge = E.Cog1 + ' ' + E.Cog2 + ' ' + E.Nom + ' | F:' + E.Fila + '|S:' + E.Seient;
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
                }                    
            }            
        });

    </script>

</body>
