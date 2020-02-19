<?php 

require_once '../../const.php';
require_once APIDIRWEB . 'myapiweb.php';

error_reporting( E_ERROR );
ini_set('display_errors', 1);

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
            
    $API = new MyAPIWeb($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    echo $API->processAPI();
    
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}

?>