<?php 

require_once BASEDIR."Database/DB.php";

class NodesModel extends BDD {


    public function __construct() {

        $OldFields = array("idNodes", "idPare", "TitolMenu", "HTML", "isCategoria", "isPhp", "isActiva", "Ordre", "Nivell", "Url", "Categories", "site_id", "actiu");
        $NewFields = array("idNodes", "idPare", "TitolMenu", "Html", "isCategoria", "isPhp", "isActiva", "Ordre", "Nivell", "Url", "Categories", "idSite", "Actiu");
        parent::__construct("nodes", "Nodes", $OldFields, $NewFields );                        

    }

}

?>