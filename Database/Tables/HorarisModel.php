<?php 

require_once BASEDIR."Database/DB.php";

class HorarisModel extends BDD {

    public function __construct() {

        $OldFields = array("actiu", "Activitats_ActivitatID", "Avis", "Dia", "Espectadors", "Estat", "HoraFi", "HoraInici", "HoraPost", "HoraPre", "HorarisID", "isEntrada", "Places", "Preu", "PreuR", "Responsable", "site_id", "Titol");
        $NewFields = array("Actiu", "ActivitatId", "Avis", "Dia", "Espectadors", "Estat", "HoraFi", "HoraInici", "HoraPost", "HoraPre", "HorariId", "IsEntrada", "Places", "Preu", "PreuR", "Responsable", "SiteId", "Titol");
        parent::__construct("horaris", "HORARIS", $OldFields, $NewFields );                        

    }

    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

}

?>