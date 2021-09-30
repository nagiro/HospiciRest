<?php 

require_once BASEDIR."Database/DB.php";

class HorarisModel extends BDD {

    const FIELD_Actiu = "Actiu";
    const FIELD_ActivitatId = "ActivitatId";
    const FIELD_Avis = "Avis";
    const FIELD_Dia = "Dia";
    const FIELD_Espectadors = "Espectadors";
    const FIELD_Estat = "Estat";
    const FIELD_HoraFi = "HoraFi";
    const FIELD_HoraInici = "HoraInici";
    const FIELD_HoraPost = "HoraPost";
    const FIELD_HoraPre = "HoraPre";
    const FIELD_HorariId = "HorariId";
    const FIELD_IsEntrada = "IsEntrada";
    const FIELD_Places = "Places";
    const FIELD_Preu = "Preu";
    const FIELD_PreuR = "PreuR";
    const FIELD_Responsable = "Responsable";
    const FIELD_SiteId = "SiteId";
    const FIELD_Titol = "Titol";    

    public function __construct() {

        $OldFields = array("actiu", "Activitats_ActivitatID", "Avis", "Dia", "Espectadors", "Estat", "HoraFi", "HoraInici", "HoraPost", "HoraPre", "HorarisID", "isEntrada", "Places", "Preu", "PreuR", "Responsable", "site_id", "Titol");
        $NewFields = array(self::FIELD_Actiu, self::FIELD_ActivitatId, self::FIELD_Avis, self::FIELD_Dia, self::FIELD_Espectadors, self::FIELD_Estat, self::FIELD_HoraFi, self::FIELD_HoraInici, self::FIELD_HoraPost, self::FIELD_HoraPre, self::FIELD_HorariId, self::FIELD_IsEntrada, self::FIELD_Places, self::FIELD_Preu, self::FIELD_PreuR, self::FIELD_Responsable, self::FIELD_SiteId, self::FIELD_Titol );
        parent::__construct("horaris", "HORARIS", $OldFields, $NewFields );                        

    }

    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

}

?>