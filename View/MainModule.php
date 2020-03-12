<?php 

require_once VIEWDIR . 'Routes.php';
require_once CONTROLLERSDIR . 'AuthController.php';

class MainModule {

    public $Router; 
    public $Auth;
    public $Html; 

    public function __construct() {

        session_start();

        // Carrego l'enrutador que tracti la URL entrada
        $this->Router = new Router($_SERVER);
        
        // Carrego el controlador d'autenticitat i login
        $this->Auth = new AuthController();
        if( isset( $_SESSION['AuthToken'] ) ) $this->Auth->TokenDecode($_SESSION['AuthToken']);
        else $this->Auth->TokenDecode( 0 );

        // Creem la vista
        $this->executeView();

    }

    public function getView() {                
        return implode(" ", $this->Html);
    }

    public function executeView() {
        
        $this->getModuleContent('HtmlHeader.php');        
                        
        // Si la crida és una vista "oberta"        
        if ( !$this->Router->isCorrect() )  $this->executeHome();        
        if (  $this->Router->isOpen() )     $this->executeOpenView();
        if (  $this->Router->isSecured() )  $this->executeAdminView();
        
        $this->getModuleContent('HtmlFooter.php');        

    }

    private function executeOpenView() {
        $this->executeOpenUrlView( $this->Router->getUrl() );        
    }

    private function executeOpenUrlView($url) {

        switch($url) {
            case 'admin/login': $this->getModuleContent('Login.php'); break;
        }         
        
    }

    private function executeAdminView() {

        // El Router valida que s'envii un Token correcte amb la sessió                        
        
        if( !$this->Auth->isAuthenticated() ) {

            $this->getModuleContent('Login.php');            

        } else {
                    
            $this->getModuleContent('Menus.php');
            $this->executeAdminUrlView( $this->Router->getUrl() );                           

        }
    }

    private function executeAdminUrlView($url) {
        switch($url) {
            case 'admin/login': $this->getModuleContent('Login.php'); break;
            case 'admin/avui': $this->getModuleContent('Avui.php'); break;
            case 'admin/promocions': $this->getModuleContent('Promocions.php'); break;
            case 'admin/taulell': $this->getModuleContent('Taulell.php'); break;
        }         
    }


    private function getModuleContent($filename) {
        $url = VIEWDIRMOD . $filename;                        
        if (is_file($url)) {                        
            ob_start();
            include $url;
            $this->Html[] = ob_get_clean();
        } else {
            $this->Html[] = "<p>No he trobat la pàgina</p>";
        }
    }
                
}



?>
