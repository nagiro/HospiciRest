<?php 

require_once '../const.php';
require_once VIEWDIRMOD . 'HelperForm.php';
require_once VIEWDIR . 'MainModule.php';
    
error_reporting( E_ALL );
ini_set('display_errors', 1);

try {

    $MainModule = new MainModule();
    echo $MainModule->getView();

} catch (PDOException $e) { echo "Hi ha hagut algun error inesperat: " . $e->getCode(); 
} catch (Exception $e) { echo $e->getMessage(); }



?>