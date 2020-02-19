<?php 

require_once BASEDIR."Database/DB.php";

class CiclesModel extends BDD {

    public $E;

    public function __construct() {

        $this->$E = array(
            "c.actiu as C_ACTIU",
            "c.CicleID as C_CICLEID",
            "c.dComplet as C_DCOMPLET",
            "c.dCurt as C_DCURT",
            "c.dMig as C_DMIG",
            "c.extingit as C_EXTINGIT",
            "c.Imatge as C_IMATGE",
            "c.Nom as C_NOM",
            "c.PDF as C_PDF",
            "c.site_id as C_SITE_ID",
            "c.tComplet as C_TCOMPLET",
            "c.tCurt as C_TCURT",
            "c.tMig as C_TMIG",
            "c.Visibleweb as C_VISIBLEWEB"
        );


    }
}

?>