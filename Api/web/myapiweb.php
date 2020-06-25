<?php

require_once APIDIR . 'api.php';
require_once CONTROLLERSDIR . 'ControllersWebLoader.php';

class NoAuthException extends Exception {
    
    public function __construct($message, $code = 0, Exception $previous = null) {
        // some code
    
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }
}

class MyAPIWeb extends API
{

    public $dbh;
    private $LOCAL_URL;    
    private $Auth = false;

    public $C = array();

    public function __construct($request, $origin) {
        
        parent::__construct($request);
        session_start();
        
        //Comprovem si el token que ha arribat és vàlid o no.
        $this->Auth = new AuthController();                                      
        $this->Auth->TokenDecode($_SESSION['AuthToken']);                

        try {
            $this->dbh = new BDD("","",array(),array());            
        } catch (Exception $e) { return array($e->getMessage(), 500); }        
        
    }

    protected function GeneraResguard() {
        $WAPI = new WebApiController();
        $InscripcioCodificada = (isset($_GET['i'])) ? $_GET['i'] : '';
        $isGrup = (isset($_GET['g']));
        $testMail = (isset($_GET['m']));
        
        if($isGrup) { 
            $HTML = $WAPI->generaInscripcio($InscripcioCodificada);
        } else {
            $HTML = $WAPI->generaResguard($InscripcioCodificada);
        }

        if($testMail) {
            $WAPI->EnviaEmailInscripcio( $InscripcioCodificada, 'albert.johe@gmail.com' );
        }

        // Retornem 0 perquè ensenyem l'HTML tal qual va. 
        return array($HTML, '0');
    }


    protected function ExisteixDNI() {
        
        if( isset( $this->request['DNI'] ) ) {
            $WAPI = new WebApiController();
            $ExisteixDNI = $WAPI->ExisteixDNI($this->request['DNI']);
            return array(array('ExisteixDNI' => $ExisteixDNI), 200);
        } 

    }    

    /*

    array(2) {
  ["post"]=>
  array(7) {
    ["DNI"]=> string(9) "40359575A"
    ["Nom"]=> string(0) ""
    ["Cog1"]=> string(0) ""
    ["Cog2"]=> string(0) ""
    ["Email"]=> string(0) ""
    ["Telefon"]=> string(0) ""
    ["QuantesEntrades"]=> string(1) "1"
  }
  ["files"]=> array(0) {}
}

    */    

    protected function AltaUsuariSimple() {
        
        //Agafo el DNI, Nom, Email i Telèfon... de contrasenya poso un número aleatori. 
        $DNI = isset($this->request['post']['DNI']) ? $this->request['post']['DNI'] : '';
        $Nom = isset($this->request['post']['Nom']) ? $this->request['post']['Nom'] : '';
        $Cog1 = isset($this->request['post']['Cog1']) ? $this->request['post']['Cog1'] : '';
        $Cog2 = isset($this->request['post']['Cog2']) ? $this->request['post']['Cog2'] : '';
        $Email = isset($this->request['post']['Email']) ? $this->request['post']['Email'] : '';        
        $Telefon = isset($this->request['post']['Telefon']) ? $this->request['post']['Telefon'] : '';

        $Municipi = isset($this->request['post']['Municipi']) ? $this->request['post']['Municipi'] : '';
        $Genere = isset($this->request['post']['Genere']) ? $this->request['post']['Genere'] : '';
        $AnyNaixement = isset($this->request['post']['AnyNaixement']) ? $this->request['post']['AnyNaixement'] : '';

        $QuantesEntrades = isset($this->request['post']['QuantesEntrades']) ? $this->request['post']['QuantesEntrades'] : '';        
        $ActivitatId = isset($this->request['post']['ActivitatId']) ? $this->request['post']['ActivitatId'] : 0;
        $CicleId = isset($this->request['post']['CicleId']) ? $this->request['post']['CicleId'] : 0;        

        $WAPI = new WebApiController();
        try {
            $Matricules = $WAPI->NovaInscripcioSimple($DNI, $Nom, $Cog1, $Cog2, $Email, $Telefon, $Municipi, $Genere, $AnyNaixement, $QuantesEntrades, $ActivitatId, $CicleId);
        } catch( Exception $e) { return array( array('matricules' => array(), 'error' => $e->getMessage()), 200); }
              
        return array( array('matricules' => $Matricules, 'error' => '' ), 200 );

    }

    /**
     * @Params accio
     * @Params idUsuari
     * @Params AuthToken
    */     
    protected function getActivitats() {
                                             
        $mode = 'home';

        // Només te un post el filtre d'activitats
        if(isset($this->request['post'])) {
            $P = $this->request['post'];
            $mode = $P['mode'];
        } else {                            
            $mode = $this->request['mode'];
        }

        $WEB = new WebController();
                        
        try {
            switch($mode) {
                case 'home':                     
                    $EXTRES = $WEB->viewHome();
                break;

                case 'cicles':                    
                    $idC = $this->request['idCicle'];                                
                    $EXTRES = $WEB->viewCicles( $idC );                    
                break;

                case 'activitats':                                      
                    $Filtres = json_decode($this->request['post']['Filtres'], true);
                    $idT = $this->request['post']['idTipus'];
                    
                    $EXTRES = $WEB->viewActivitats( $idT, $Filtres );
                break;

                case 'detall':                    
                    $idA = $this->request['idActivitat'];                    
                    $EXTRES = $WEB->viewDetall( $idA ); 
                break;

                case 'pagina': 
                    $idN = $this->request['idNode'];                                        
                    $EXTRES = $WEB->viewPagina( $idN );
                break;

                case 'calendari':
                    $EXTRES = $WEB->viewCalendari();
                break;

            }            

            // Sempre carreguem el menú
            $EXTRES["Menu"] = $WEB->getMenu();              

            return array($EXTRES, 200);

        } catch (PDOException $e) { return array( $e->getMessage(), 500); 
        } catch (Exception $e) { return array( $e->getMessage(), 500); } 

        
    }    


 }

 ?>
