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
        .qrcode-stream-wrapper { width: 20% !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>

</head>
<body>
    
    <div id="validador" class="page">                
    
        <h1>Validador d'entrades</h1>        

        <div v-if="CursEscollit > -1">

            <h2>{{CursosAEscollir[CursEscollit]}}</h2>

            <div class="validador_box">
                <label for="EntradaDades">Codi entrada</label>                
                <input style="width: 70%" type="text" v-model="QRText" id="EntradaDades" v-on:keyup.13="ValidaCodi($event, true)" />
                <button @click="ValidaCodi($event, true)">Valida</button>
            </div>

            <div style="margin-top: 1vw;">
                <div id="camera" v-show="isScanning">            
                    <div> <video style="display:none" ref="video" id="video" width="100%" height="100%" autoplay/> </div>            
                    <canvas ref="canvas" id="canvas" width="100px" height="100px" />            
                </div>
                <button @click="Escaneja()" :class="(isScanning) ? 'btn btn-success' : 'btn btn-info'">{{(isScanning) ? 'Atura escaneig' : 'Escaneja'}}</button>
            </div>

            <div class="validador_resposta" :class="VRC">                            
                {{Missatge}}
            </div>
            
            <div class="validador_falten">
                <h1>Llistat d'assistents [{{Entrats}}/{{Total}}] = {{Total - Entrats}}</h1>
                <br /><br />
                <table>
                    <tr v-for="MF of CursosMatricules" :class="EstilFila(MF.data_hora_entrada, MF.Estat)">                    
                        <td style="padding: 2vw 0.5vw; "><button v-if="!MF.data_hora_entrada" @click="ValidaCodi(MF.idMatricules, false)">Valida</button></td>    
                        <td style="padding: 2vw 0.5vw; " v-if="!MF.Comentari">{{MF.Cog1}} {{MF.Cog2}}, {{MF.Nom}}</td>
                        <td style="padding: 2vw 0.5vw; " v-if="MF.Comentari && MF.Comentari.length > 0">{{MF.Comentari}}</td>
                        <td style="padding: 2vw 0.5vw; " v-show="MF.Fila > 0"> F: {{MF.Fila}} | S: {{MF.Seient}}</td>
                        <td style="padding: 2vw 0.5vw; background-color: orange; " v-show="MF.Estat == ConstEstatLlistaEspera "> Llista espera </td>                                                
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
                ConstEstatLlistaEspera: CONST_ESTAT_LLISTA_ESPERA,
                error: "",                


                video: {},
                canvas: {},
                captures: [],
                LastCodeReaded: "",
                isScanning: false,                
                
            },            
            created: function() {
                
                let T = '';                                
                const D = <?php echo $Data ?>;                
                this.CursosMatriculesRaw = D.Llistat;
                
                for(E of this.CursosMatriculesRaw) {                    
                    if(E.TitolCurs != T) this.CursosAEscollir.push(E.TitolCurs); 
                    T = E.TitolCurs;
                }                                

            },

            mounted() {},

            computed: {},
            methods: {
                Escaneja() {                                        
                    
                    this.video = this.$refs.video;
                    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                        navigator.mediaDevices.getUserMedia({ audio: false, video: { facingMode: { exact: "environment" } } }).then(stream => {
                            video.srcObject = stream;
                                video.play();
                                video.onplay = function () {};
                                video.play();
                                requestAnimationFrame(this.tick);
                        }).catch(function(err) { console.log(err); alert(err.message + err.constraint); });
                    }
                
                    this.isScanning = !this.isScanning;
                    requestAnimationFrame(this.tick);

                },
                drawLine(begin, end, color) {

                    this.context.beginPath();
                    this.context.moveTo(begin.x, begin.y);
                    this.context.lineTo(end.x, end.y);
                    this.context.lineWidth = 4;
                    this.context.strokeStyle = color;
                    this.context.stroke();

                },
                tick() {        
                    
                    this.canvas = this.$refs.canvas;                    
                    this.canvas.height = 200; //this.video.height;
                    this.canvas.width = 200; //this.video.width;                    
                    this.context = this.canvas.getContext("2d");             
                    this.context.drawImage(video, 0, 0, this.canvas.width, this.canvas.height);       
                    
                    var imageData = this.context.getImageData(0, 0, this.canvas.width, this.canvas.height);
                    var code = jsQR(imageData.data, imageData.width, imageData.height);
                    
                    if( code ) {                        
                        
                        if( code.data != this.LastCodeReaded ) { 
                            this.LastCodeReaded = code.data;
                            this.QRText = code.data;
                            this.ValidaCodi(this.QRText, true);                            
                        }

                        this.drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#FF3B58");
                        this.drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#FF3B58");
                        this.drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#FF3B58");
                        this.drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#FF3B58");

                    } else {

                        this.QRText = "";

                    }

                    if(this.isScanning) requestAnimationFrame(this.tick);

                },
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
                EstilFila: function(HoraEntrada, Estat) {                                                            
                    if( HoraEntrada && HoraEntrada.length > 0 ) { return 'validador_arribat'; }
                    else if (Estat == this.ConstEstatLlistaEspera) { return 'validador_falta_arribar_llista_espera'; }
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
                }

            }
        });

    </script>

</body>