<?php 

require_once BASEDIR.'/Database/Tables/UsuarisModel.php';

class AuthController {

    public $UsuarisModel; 
    public $TokenLiteral;    
    public $idUsuari; 
    public $idSite; 
    public $isAdmin;
    public $UM;
    public $TokenArray;

    public function __construct() {        
        if(session_status() != PHP_SESSION_ACTIVE) session_start();
        $this->UM = new UsuarisModel();
        $this->TokenLiteral = '';
        $this->TokenArray = array();
        $this->idUsuari = 0;
        $this->idSite = 0;
        $this->isAdmin = false;
    }

    public function hasSessionToken() {
        return (isset($_SESSION['AuthToken']));
    }

    public function doLogin($login, $password, $idS) {
        
        // Fem el login, i aconseguim un token a partir del seu usuari                
        $RET = $this->UM->doLogin($login, $password, $idS);
        if(sizeof($RET) > 0) {

            $this->EncodeToken($RET[0]['USUARIS_IdUsuari'], $idS, true );
            return true;

        } else {

            $this->idUsuari = 0;
            $this->idSite = 0;
            $this->isAdmin = 0;
            $this->TokenLiteral = 0;
            return false;

        }        
    }    

    public function isAdmin() {        
        return $this->isAdmin;
    }

    public function isAuthenticated() {        
        return ($this->idUsuari > 0 && $this->idSite > 0);        
    }

    public function getSiteIdIfAdmin() {
        return ( $this->isAdmin ) ? $this->idSite : 0;
    }

    public function getToken() {
        return $this->TokenLiteral;
    }    

    /**
     * $Token: String encryptat que s'ha creat amb EncodeToken
     * Si és 0, no hi ha token i per tant, reinicialitzem
    */    
    public function DecodeToken($Token = '') {
        
        // Mirem quin token carreguem. 
        if(strlen($Token) == 0)
            if(strlen($this->TokenLiteral) > 0) $Token = $this->TokenLiteral;
            elseif( $this->hasSessionToken() ) $Token = $_SESSION['AuthToken'];
            else $Token = '';
        else { 
            $Token = $Token; 
            $this->TokenLiteral = $Token;
            $_SESSION['AuthToken'] = $Token;
        }
        
        $Text = json_decode($this->Decrypt($Token), true);            

        if( ! is_null($Text)) {
            
            $this->idUsuari = $Text['idUsuari'];
            $this->isAdmin = $Text['isAdmin'];
            $this->idSite = $Text['idSite'];        

        } else {

            $this->idUsuari = 0;
            $this->isAdmin = 0;
            $this->idSite = 0;        

        }        
    }

    public function EncodeToken($idUsuari, $idSite, $isAdmin) {
        $Text = json_encode(array('idUsuari' => $idUsuari, 'idSite' => $idSite, 'isAdmin' => $isAdmin ));
        $Text = $this->Encrypt($Text);
        
        $this->TokenLiteral = $Text;

        $_SESSION['AuthToken'] = $this->TokenLiteral;

        return $Text;
    }

    public function Encrypt($id) { return base64_encode(openssl_encrypt($id, 'aes128', '(ccg@#).', 0, '45gh354645gh3546' )); }
    public function Decrypt($id) { return openssl_decrypt(base64_decode($id), 'aes128', '(ccg@#).', 0, '45gh354645gh3546'); }

}


?>