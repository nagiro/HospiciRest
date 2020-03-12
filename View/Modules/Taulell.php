
<div id="taulellComponent" style="margin-bottom: 30px;" class="col-10">

  <div class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Cercador</h5></div>  
    <div class="card-body">    

      <div class="row">
        
        <?php echo InputHelper('textCercador',  8,"Paraules", "cercador", "Paraules a buscar...", false); ?>        
        <?php echo ButtonHelper('BotoCerca', 2, "Cerca!", 'btn-success'); ?>        
        
      </div>    
      
    </div>
  </div>  

  <div v-if="!EditantAltri && !EditantPropi" class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><?php echo TitleWithAdd('getNewMissatge()', 'Llistat de missatges') ?></div>    
    <div class="card-body">            
      <div v-for="(I, K) in Missatges">
        <div class="titol_missatge">{{I.Data}}</div>
        <div class="taula_missatges" v-for="(I2, K2) in I.Missatge"> 
          <div class="taula_missatges_col1 withHand mytooltip" @click="EditaMissatge(I2.Missatges_MissatgeId)"> 
            <i class="fas fa-caret-right"></i> 
            <span class="MissatgeTitol "> {{I2.Missatges_Titol}} </span>
            <span class="QuantesRespostes"> ({{I2.Respostes_QuantsMissatges}} resposta/s) </span>
            <span class="mytooltiptext" v-html="I2.Missatges_Text"> </span>
          </div>
          <div class="taula_missatges_col2">{{I2.USUARIS_NomComplet}}</div>          
        </div>
      </div>

    <div class="VeureMes withHand" @click="VeureMesMissatges()">Mes missatges</div>
    </div>
  </div>  

  <div v-if="EditantPropi == true || EditantAltri == true" class="card border-secondary card-default" style="margin-top:20px;">
    
    <div class="card-header"><h5>Editant el missatge {{MissatgeDetall.PROMOCIONS_PROMOCIO_ID}}</h5></div>  
    <div class="card-body">    
    
      <div class="table table-striped table-sm">          
                
        <!-- ESPAI D'ALTRI -->

        <div class="RequadreEdicio" v-if="EditantAltri == true">
          <div class="EdicioTitolMissatge"> {{MissatgeDetall.Missatges_Titol}} </div>
          <div class="EdicioTextlMissatge" v-html="MissatgeDetall.Missatges_Text"></div>
        </div>

        <!-- FI ESPAI D'ALTRI -->

        <!-- ESPAI DE PROPI -->

        <div v-if="EditantPropi == true">
          <input-helper :titol = "'Titol'" :valor-defecte = "MissatgeDetall.Missatges_Titol" :id = "'Missatges_Titol'" @onchange = "MissatgeDetall.Missatges_Titol = $event" ></input-helper>
          <ckeditor-helper :titol = "'Text'" :valor-defecte = "MissatgeDetall.Missatges_Text" :id = "'Missatges_Text'" @onchange = "MissatgeDetall.Missatges_Text = $event" ></ckeditor-helper>
          <datepicker-helper :titol = "'Data publicació'" :valor-defecte = "MissatgeDetall.Missatges_Publicacio" :id = "'Missatges_Publicacio'" @onchange = "MissatgeDetall.Missatges_Publicacio = $event"></datepicker-helper>
        </div>

        <div class="R" v-if="EditantPropi == true">
          <div class="FT">
            <button v-on:click="GuardaMissatge" class="btn btn-success">Guardar</button>
            <button v-on:click="EsborraMissatge" class="btn btn-danger">Eliminar</button>
          </div>
          <div class="FI">              
            <button v-on:click="CancelaEdicio" class="btn btn-info">Tornar</button>            
          </div>
          
        </div>                 

        <!-- FI ESPAI DE PROPI -->

      </div>

      <div class="Franja"> Respostes </div>
      <div class="RespostesLlistat" v-for="R in MissatgeRespostes">        
        <div class="RespostaCol1">{{R.USUARIS_NomComplet}} <div class="RespostaCol1Dia">{{R.Respostes_Data}}</div></div>
        <div class="RespostaCol2">{{R.Respostes_Text}}</div>          
      </div>              

      <div class="RespostaInput" v-if="RespostaEdit">
        <input-helper :titol = "'Text'" :valor-defecte = "RespostaDetall.Respostes_Text" :id = "'Respostes_Text'" @onchange = "RespostaInput($event, false)" @onintro = "RespostaInput($event, true)" ></input-helper>
      </div>
      <button v-on:click="getNewResposta()" v-if="!RespostaEdit" class="BotoResposta btn btn-sm btn-success">Afegir resposta</button>

    </div>
  </div>

</div>


<style>

.titol_missatge { font-weight: bold; margin-top:20px; margin-bottom:10px; }
.taula_missatges_col1 { padding:5px; width: 80%; font-size: 12px; }
.taula_missatges_col2 { padding:5px; width: 20%; }
.taula_missatges { display: flex; font-size:12px; border-bottom:1px solid #CCCCCC; }
.QuantesRespostes { margin-left: 5px; font-size: 10px; color: gray; }

.RequadreEdicio { position: relative; min-height: 150px; }
.EdicioTitolMissatge { font-size:14px; font-weight: bold; }
.EdicioTextlMissatge { font-size: 12px; }
.BotoResposta { margin-top: 10px; }

.Franja { background-color: #CCCCCC; padding: 5px; font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 12px; }
.RespostaInput {}
.RespostesLlistat { padding: 5px; display: flex; border-bottom: 1px solid #CCCCCC }
.RespostaCol1 { font-size:10px; width: 20%; }
.RespostaCol1Dia { font-size: 10px; }
.RespostaCol2 { width: 80%; font-size:12px; }

.VeureMes { padding: 10px; background-color: #999; text-align: center; margin-top: 15px; font-weight: bold; color: white; }


</style>


<script>    

  var apiUrl = '/'  

  var vm2 = new Vue({
  el: '#taulellComponent',    
  data: { 
    Missatges: [], 
    MissatgeDetall: {}, 
    MissatgeRespostes: [],
    RespostaDetall: {},
    EditantPropi: false,
    EditantAltri: false, 
    RespostaEdit: false,
    textCercador: "",      
    TotalMissatges: 20,       
  },
  created: function() {
          this.BotoCerca();
          this.EditantPropi = false;  
          this.EditantAltri = false;  
  },
  computed: {},
  methods: {         

    BotoCerca: function(VincDeClicarBoto = true) {

      if(VincDeClicarBoto) this.TotalMissatges = 20;

      this.axios.get('/apiadmin/taulell', { 'params' : { 'accio': 'L', 'q': this.textCercador, 'lim': this.TotalMissatges } } )
        .then( R => { 
            
            if(VincDeClicarBoto) this.Missatges = R.data; 
            else R.data.map( E => this.Missatges.push(E));

            this.EditantAltri = false; 
            this.EditantPropi = false; 
          } 
        )
        .catch( E => { alert(E) } );

    },

    VeureMesMissatges: function() {
      this.TotalMissatges += 20;
      this.BotoCerca(false);
    },

    /* Quan rebo un intro a la resposta, la guardo */
    RespostaInput: function(text, introKeyPressed) {               
      this.RespostaDetall.Respostes_Text = text;
      if (introKeyPressed) {
        if(text.length > 0) this.GuardaResposta();
        else { this.RespostaEdit = false; }
      }
    },

    /* Guardo la resposta pròpiament */
    GuardaResposta: function() {

      let fd = new FormData();
      fd.append('accio', 'UR'); 
      fd.append('RespostaDetall', JSON.stringify(this.RespostaDetall));
      
      this.axios.post('/apiadmin/taulell', fd )
                .then( R => { this.LoadRespostes(this.RespostaDetall.Respostes_PareId) } )
                .catch( E => { alert(E); } ); 

    },

    /* Carrego les respostes d'un missatge */
    LoadRespostes: function(idMissatge) {
      let fd = new FormData();
      fd.append('accio', 'LR'); 
      fd.append('idMissatge', idMissatge);
      this.axios.post('/apiadmin/taulell', fd )
                .then( R => { this.MissatgeRespostes = R.data; this.RespostaEdit = false; } )
                .catch( E => { alert(E); } ); 
    },
    
    /* Carrego una resposta en blanc */
    getNewResposta: function() {
      let fd = new FormData();
      fd.append('accio', 'AR'); 
      fd.append('idMissatge', this.MissatgeDetall.Missatges_MissatgeId);      

      this.axios.post('/apiadmin/taulell',  fd )
                .then( R => { this.RespostaDetall = R.data; this.RespostaEdit = true; } )
                .catch( E => { alert(E) });

    },
    
    /* Carrego un nou missatge en blanc */
    getNewMissatge: function() {
      this.axios.get('/apiadmin/taulell', { 'params' : { 'accio': 'A'}} )
                .then( R => { this.MissatgeDetall = R.data; this.EditantPropi = true; } )
                .catch( E => { alert(E) });
    },
    
    /* Edito un missatge */
    EditaMissatge: function(idMissatge) {

      this.axios.get('/apiadmin/taulell', { params: { 'accio': 'CU', 'idMissatge': idMissatge }} )
                .then(  R => {  this.MissatgeDetall = R.data.MISSATGE; 
                                this.MissatgeRespostes = R.data.RESPOSTES;
                                this.EditantPropi = this.MissatgeDetall.GEN_POT_EDITAR; 
                                this.EditantAltri = !this.EditantPropi;    
                                console.log(R);                            
                             } )
                .catch( E => { alert(E); } );      

    },
    
    /* Cancel·lo la edició */
    CancelaEdicio: function() {       
      this.EditantPropi = false; 
      this.EditantAltri = false; 
      this.MissatgeDetall = {};       
    },

    /* Guardo un missatge */
    GuardaMissatge: function() {
      let fd = new FormData();
      fd.append('accio', 'U');             
      fd.append('MissatgeDetall', JSON.stringify(this.MissatgeDetall));      
      this.axios.post('/apiadmin/taulell', fd )
                .then( R => { this.BotoCerca(); this.EditantPropi = false; } )
                .catch( E => { alert(E); } ); 
    },

    /* Esborro un missatge */
    EsborraMissatge: function() {
      let fd = new FormData();
      fd.append('accio', 'D'); 
      fd.append('MissatgeDetall', JSON.stringify(this.MissatgeDetall));
      
      this.$http.post('/apiadmin/taulell', fd )
                .then( R => { this.BotoCerca(); this.Editant = false; } )
                .catch( E => { alert(E); } );      

    }

  }
  });
</script>