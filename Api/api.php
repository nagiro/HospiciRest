<?php

// http://coreymaynard.com/blog/creating-a-restful-api-with-php/

abstract class API
{
    /**
     * Property: method
     * The HTTP method this request was made in, either GET, POST, PUT or DELETE
     */
    protected $method = '';
    /**
     * Property: endpoint
     * The Model requested in the URI. eg: /files
     */
    protected $endpoint = '';
    /**
     * Property: verb
     * An optional additional descriptor about the endpoint, used for things that can
     * not be handled by the basic methods. eg: /files/process
     */
    protected $verb = '';
    /**
     * Property: args
     * Any additional URI components after the endpoint and verb have been removed, in our
     * case, an integer ID for the resource. eg: /<endpoint>/<verb>/<arg0>/<arg1>
     * or /<endpoint>/<arg0>
     */
    protected $args = Array();
    /**
     * Property: file
     * Stores the input of the PUT request
     */
     protected $file = Null;

    /**
     * Constructor: __construct
     * Allow for CORS, assemble and pre-process the data
     */
    public function __construct($request) {

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
            header('Access-Control-Allow-Methods: GET, POST, PUT');
            header('Access-Control-Allow-Headers: X-Custom-Header');
            header('Content-Type: text/html; charset=utf-8');
        }

        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

                exit;
        }
        
        header("Access-Control-Allow-Methods: *");
        header("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept");
        header("Content-Type: application/json");        

        $this->args = explode('/', rtrim($request, '/'));
        unset($this->args[0]); // Esborro el primer perquè és part de la URL per arribar a l'api.
        $this->endpoint = array_shift($this->args);
        if (array_key_exists(0, $this->args) && !is_numeric($this->args[0])) {
            $this->verb = array_shift($this->args);
        }

        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'PUT') {
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

    public function processAPI() {
        try {
            if (method_exists($this, $this->endpoint)) {
                $RES = $this->{$this->endpoint}($this->args);
                if($RES[1] == 0) {
                    return $this->_responseRawHtml($RES[0]);
                } else { return $this->_response($RES[0], $RES[1]); }
            }
            return $this->_response("No Endpoint: $this->endpoint", 404);
        } catch(Exception $e) { return $this->_response( $e->getMessage(), 500); }
    }

    private function _response($data, $status = 200) {
        header("HTTP/1.1 " . $status . " " . $this->_requestStatus($status));        
        return json_encode($data, JSON_NUMERIC_CHECK);
    }

    private function _responseRawHtml($data) {
        header_remove();
        header('Content-type: text/html');
        header("HTTP/1.1 200 " . $this->_requestStatus(200));        
        return $data;
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

    private function _requestStatus($code) {
        $status = array(
            200 => 'OK',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }


}

?>
