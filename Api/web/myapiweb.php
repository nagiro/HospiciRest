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

        } catch (PDOException $e) { return array($e->getMessage(), 500); }
        
    }    


 }

 ?>
