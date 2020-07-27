<?php 

require_once BASEDIR.'/Database/Tables/UsuarisModel.php';

class AuthController {

    public $UsuarisModel; 
    public $TokenLiteral;
    public $TokenArray;
    public $idUsuari; 
    public $idSite; 
    public $isAdmin;

    public function __construct() {        
        $this->UM = new UsuarisModel();
        $this->TokenLiteral = 0;
        $this->TokenArray = array();
        $this->idUsuari = 0;
        $this->idSite = 0;
        $this->isAdmin = false;
    }

    public function doLogin($login, $password, $idS) {
        // Fem el login, i aconseguim un token a partir del seu usuari                
        return $this->UM->doLogin($login, $password, $idS);
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

    public function DecodeToken($Token) {
        $Text = json_decode($this->Decrypt($Token), true);
        $this->idUsuari = $Text['idUsuari'];
        $this->isAdmin = $Text['isAdmin'];
        $this->idSite = $Text['idSite'];        
    }

    public function EncodeToken($idUsuari, $idSite, $isAdmin) {
        $Text = json_encode(array('idUsuari' => $idUsuari, 'idSite' => $idSite, 'isAdmin' => $isAdmin ));
        $Text = $this->Encrypt($Text);
        return $Text;
    }

    public function Encrypt($id) { return base64_encode(openssl_encrypt($id, 'aes128', '(ccg@#).', 0, '45gh354645gh3546' )); }
    public function Decrypt($id) { return openssl_decrypt(base64_decode($id), 'aes128', '(ccg@#).', 0, '45gh354645gh3546'); }

}


?>