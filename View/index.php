<?php 

require_once '../const.php';
require_once VIEWDIRMOD . 'HelperForm.php';
require_once VIEWDIR . 'MainModule.php';
    
error_reporting( E_ALL );
ini_set('display_errors', 1);

$MainModule = new MainModule();
echo $MainModule->getView();


?>