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
        return ($this->TokenLiteral !== 0);        
    }

    public function getToken() {
        return $this->TokenLiteral;
    }    

    public function TokenDecode($Token) {        
        $this->TokenLiteral = $Token;
        $this->TokenArray = array();
        $this->idUsuari = 0;
        $this->idSite = 0;
        $this->isAdmin = false;
        
        // Desgranem el token Configuració . Valors . Signature
        if( !is_null($Token) ) {
            $this->TokenArray = explode('.', $Token);                
            if ( sizeof($this->TokenArray) == 3 ) {
                if ($this->TokenArray[2] == hash_hmac('sha256', $this->TokenArray[0] . "." . $this->TokenArray[1], date('d', time()))){
                    $this->TokenArray[1] = json_decode(base64_decode($this->TokenArray[1]), true);
                    $this->TokenArray[0] = json_decode(base64_decode($this->TokenArray[0]), true);                
                    $this->idUsuari = $this->TokenArray[1]['idUser'];
                    $this->idSite = $this->TokenArray[1]['idSite'];
                    $this->isAdmin = $this->TokenArray[1]['isAdmin'];
                    return true;
                } else return false;
            } else return false;
        } else return false;
                
    }

    public function TokenEncode($idU, $idS, $isAdmin) {        
        $Token[0] = base64_encode(json_encode(array('typ' => 'JWT', 'alg' => 'HS256')));
        $Token[1] = base64_encode(json_encode(array('idUser'=> $idU, 'idSite' => $idS, 'isAdmin' => $isAdmin )));
        $Token[2] = hash_hmac('sha256', $Token[0] . "." . $Token[1], date('d', time()));
        $this->TokenLiteral = implode('.', $Token);
        $this->TokenDecode($this->TokenLiteral);
    }
}


?>