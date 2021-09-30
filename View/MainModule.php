<?php 

require_once VIEWDIR . 'Routes.php';
require_once CONTROLLERSDIR . 'AuthController.php';
require_once CONTROLLERSDIR.  '/Web/WebController.php';
require_once BASEDIR . 'vendor/autoload.php';

class MainModule {

    public $Router; 
    public $Auth;
    public $WebController;
    public $Html;         

    public function __construct() {
        
        // Carrego l'enrutador que tracti la URL entrada
        $this->Router = new Router($_SERVER);
        
        // Carrego el controlador d'autenticitat i miro si tenim algun token
        $AuthToken = (isset($_GET['AuthToken'])) ? $_GET['AuthToken'] : '';
        $this->Auth = new AuthController();
        $this->Auth->DecodeToken($AuthToken);
        

        $this->WebController = new WebController();

        // Creem la vista
        $this->executeView();

    }

    public function getView() {                
        return implode(" ", $this->Html);
    }

    public function executeView() {
           
        // Si la crida és una vista "oberta"        
        if ( !$this->Router->isCorrect() )  $this->executeOpenUrlView(array(''));        
        if (  $this->Router->isOpen() )     $this->executeOpenView();
        if (  $this->Router->isSecured() )  $this->executeAdminView();        

    }

    private function executeOpenView() {        
        $this->executeOpenUrlView( $this->Router->getUrlParts() );        
    }

    private function executeOpenUrlView($url) {                                        
        
        $Data = array();
        $Data['HeaderData'] = $this->setHeaderData('Casa de Cultura', '/WebFiles/Web/img/LogoCCG.jpg');        

        switch($url[0]) {
            case '': 
                $this->getModuleContent('HtmlHeaderWeb.php', $Data);
                $Data = $this->WebController->ViewHome();                                
                // $this->getModuleContent('Web/home.php', base64_encode(json_encode($Data)) ); 
                $this->getModuleContent('Web/home.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
            break;
            
            // Inscripcions de la Casa de Cultura
            case 'inscripcio':
                $this->getModuleContent('HtmlHeaderWeb.php', $Data);                
                // $T = $this->Auth->EncodeToken(1, 1, 1);
                $Token = ( isset( $url[2] ) && $url[2] == 'token' && isset( $url[3] ) ) ? $url[3] : '';
                $this->Auth->DecodeToken($Token);
                $Data = $this->WebController->viewDetall( 0 , $url[1], $this->Auth->getSiteIdIfAdmin(), $Token, false );                
                $this->getModuleContent('Web/detall.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
            break;
            
            // Inscripcions de l'Hospici
            // host/inscripcions/{idCurs}/token/{token}
            // host/inscripcions/llistat/0/Casa_de_cultura_riudellots
            case 'inscripcions':                                
                //Mirem i avaluem els paràmetres de la URL
                $Token = ( isset( $url[2] ) && $url[2] == 'token' && isset( $url[3] ) ) ? $url[3] : '';
                if($Token != '') $this->Auth->DecodeToken($Token);
                $Lloc = ( isset( $url[1] ) && $url[1] == 'llistat' && isset( $url[2] ) ) ? $url[2] : 0;
                
                // Si accedim a un lloc específic, llistem tots els cursos. Altrament mostrem el curs en qüestió
                if($Lloc == 0) {                    
                    $Data = $this->WebController->viewDetall( 0 , $url[1], $this->Auth->getSiteIdIfAdmin(), $Token, true );                    
                    $CM = new CursosModel();
                    $Lloc = (isset($Data['Curs'][0][$CM->gnfnwt(CursosModel::FIELD_SiteId)])) ? $Data['Curs'][0][$CM->gnfnwt(CursosModel::FIELD_SiteId)] : 1;
                    $Data['HeaderData'] = $this->setHeaderData(null, null, $this->WebController->getSiteInfo($Lloc));
                } elseif($Lloc > 0) {                                        
                    $Data = $this->WebController->viewCursosSites( $Lloc );
                    $Data['HeaderData'] = $this->setHeaderData(null, null, $this->WebController->getSiteInfo($Lloc) );
                }

                $this->getModuleContent('HtmlHeaderWeb.php', $Data);                                
                $this->getModuleContent('Web/inscripcions_sites.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
            break;            

            // Detall de pàgina de la Casa de Cultura de Girona
            case 'detall':
                $this->getModuleContent('HtmlHeaderWeb.php', $Data);
                $Token = ( isset( $url[2] ) && $url[2] == 'token' && isset( $url[3] ) ) ? $url[3] : ''; 
                $this->Auth->DecodeToken($Token);
                $Data = $this->WebController->viewDetall( $url[1] , 0 , $this->Auth->getSiteIdIfAdmin(), $Token, false );
                $this->getModuleContent('Web/detall.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
            break;

            // Cicles de la Casa de Cultura             
            case 'cicles':
                $this->getModuleContent('HtmlHeaderWeb.php', $Data);
                $Data = $this->WebController->viewCicles($url[1]);
                $this->getModuleContent('Web/llistat.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
            break;

            // Activitats de pàgina de la Casa de Cultura de Girona
            case 'activitats':
                $this->getModuleContent('HtmlHeaderWeb.php', $Data);
                // Tipus de filtres... 
                $Filtres = $this->WebController->getUrlToFilters($url);                                                                                                        
                $Data = $this->WebController->viewActivitats( $Filtres );
                $this->getModuleContent('Web/llistat.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
            break;

            // Pàgina html de nodes estàndard de la Casa de Cultura de Girona
            case 'pagina': 
                $this->getModuleContent('HtmlHeaderWeb.php', $Data);                                
                $Data = $this->WebController->viewPagina( $url[1] );
                $this->getModuleContent('Web/pagina.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
            break;

            // Validador d'entrades de la Casa de Cultura de Girona
            case 'validador':
                if(!isset($url[1])) throw new Exception("Has d'entrar el codi d'entitat.");
                if(!is_numeric($url[1])) throw new Exception("Has d'entrar el codi d'entitat correcte."); 
                $idS = strval( $url[1] );
                $Data = $this->WebController->getActivitatsDiaValidar( $idS );
                $this->getModuleContent('HtmlHeaderWeb.php', $Data);       
                $this->getModuleContent('Web/validador.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
            break;

            // Gestió d'espais de l'Hospici
            case 'espais':                
                                
                // espais/llistat/:idSite/:TextNomSite
                if($url[1] == 'llistat'):
                    $Data['EspaisDisponibles'] = $this->WebController->getEspaisDisponibles($url[2]);
                    $Data['Site'] = $this->WebController->getSiteInfo($url[2]);
                    $Data['HeaderData'] = $this->setHeaderData(null, null, $Data['Site']);
                
                // espais/detall/:idEspai/:TextEspai
                elseif($url[1] == 'detall'):
                    $Data['EspaiDetall'] = $this->WebController->getDetallEspai($url[2]);                                                            
                    $Data['FormulariReservaEspai'] = $this->WebController->getFormulariReservaEspai($url[2]);                    
                    $SiteId = $Data['EspaiDetall']['Detall']['ESPAIS_SiteId'];
                    $Data['Site'] = $this->WebController->getSiteInfo($SiteId);                    
                    $Data['LlistaEspaisDisponiblesForm'] = $this->WebController->getEspaisDisponibles($SiteId, true);
                    $Data['HeaderData'] = $this->setHeaderData(null, null, $Data['Site']);
                endif;                                

                $this->getModuleContent('HtmlHeaderWeb.php', $Data);                                                
                $this->getModuleContent('Web/espais_sites.php', json_encode($Data) ); 
                $this->getModuleContent('HtmlFooterWeb.php');                
                
            break;            
        }         
        
    }

    /**
     * Funció que carrega un títol, imgUrl pels headers de la web.
    */
    private function setHeaderData($Titol, $ImgUrl, $SiteObject = null) {
        require_once DATABASEDIR.'Tables/SitesModel.php';
        $SM = new SitesModel();

        if(is_null($Titol) && is_null($ImgUrl)) {
            return array(
                'Nom' => $SiteObject[$SM->gnfnwt( SitesModel::FIELD_Nom)], 
                'ImgUrl' => $SiteObject[$SM->gnfnwt( SitesModel::FIELD_LogoUrl )] );
        }
        else {
            return array('Nom' => $Titol, 'ImgUrl' => $ImgUrl);
        }
    }

    private function executeAdminView() {

        // El Router valida que s'envii un Token correcte amb la sessió                                
        if( !$this->Auth->isAuthenticated() ) {
            
            $this->getModuleContent('Admin/HtmlHeaderAdmin.php');
            $this->getModuleContent('Admin/Login.php');            
            $this->getModuleContent('Admin/HtmlFooterAdmin.php');

        } else {
        
            $this->getModuleContent('Admin/HtmlHeaderAdmin.php');
            $this->getModuleContent('Admin/Menus.php');
            $this->executeAdminUrlView( $this->Router->getUrl() );                           
            $this->getModuleContent('Admin/HtmlFooterAdmin.php');

        }
    }

    private function executeAdminUrlView($url) {
                        
        switch($url) {
            case 'admin/login': $this->getModuleContent('Admin/Login.php'); break;
            case 'admin/avui':  $this->getModuleContent('Admin/Avui.php'); break; 
            case 'admin/promocions': $this->getModuleContent('Admin/Promocions.php'); break;
            case 'admin/taulell': $this->getModuleContent('Admin/Taulell.php'); break;
            case 'admin/horaris': $this->getModuleContent('Admin/Horaris.php'); break;
        }         
        
    }


    private function getModuleContent($filename, $Data = array()) {
        $url = VIEWDIRMOD . $filename;                 
        if (is_file($url)) {                                    
            ob_start();
            $this->Data = $Data;
            include $url;            
            $this->Html[] = ob_get_clean();            
        } else {
            // $this->Html[] = "<p>No he trobat la pàgina</p>";
        }
    }
                
}



?>
