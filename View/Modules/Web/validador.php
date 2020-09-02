    <style>
        #validador { padding: 2vw; }
        .validador_box { margin-top: 2vw; }
        .validador_resposta { background-color: black; margin-top:2vw; border: 1px solid black; text-align: center; padding: 2vw; color: white; font-size: 2rem; }
        .validador_error { background-color: red; }
        .validador_ok { background-color: green; }
    </style>

</head>
<body>
    
    <div id="validador" class="page">

        <h1>Validador d'entrades</h1>

        <div class="validador_box">
            <input type="text" v-model="QRText" v-on:keyup.13="ValidaCodi" />
            <button @click="ValidaCodi">Valida</button>
        </div>

        <div class="validador_resposta" :class="VRC">
            {{QRTextCopy}}
            <br />
            <br />
            {{Missatge}}
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
                Missatge: ''

            },            
            created: function() {},
            computed: {},
            methods: {
                ValidaCodi(text) {
                    let FD = new FormData();
                    FD.append('QR', this.QRText); 

                    this.axios.post('/apiweb/validaCodi', FD )
                    .then( R => {  
                        console.log(R.data);
                        if(R.data.estat) {
                            this.VRC = ['validador_resposta', 'validador_ok'];
                            this.Missatge = 'Entrada correcta';                            
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
