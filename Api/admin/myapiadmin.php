<?php

require_once APIDIR . 'api.php';
require_once CONTROLLERSDIR . 'AuthController.php';

class NoAuthException extends Exception {
    
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}

class MyAPIAdmin extends API
{

    public $dbh;
    private $LOCAL_URL;    
    private $Auth = false;

    public $C = array();

    public function __construct($request, $origin) {
        
        parent::__construct($request);        
        
        //Comprovem si el token que ha arribat és vàlid o no.        
        $this->Auth = new AuthController();        
        $this->Auth->DecodeToken();
        
        
        try {
            $this->dbh = new BDD("","",array(),array());            
        } catch (Exception $e) { return array($e->getMessage(), 500); }                        

    }

    protected function Prova() {
        $AM = new ActivitatsModel();
        var_dump($AM->Form(0, 1));
        die;
    }

    protected function Sites() {

        require_once CONTROLLERSDIR . 'Admin/SitesController.php';
        
        $accio = (isset($this->request['accio'])) ? $this->request['accio']: ''; 
        
        if($this->Auth->isAuthenticated()) {                        

            // Aquí només hi accedim per carregar el login o bé quan no hem estat autenticats
              switch($accio) {
                case 'ALL_SITES':      
                    
                    $S = new SitesController();
                    $RET = $S->getSitesActius();                      
                    return array($RET, 200); 
                break;
                default: $RET = array(array("No estàs autenticat."), 500);
            }            

        } else { 
            
            // Aquí només hi accedim per carregar el login o bé quan no hem estat autenticats
            switch($accio) {
                case 'ALL_SITES':                          
                    $S = new SitesController();
                    $RET = $S->getSitesActius();                                          
                    return array($RET, 200); 
                break;
                default: $RET = array(array("No estàs autenticat."), 500);
            }            

        }         
    }

    protected function Promocions() {
        
        require_once CONTROLLERSDIR . 'Admin/PromocionsController.php';

        if($this->Auth->isAuthenticated()) {            
            $accio = (isset($this->request['accio'])) ? $this->request['accio']: ''; 
            $paraules = (isset($this->request['q']))?$this->request['q']:'';
            $tipus = (isset($this->request['t']))?$this->request['t']:1;
            $idPromocio = (isset($this->request['idPromocio']))?$this->request['idPromocio']:0;
            $PromocioDetall = (isset($this->request['post']['PromocioDetall']))?json_decode($this->request['post']['PromocioDetall'], true):array();

            if(isset($this->request['post'])) $accio = $this->request['post']['accio'];
            
            $P = new PromocionsController();
            $RET = array();
            
            switch($accio) {
                case 'L':   $RET = $P->getLlistaPromocions($this->Auth->idSite, $paraules, $tipus); break;        
                case 'C':   $RET = $P->getPromocionsActives($this->Auth->idSite); break;        
                case 'CU':  $RET = $P->getById($idPromocio); break;        
                case 'A':   $RET = $P->getNewPromocio($this->Auth->idSite); break;
                case 'U':   $RET = $P->doUpdate($PromocioDetall); break;        
                case 'D':   $RET = $P->doDelete($PromocioDetall); break;        
                case 'UO':  $RET = $P->doOrderChange( $this->request['post']['Promocions']); break;                
            }
            
            return array($RET, 200);

        } else { 
            
            return array(array("No estàs autenticat."), 500); 

        }         
        
    }

    protected function Taulell() {
        
        require_once CONTROLLERSDIR . 'Admin/TaulellController.php';

        if($this->Auth->isAuthenticated()) {            
            $accio = (isset($this->request['accio'])) ? $this->request['accio']: ''; 
            $paraules = (isset($this->request['q']))?$this->request['q']:'';            
            $limitCerca = (isset($this->request['lim']))?$this->request['lim']:'';            
            $idMissatge = (isset($this->request['idMissatge']))?$this->request['idMissatge']:0;
            $TaulellDetall = array();
            $RespostaDetall = array();

            if(isset($this->request['post'])) {
                $P = $this->request['post'];
                $accio = $P['accio'];
                $idMissatge = (isset($P['idMissatge']))?$P['idMissatge']:0;
                $MissatgeDetall = (isset($P['MissatgeDetall']))?json_decode($P['MissatgeDetall'], true):array();
                $RespostaDetall = (isset($P['RespostaDetall']))?json_decode($P['RespostaDetall'], true):array();
            } 
                        
            $P = new TaulellController();            
            $RET = array();
            
            switch($accio) {
                case 'L':   $RET = $P->getLlistaMissatges($this->Auth->idSite, $paraules, $limitCerca); break;                        
                case 'CU':  $RET = $P->getById($idMissatge, $this->Auth->idUsuari, true); break;                        
                case 'AR':  $RET = $P->getNewResposta($idMissatge, $this->Auth->idUsuari, $this->Auth->idSite); break;
                case 'UR':  $RET = $P->doUpdateResposta($RespostaDetall); break;        
                case 'LR':  $RET = $P->getRespostesFromMissatge($idMissatge, $this->Auth->idUsuari); break;        
                case 'A':   $RET = $P->getNewMissatge($this->Auth->idUsuari, $this->Auth->idSite); break;
                case 'U':   $RET = $P->doUpdate($MissatgeDetall); break;
                case 'DR':  $RET = $P->doDeleteResposta($RespostaDetall); break;
                case 'D':   $RET = $P->doDeleteMissatge($MissatgeDetall); break;                        
            }
            
            return array($RET, 200);

        } else { 
            
            return array(array("No estàs autenticat."), 500); 

        }         
        
    }

    protected function Horaris() {
        
        require_once CONTROLLERSDIR . 'Admin/HorarisController.php';

        $RET = "";                
        if($this->Auth->isAuthenticated()) {            
        
            $accio = (isset($this->request['accio'])) ? $this->request['accio']: ''; 
            $paraules = (isset($this->request['q']))?$this->request['q']:'';            
            $DataInicial = (isset($this->request['DataInicial']))   ? $this->request['DataInicial'] : Date('Y-m-d');
            $idActivitat = (isset($this->request['idA']))     ? $this->request['idA']:0;
            $idSite = (isset($this->request['idS']))     ? $this->request['idS']:0;
            $ActivitatDetall = (isset($this->request['post']['ActivitatDetall'])) ? json_decode($this->request['post']['ActivitatDetall'], true):array();

            if(isset($this->request['post'])) $accio = $this->request['post']['accio'];

            $H = new HorarisController();                        
            $RET = array();
            
            switch($accio) {
                case 'L':   $RET = $H->getLlistaHoraris($this->Auth->idSite, $paraules, $DataInicial); break;        
                
                // Edita activitat
                case 'EA': 
                    $AM = new ActivitatsModel();
                    $RET = $AM->Formulari($idActivitat, 1);                                   
                break;
                
                case 'GetEditActivitat':  
                    $AM = new ActivitatsModel();
                    $RET = $AM->Formulari($idActivitat, 1);                                   
                break;        
//                case 'A':   $RET = $P->getNewPromocio($this->Auth->idSite); break;
                case 'UA':   $RET = $H->doUpdateActivitat($ActivitatDetall); break;        
                case 'DA':   $RET = $H->doDeleteActivitat($ActivitatDetall); break;        
//                case 'UO':  $RET = $P->doOrderChange( $this->request['post']['Promocions']); break;                
            }

            return array($RET, 200);

        } else { 
            
            return array(array("No estàs autenticat."), 500); 

        }         
        
    }
    
    /**
     * @Params accio
     * @Params idUsuari
     * @Params AuthToken
    */     
    protected function Menus() {
                     
        require_once CONTROLLERSDIR . 'Admin/MenusController.php';

        if($this->Auth->isAuthenticated()) {

            $accio = $this->request['accio'];                    
            $P = new MenusController();
            $RET = array();
            
            switch($accio) {
                case 'UM': $RET = $P->getMenusByUser($this->Auth->idUsuari, $this->Auth->idSite); break;        
            }

        }

        return array($RET, 200);
        
    }

    /**
     * @Params accio
     * @Params idUsuari
     * @Params AuthToken
    */     
    protected function Avui() {
                      
        require_once CONTROLLERSDIR . 'Admin/AvuiController.php';

        if($this->Auth->isAuthenticated()) {

            $accio = $this->request['accio'];                    
            $AC = new AvuiController();
            $RET = array();
            
            switch($accio) {
                case 'C': $RET = $AC->consulta($this->Auth->idUsuari, $this->Auth->idSite); break;        
            }

        }

        return array($RET, 200);
        
    }    

    


    /**
     * @Params accio
     * @Params idUsuari
     * @Params AuthToken
     * /admin/login
    */     
    protected function Auth() {                
        $accio = $this->request['accio'];
        $Login = (isset($this->request['login'])) ? $this->request['login'] : '';
        $Password = (isset($this->request['password'])) ? $this->request['password'] : '';
        $IdSite = (isset($this->request['idsite'])) ? $this->request['idsite'] : '';
        $Token = (isset($this->request['token'])) ? $this->request['token'] : '';
        
        $RET = array();
        
        switch($accio) {
            
            //Aquí hi entrem quan no hi ha token, però
            case 'A':   
                if( $this->Auth->doLogin($Login, $Password, $IdSite) )
                        return array('', 200);
                else    return array("No he trobat l'usuari", 500);
            break;                    

        }

        return array($RET, 200);
        
    }


       /* Carrego un arxiu al perfil */
    protected function Upload() {

        if($this->Auth->isAuthenticated()) {
                    
            $file  = (isset($this->request["files"]["File"])) ? $this->request["files"]["File"] : "";

            $accio =        (isset($this->request["post"]["accio"]))        ? $this->request["post"]["accio"]       : "";                                    
            $tipus =        (isset($this->request["post"]["Tipus"]))        ? $this->request["post"]["Tipus"]       : "" ;
            $idElement =    (isset($this->request["post"]["idElement"]))    ? $this->request["post"]["idElement"]   : "" ;
            $RET = array();                        
            
            switch($accio) {
                case 'Activitat':                     
                    $HorarisController = new HorarisController(); 

                    try {
                        $RET = $HorarisController->doUpload($accio, $file, $tipus, $idElement, $this->Auth->idUsuari, $this->Auth->idSite);
                    } catch(Exception $e){ return array($e->getMessage(), 500);  };
                    break;        
                
                case 'Activitat_Delete':                     
                        $HorarisController = new HorarisController(); 
    
                        try {
                            $RET = $HorarisController->doUploadDelete($accio, $file, $tipus, $idElement, $this->Auth->idUsuari, $this->Auth->idSite);
                        } catch(Exception $e){ return array($e->getMessage(), 500);  };
                        break;                            

                case 'Promocio': 
                    
                    require_once CONTROLLERSDIR . 'Admin/PromocionsController.php';
                    
                    $PC = new PromocionsController(); 

                    try {
                        $RET = $PC->doUpload($accio, $file, $tipus, $idElement, $this->Auth->idUsuari, $this->Auth->idSite);
                    } catch(Exception $e){ return array($e->getMessage(), 500);  };
                    break;        

                case 'Promocio_Delete': 
                                                                
                    require_once CONTROLLERSDIR . 'Admin/PromocionsController.php';

                    $PC = new PromocionsController();                     
                    try {
                        // Aquí Tipus és la mida de la imatge 's', 'm', 'l'
                        $RET = $PC->doUploadDelete($tipus, $idElement, $this->Auth->idUsuari, $this->Auth->idSite);
                    } catch(Exception $e){ return array($e->getMessage(), 500);  };
                    break;        
            }

        }

        return array($RET, 200);

     }

       /* Carrego un arxiu al perfil */
     protected function deleteFile() {

        $RET = array();
        $Params = array();
        
        $NomArxiu = $this->request['post']['NomArxiu'];        
        $TipusArxiu = $this->request['post']['TipusArxiuId'];        
        
        if(isset($this->request['post']['idU'])) {
         $idU = $this->request['post']['idU'];         
        }
        elseif(isset($this->request['post']['idE'])) {
          $idE = $this->request['post']['idE'];           
        }
        else return array("No has entrat cap número d'usuari o d'empresa", 500);

        $name = $NomArxiu;
        // Primer mirem si el directori existeix, sinó el creem

        $base_dir = '';
        if($idU > 0) $base_dir = $this->LOCAL_USERS_FILES . $idU;
        if($idE > 0) $base_dir = $this->LOCAL_EMPRESES_FILES . $idE;

        $dest_file = $base_dir . '/' . $name;        
        if (file_exists($dest_file)){
            if (unlink($dest_file)) {
                $Params[] = $name;
                $Params[] = $idU;
                if ( $TipusArxiu == 1 ){
                    $UPDATE = "UPDATE usuaris SET u_hasCurriculum = NULL WHERE u_idUsuari = ?";
                    $this->runQuery( $UPDATE, $Params );
                } elseif ($TipusArxiu == 2) {
                    $UPDATE = "UPDATE usuaris SET u_hasFoto = NULL WHERE u_idUsuari = ?";
                    $this->runQuery( $UPDATE, $Params );
                }
    
                return array('OK', 200);
            } else {
                return array('No he pogut esborrar l\'arxiu', 500);
            }
        } else {
            return array('L\'arxiu no existeix', 500);
        }

    }     



 }

 ?>
