<?php

require_once BASEDIR."Database/DB.php";

class ActivitatsModel extends BDD {


    public function __construct() {

        $OldFields = array("actiu", "ActivitatID", "Categories", "Cicles_CicleID", "dComplet",           "dCurt",           "Definiciohoraris", "Descripcio", "dMig",           "Estat", "Imatge", "InfoPractica",       "isEntrada", "isImportant", "Nom", "Organitzador", "PDF", "Places", "Preu", "PreuReduit", "Publicable", "PublicaWEB",    "Responsable", "site_id", "tComplet",     "tCurt",     "TipusActivitat_idTipusActivitat", "tipusEnviament", "tMig");
        $NewFields = array("Actiu", "ActivitatId", "Categories", "CiclesCicleId" , "DescripcioCompleta", "DescripcioCurta", "DefinicioHoraris", "Descripcio", "DescripcioMig",  "Estat", "Imatge", "InformacioPractica", "IsEntrada", "IsImportant", "Nom", "Organitzador", "Pdf", "Places", "Preu", "PreuReduit", "Publicable", "PublicableWeb", "Responsable", "SiteId",  "TitolComplet", "TitolComplet", "TipusActivitatId",                "TipusEnviament", "TitolMig");
        parent::__construct("activitats", "Activitats", $OldFields, $NewFields );

    }
}

?>