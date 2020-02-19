<?php 

require_once BASEDIR."Database/DB.php";

class EspaisModel extends BDD {

    public $E;

    public function __construct() {
        $this->$C["Espais"] = array(
            "e.actiu as E_ACTIU",
            "e.descripcio as E_DESCRIPCIO",
            "e.EspaiID as E_ESPAIID",
            "e.isLlogable as E_ISLLOGABLE",
            "e.Nom as E_NOM",
            "e.Ordre as E_ORDRE",
            "e.site_id as E_SITE_ID"
        );

    }
}

?>