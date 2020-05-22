<?php 

require_once BASEDIR."Database/DB.php";

class EspaisModel extends BDD {

    public function __construct() {

        $OldFields = array("actiu", "descripcio", "EspaiID", "isLlogable", "Nom", "Ordre", "site_id");
        $NewFields = array("Actiu", "Descripcio", "EspaiId", "IsLlogable", "Nom", "Ordre", "SiteId");
        parent::__construct("espais", "ESPAIS", $OldFields, $NewFields );                        

    }
    
    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

}

?>