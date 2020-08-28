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
//        $this->Auth->TokenDecode($_SESSION['AuthToken']);                

        try {
            $this->dbh = new BDD("","",array(),array());            
        } catch (Exception $e) { return array($e->getMessage(), 500); }        
        
    }

    protected function GeneraResguard() {
        $WAPI = new WebApiController();
        $InscripcioCodificada = (isset($_GET['i'])) ? $_GET['i'] : '';        
        $isGrup = (isset($_GET['g']));
        $testMail = (isset($_GET['m']));
        $UrlDesti = (isset($_GET['u'])) ? base64_decode($_GET['u']) : 'https://www.casadecultura.cat';

        if($isGrup) { $HTML = $WAPI->generaResguard( $InscripcioCodificada, $UrlDesti, 0 ); }
    if($testMail) { /* $WAPI->EnviaEmailInscripcio( $InscripcioCodificada, 'albert.johe@gmail.com' ); */ }

        // Retornem 0 perquè ensenyem l'HTML tal qual va. 
        return array($HTML, '0');
    }


    /**
     * Funció que es crida des del TPV per validar el pagament
     */
    protected function getTpv() {
        $WAPI = new WebApiController();
        $WAPI->getTpv($_REQUEST, false);
    }

    /**
     * Funció que es crida des del TPV com a URL de OK i imprimeix l'entrada
     */
    protected function getTpvOk() {
        $WAPI = new WebApiController();
        $HTML = $WAPI->getTpv($_REQUEST, true);
        return array($HTML, '0');
    }    

    protected function ExisteixDNI() {
        
        if( isset( $this->request['DNI'] ) ) {
            $WAPI = new WebApiController();
            
            $DNI = isset($this->request['DNI']) ? $this->request['DNI'] : '';
            $idCurs = isset($this->request['idCurs']) ? $this->request['idCurs'] : '';            
            $IsRestringit = isset($this->request['IsRestringit']) ? $this->request['IsRestringit'] : '';            
            
            $ExisteixDNI = $WAPI->ExisteixDNI($DNI, $idCurs, $IsRestringit);
            return array($ExisteixDNI, 200);
        } 

    }    

    /**
     * Funció que realitza la inscripció d'usuaris a través del web. 
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
        $CursId = isset($this->request['post']['CursId']) ? $this->request['post']['CursId'] : 0;        

        $TipusPagament = isset($this->request['post']['TipusPagament']) ? $this->request['post']['TipusPagament'] : 0;        
        $DescompteAplicat = isset($this->request['post']['DescompteAplicat']) ? $this->request['post']['DescompteAplicat'] : -1;
        
        $Localitats = isset($this->request['post']['Localitats']) ? json_decode($this->request['post']['Localitats'], true) : array();

        $Token = isset($this->request['post']['Token']) ? json_decode($this->request['post']['Token'], true) : array();

        $UrlDesti = isset($this->request['post']['UrlDesti']) ? $this->request['post']['UrlDesti'] : 0;        

        $WAPI = new WebApiController();
        try {
            $RET = $WAPI->NovaInscripcioSimple($DNI, $Nom, $Cog1, $Cog2, $Email, $Telefon, $Municipi, $Genere, $AnyNaixement, $QuantesEntrades, $ActivitatId, $CicleId, $CursId, $TipusPagament, $UrlDesti, $DescompteAplicat, $Localitats, $Token);
        } catch( Exception $e) { return array( array('matricules' => array(), 'error' => $e->getMessage()), 200); }
              
        return array( array('AltaUsuari' => $RET, 'error' => '' ), 200 );

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
                    /* $idA = $this->request['idActivitat']; */
                    /* $EXTRES = $WEB->viewDetall( $idA , 0 );  */
                break;

                case 'inscripcio':                    
                    /*
                    $idC = $this->request['idCurs'];
                    $EXTRES = $WEB->viewDetall( 0 , $idC ); 
                    */
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
