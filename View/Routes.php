<?php 


class Router {

    public $method;
    public $request;

    public $urlSecured = array(
        'admin',        
    );

    public $urlOpen = array(        
        '',     //Empty route
        'detall',
        'activitats',
        'cicles', 
        'pagina',
        'inscripcio'
    );

    public function __construct($SERVER) {

        $this->method = $SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $SERVER)) {
            if ($SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
                $this->method = 'PUT';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
        
        switch($this->method) {
        case 'DELETE':
        case 'POST':
            $postdata = file_get_contents("php://input");
            $this->request = json_decode($postdata, true);
            $this->request['post'] = $_POST;
            $this->request['files'] = $_FILES;            
            // $this->request = $this->_cleanInputs($_POST);
            break;
        case 'GET':                
            $this->request = $this->_cleanInputs($_GET);            
            break;
        case 'PUT':
            $this->request = $this->_cleanInputs($_GET);
            $this->file = file_get_contents("php://input");
            break;
        default:
            $this->_response('Invalid Method', 405);
            break;
        }

    }

    private function _cleanInputs($data) {
        $clean_input = Array();
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $clean_input[$k] = $this->_cleanInputs($v);
            }
        } else {
            $clean_input = trim(strip_tags($data));
        }
        return $clean_input;
    }

    public function getUrl() {
        return $this->request['request'];
    }

    public function getUrlParts() {
        return explode("/" , $this->request['request']);
    }

    public function getParameters() {
        $RET = array();
        foreach($this->request as $K => $P) { if($K != "request") $RET[$K] = $P; }        
        return $RET; 
    }    

    public function isSecured() {        
        $URL = $this->getUrlParts();
        return in_array( $URL[0], $this->urlSecured);
    }

    public function isOpen() {    
        $URL = $this->getUrlParts();        
        return in_array( $URL[0], $this->urlOpen);        
    }

    public function isCorrect() {
        return $this->isSecured() || $this->isOpen();
    }

}



?>