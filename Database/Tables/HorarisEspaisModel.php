<?php 

require_once BASEDIR."Database/DB.php";

class HorarisEspaisModel extends BDD {

    public function __construct() {

        $OldFields = array("actiu", "Espais_EspaiID", "Horaris_HorarisID", "idEspaiextern", "idHorarisEspais", "Material_idMaterial", "site_id");
        $NewFields = array("Actiu", "EspaiId", "HorariId", "EspaiExternId", "HorariEspaiId", "MaterialId", "SiteId");
        parent::__construct("horarisespais", "HORARIS_ESPAIS", $OldFields, $NewFields );                        

    }
    
    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }
    
}

?>