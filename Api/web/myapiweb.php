<?php

require_once APIDIR . 'api.php';
require_once CONTROLLERSDIR . 'ControllersWebLoader.php';
require_once VIEWDIRMOD . 'HelperForm.php';

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
    
    /**
     * Funció que és cridada per guardar el formulari de Wufoo quan s'ha creat.
     */
    protected function GuardaFormulariWufoo() {        
        // Si rebo un formulari de Wufoo, guardo el formulari                         
        if($_POST['HandshakeKey'] == "CasaDeCultura.") {

            require_once( DATABASEDIR . 'WufooClass.php' );                             
            $WC = new WufooClass();
            $WC->saveEntry( $_POST );
        }        
    }

    protected function GeneraResguard() {
        $WAPI = new WebApiController();
        $InscripcioCodificada = (isset($_GET['i'])) ? $_GET['i'] : '';        
        $isGrup = (isset($_GET['g']));
        $testMail = (isset($_GET['m']));
        $UrlDesti = (isset($_GET['u'])) ? base64_decode($_GET['u']) : 'https://www.casadecultura.cat';
        $GenKey = (isset($_GET['k']));
        $DownloadDocument = (isset($_GET['d']));

        if($isGrup) { 
            $HTML = $WAPI->generaResguard( $InscripcioCodificada, $UrlDesti, 0 ); 
            $HTML = str_replace('@@DISPLAY_MAIL@@',  'none', $HTML); // Si encara hi ha el display... l'ensenyem perquè es vegi que s'ha enviat el correu.

            if($DownloadDocument) { 
                $fitxer = tempnam(sys_get_temp_dir(), "ENT");
                file_put_contents($fitxer, $HTML);
                $baseFitxer = 'entrades.html';
                header("Content-Type: application/octet-stream");            
                header("Content-Length: ".filesize($fitxer));            
                header("Content-Disposition:attachment;filename=" .$baseFitxer);
                readfile($fitxer);       
                die;                                         
            }

        }
        if($testMail) { $HTML = $WAPI->ReenviaEmailInscripcio( $InscripcioCodificada, $UrlDesti ); }
        if($GenKey) { $HTML = "El valor {$_GET['k']} és igual a " . $WAPI->Encrypt($_GET['k']); }


        // Retornem 0 perquè ensenyem l'HTML tal qual va. 
        return array($HTML, '0');
    }


    /**
     * Funció que es crida des del TPV per validar el pagament
     */
    protected function validaCodi() {
        $WAPI = new WebApiController();
        $QREntrada = (isset($_POST['QR'])) ? $_POST['QR'] : '';        
        if(isset($_POST['idMatricula'])) $QREntrada = $WAPI->Encrypt($_POST['idMatricula']);        
        return array($WAPI->ValidaQR($QREntrada), 200);
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

    protected function PutOperacioDatafon() {
        $WAPI = new WebApiController();        

        $Matricules = isset($this->request['post']['Matricules']) ? json_decode($this->request['post']['Matricules'], true) : array();
        $CodiOperacio = isset($this->request['post']['CodiOperacio']) ? $this->request['post']['CodiOperacio'] : '';            
        $PagatCorrectament = isset($this->request['post']['PagatCorrectament']) ? $this->request['post']['PagatCorrectament'] : '';            
        $UrlDesti = isset($this->request['post']['UrlDesti']) ? $this->request['post']['UrlDesti'] : '';            
        
        if($PagatCorrectament == '1'){
            $WAPI->setCodiOperacio($CodiOperacio, $Matricules, $PagatCorrectament);        
            $WAPI->ReenviaEmailInscripcio( $Matricules[0], $UrlDesti );
        }

        return array('', '0');
    }    

    /**
    * Funció que ens diu si un DNI existeix o no a la nostra base de dades
    */    
    protected function ExisteixDNI() {
        
        if( isset( $this->request['DNI'] ) ) {
            $WAPI = new WebApiController();            
            $DNI = isset($this->request['DNI']) ? $this->request['DNI'] : '';
            $ExisteixDNI = $WAPI->ExisteixDNI($DNI);                                                                                                                
            return array($ExisteixDNI, 200);
        } 

    }    

    /**
    * Funció que ens diu si un usuari per un curs quines restriccions té
    */
    protected function getPermisosUsuarisCursos() {
        $WAPI = new WebApiController();         
        $DNI = isset($this->request['DNI']) ? $this->request['DNI'] : '';            
        $IdUsuariDecrypted = isset($this->request['IdUsuariEncrypted']) ? $WAPI->Decrypt($this->request['IdUsuariEncrypted']) : '';            
        $idCurs = isset($this->request['idCurs']) ? $this->request['idCurs'] : '';            
        $IsRestringit = isset($this->request['IsRestringit']) ? $this->request['IsRestringit'] : '';            
        $RET = $WAPI->getPermisosUsuariCursos($DNI, $IdUsuariDecrypted, $idCurs, $IsRestringit);
        return array($RET, 200);
    }

    /**
    * Funció que gestiona baixa o 
    **/
    protected function AccionsExisteixDNI() {
        $WAPI = new WebApiController();

        $idUsuari = isset($this->request['I']) ? $WAPI->Decrypt($this->request['I']) : '';
        $CURS = isset($this->request['C']) ? $this->request['C'] : '';
        $Accio = isset($this->request['A']) ? $this->request['A'] : '';
        
        
        $RET = 0;
        switch($Accio) {            
            case 'B': $RET = $WAPI->BaixaInscripcioWeb($idUsuari, $CURS); break;
            case 'R': $RET = $WAPI->ReenviaEmailInscripcioWeb( $idUsuari, $CURS ); break;
        }
        
        return array($RET, 200);
    }

    protected function getLlistatTeatre() {
        $idActivitatCurs = isset($this->request['idActivitatCurs']) ? $this->request['idActivitatCurs'] : '';        
        $WAPI = new WebApiController();
        return array($WAPI->getLlistatTeatre($idActivitatCurs), 200);                
    }


    /**
    * Alta usuari simple. Retorna el mateix que la funció ExisteixDNI
     */

    protected function NouUsuari() {

        //Agafo el DNI, Nom, Email i Telèfon... de contrasenya poso un número aleatori. 
        $WAPI = new WebApiController();
        $DNI = isset($this->request['post']['DNI']) ? $this->request['post']['DNI'] : '';
        $Nom = isset($this->request['post']['Nom']) ? $this->request['post']['Nom'] : '';
        $Cog1 = isset($this->request['post']['Cog1']) ? $this->request['post']['Cog1'] : '';
        $Cog2 = isset($this->request['post']['Cog2']) ? $this->request['post']['Cog2'] : '';
        $Email = isset($this->request['post']['Email']) ? $this->request['post']['Email'] : '';        
        $Telefon = isset($this->request['post']['Telefon']) ? $this->request['post']['Telefon'] : '';

        $Municipi = isset($this->request['post']['Municipi']) ? $this->request['post']['Municipi'] : '';
        $Genere = isset($this->request['post']['Genere']) ? $this->request['post']['Genere'] : '';
        $AnyNaixement = isset($this->request['post']['AnyNaixement']) ? $this->request['post']['AnyNaixement'] : '';
        
        try {
            
            $NouIdUsuari = $WAPI->NouUsuari($DNI, $Nom, $Cog1, $Cog2, $Email, $Telefon, $Municipi, $Genere, $AnyNaixement);
            return array( array('IdUsuariEncrypted' => $WAPI->Encrypt($NouIdUsuari), 'ExisteixDNI' => ($NouIdUsuari > 0)) , 200);

        } catch( Exception $e) { return array( array('matricules' => array(), 'error' => $e->getMessage()), 200); }

        
        
    }


    /**
     * Funció que realitza la inscripció d'usuaris a través del web. 
     */
    protected function NovaInscripcio() {

        $WAPI = new WebApiController();

        $IdUsuari = isset($this->request['post']['IdUsuariEncrypted']) ? $WAPI->Decrypt($this->request['post']['IdUsuariEncrypted']) : '';        
        $QuantesEntrades = isset($this->request['post']['QuantesEntrades']) ? $this->request['post']['QuantesEntrades'] : '';        
        $ActivitatId = isset($this->request['post']['ActivitatId']) ? $this->request['post']['ActivitatId'] : 0;
        $CicleId = isset($this->request['post']['CicleId']) ? $this->request['post']['CicleId'] : 0;        
        $CursId = isset($this->request['post']['CursId']) ? $this->request['post']['CursId'] : 0;        

        $TipusPagament = isset($this->request['post']['TipusPagament']) ? $this->request['post']['TipusPagament'] : 0;        
        $DescompteAplicat = isset($this->request['post']['DescompteAplicat']) ? $this->request['post']['DescompteAplicat'] : -1;
        
        $Localitats = isset($this->request['post']['Localitats']) ? json_decode($this->request['post']['Localitats'], true) : array();

        $Token = isset($this->request['post']['Token']) ? json_decode($this->request['post']['Token'], true) : array();

        $UrlDesti = isset($this->request['post']['UrlDesti']) ? $this->request['post']['UrlDesti'] : 0;        

        $DadesExtres = isset($this->request['post']['DadesExtres']) ? $this->request['post']['DadesExtres'] : null;
        
        try {
            $RET = $WAPI->NovaInscripcioSimple($IdUsuari, $QuantesEntrades, $ActivitatId, $CicleId, $CursId, $TipusPagament, $UrlDesti, $DescompteAplicat, $Localitats, $Token, $DadesExtres);
        } catch( Exception $e) { return array( array('matricules' => array(), 'error' => $e->getMessage()), 200); }
              
        return array( array('AltaUsuari' => $RET, 'error' => '' ), 200 );

    }

    protected function ajaxReservaEspais() {
     
        $WAPI = new WebApiController();
        $Accio = isset($this->request['post']['Accio']) ? $this->request['post']['Accio'] : ''; 
        if(empty($Accio)) $Accio = isset($this->request['Accio']) ? $this->request['Accio'] : ''; 
        
        switch($Accio){
            case 'OcupacioEspai': 
                $IdEspai = isset($this->request['post']['IdEspai']) ? $this->request['post']['IdEspai'] : '';
                $MesActual = isset($this->request['post']['MesActual']) ? $this->request['post']['MesActual'] : '';
                $AnyActual = isset($this->request['post']['AnyActual']) ? $this->request['post']['AnyActual'] : '';                
                $RET = $WAPI->getOcupacioEspai($IdEspai, $MesActual, $AnyActual);
                break;
            case 'addReservaEspai':                 
                $FormulariReservaEspai = isset($this->request['post']['DadesFormulari']) ? json_decode($this->request['post']['DadesFormulari'], true) : array();        
                $RET = array('FormulariReservaComplet' => $WAPI->setReservaEspai($FormulariReservaEspai, true) );
                break;
            case 'getEspaisDisponibles':
                $IdSite = isset($this->request['IdSite']) ? $this->request['IdSite'] : '';
                $RET = $WAPI->getEspaisDisponibles($IdSite);
        }

        return array($RET, 200);
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

    protected function getCulturaVirtual() {
        require_once AUXDIR . 'Culturavirtual/Auxiliar_CulturaVirtual.php';        
        if( isset($this->request['idS']) ) {
            $CV = new Auxiliar_CulturaVirtual();
            return array($CV->loadActivitatsFutures( $this->request['idS'] ), 200);
        } else {
            throw new Exception('No hi ha cap site amb aquesta codificació.');
        }
    }


    /**
     * Funció Upload GENÈRICA per arxius dins de downloads ( Premsa, general... )
     * Cal entrar DNI + Contrasenya i si és correcte, llavors pots carregar un arxiu a downloads
     */
    protected function UploadFrontEnds() {

        //Miro quina acció he de fer... nota de premsa (np)
        if(!isset($this->request['post']['accio'])) return array(400, "Acció incorrecta.");
        $Accio = $this->request['post']['accio'];

        //Miro quina acció he de fer... nota de premsa (np)
        if(!isset($this->request['post']['SiteId'])) return array(400, "No hi ha un site correcte.");
        $SiteId = $this->request['post']['SiteId'];

        // L'usuari és correcte i la contrasenya també
//        if(!$this->Auth->doLogin($this->request['post']['Usuari'], $this->request['post']['Password'], $this->request['post']['SiteId']))
//            return array("Autentificació fallida!", 400);

        $WAPI = new WebApiController();
        if( $Accio == 'NotaPremsaGenera' ||  $Accio == 'NotaPremsaPublica' ) {

            if(!isset($this->request['post']['html'])) return array("No hi ha l'html.", 400);
            if(!isset($this->request['files']['file_imatge'])) return array("No hi ha cap imatge.", 400);
            if(!isset($this->request['files']['file_notapremsa'])) return array("No hi ha cap arxiu de nota de premsa.",400);
            $Dades = array($this->request['post']['html']);
            $Arxius = array($this->request['files']['file_imatge'], $this->request['files']['file_notapremsa']);
            $RES = $WAPI->doUploadFrontEnds( $SiteId, $Accio , $Dades , $Arxius );
            
        }
        
        
                                                    
        if($RES[0]) return array($RES[1], 200);
        else return array($RES[1], 400);            
        
    }    

    /**
     * Funció Upload per carregar arxius en relació a Pàgines de la web
     * Cal entrar DNI + Contrasenya i si és correcte, llavors pots carregar un arxiu a downloads
     */
    protected function UploadPaginaFileApi() {

        //Comprovem si l'usuari i contrasenya són correctes
        if(!isset($this->request['post']['Usuari']) || !isset($this->request['post']['Password'])) 
            return array("No s'ha entrat usuari o contrasenya", 400);
        
        // L'usuari és correcte i la contrasenya també
        if(!$this->Auth->doLogin($this->request['post']['Usuari'], $this->request['post']['Password'], $this->request['post']['SiteId']))
            return array("Autentificació fallida!", 400);
        
        //Comprovem que hem entrat un directori i nom d'arxiu. I que existex el directori. 
        if(!isset($this->request['post']['Pagina']) || !isset($this->request['post']['NomArxiu']))
            return array("Nom o directori incorrecte!", 400);
                            
        $WAPI = new WebApiController();
        $RES = $WAPI->doUploadPaginaFile($this->request['post']['Pagina'] , $this->request['post']['NomArxiu'] , $this->request['files']['file']["tmp_name"] );
        
        if($RES[0]) return array($RES[1], 200);
        else return array($RES[1], 400);            
        
    }    

     /**
     * (Deprecated)
     * Funció Upload per carregar arxius agafats del servidor de la CCG i carregar-los al web.
     * Cal entrar DNI + Contrasenya i si és correcte, llavors pots carregar un arxiu a downloads
     */
    protected function getUploadFtp() {
        require_once AUXDIR . 'UploadFtp/Auxiliar_UploadFtp.php';        
        if( isset($this->request['idS']) && isset($this->request['node']) ) {
            $CV = new Auxiliar_UploadFtp($this->request['idS']);
            return array($CV->loadArxiusNous( $this->request['node'] ), 200);
        } else {
            throw new Exception('No hi ha cap site i node amb aquesta codificació.');
        }
    }    

    protected function getEmailWufooEspais() {        
        if( isset($this->request['idS']) ) {
            // Capturo totes les activitats a 15 dies vista i reviso si ha firmat que ok a la reserva i si hi ha un Email al camp responsable. 
            
            return array($CV->loadArxiusNous( $this->request['node'] ), 200);
        } else {
            throw new Exception('No hi ha cap site i node amb aquesta codificació.');
        }
    }    

    /**
     * 
     */
    protected function getXMLActivitats() {

        $RET = array();

        $Action = (isset($this->request['post']['Action'])) ? $this->request['post']['Action'] : '';
        $DataInici = (isset($this->request['post']['DataInici'])) ? $this->request['post']['DataInici'] : '';
        $DataFi = (isset($this->request['post']['DataFi'])) ? $this->request['post']['DataFi'] : '';
        $SiteId = (isset($this->request['post']['SiteId'])) ? $this->request['post']['SiteId'] : '';
        $User = (isset($this->request['post']['User'])) ? $this->request['post']['User'] : '';
        $Password = (isset($this->request['post']['Password'])) ? $this->request['post']['Password'] : '';
        $Html = (isset($this->request['post']['Html'])) ? $this->request['post']['Html'] : '';

        if( $Action != '' && $SiteId != '' ) {
            
            $WAPI = new WebApiController();
            try {
                if( $Action == 'load' ) {
                    if( $DataInici == '' || $DataFi == '') return array('Paràmetres incorrectes', 400);
                    $RET = $WAPI->getXMLActivitats( $DataInici,  $DataFi, $SiteId );
                } elseif( $Action == 'save' ) {
                    if( $User == '' || $Password == '' || $Html == '') return array('Paràmetres incorrectes', 400);
                    if(!$this->Auth->doLogin( $User, $Password, $SiteId ) ) return array("Autentificació fallida!", 400);                
                    else {
                        $RET = $WAPI->saveButlleti( $Html, $SiteId );
                    }
                }
            } catch (Exception $e) { return array($e->getMessage(), 500); }
            
        }

        return array($RET, 200);
        
    }

    /**
     * Funció que entra un Site i un Usuari i retorna hores treballades per dia, setmana, mes i any.
     * A més pot portar una acció = on / off / idle
     *
     * @return void
     */
    protected function apiControlHorari() {        

        $idS = ( isset($this->request['idS']) && is_numeric($this->request['idS'] ) ) ? $this->request['idS'] : 0;
        $idU = ( isset($this->request['idU']) && is_numeric($this->request['idS'] ) ) ? $this->request['idU'] : 0;
        $Accio = isset($this->request['accio']) ? $this->request['accio'] : '';
        $MesAny = isset($this->request['mesAny']) ? $this->request['mesAny'] : '';

        $idS = isset($this->request['post']['idS']) ? $this->request['post']['idS'] : $idS;
        $idU = isset($this->request['post']['idU']) ? $this->request['post']['idU'] : $idU;
        $Accio = isset($this->request['post']['accio']) ? $this->request['post']['accio'] : $Accio;        
        $MesAny = isset($this->request['post']['mesAny']) ? $this->request['post']['mesAny'] : '';
        $DadesFormulari = isset($this->request['post']['Dades']) ? json_decode($this->request['post']['Dades'], true) : array();

        if( $idS > 0 && $idU > 0 && ( $Accio == 'idle' || $Accio == 'on' || $Accio == 'off' || $Accio == 'save') ) {

            $WAPI = new WebApiController();                        
            return array($WAPI->doControlHorari( $idS, $idU, $Accio, $MesAny, $DadesFormulari ), 200);

        } else {
            throw new Exception('Paràmetres incorrectes.');
        }
    }            


 }

 ?>
