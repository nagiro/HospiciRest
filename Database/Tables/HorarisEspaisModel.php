<?php 

require_once BASEDIR."Database/DB.php";

class HorarisEspaisModel extends BDD {

    public $E;

    public function __construct() {

        $this->$E = array(
            "he.actiu as HE_ACTIU",
            "he.Espais_EspaiID as HE_ESPAIS_ESPAIID",
            "he.Horaris_HorarisID as HE_HORARIS_HORARISID",
            "he.idEspaiextern as HE_IDESPAIEXTERN",
            "he.idHorarisEspais as HE_IDHORARISESPAIS",
            "he.Material_idMaterial as HE_MATERIAL_IDMATERIAL",
            "he.site_id as HE_SITE_ID"            
        );


    }
}

?>