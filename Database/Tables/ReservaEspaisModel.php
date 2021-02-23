<?php 

require_once BASEDIR."Database/DB.php";

class ReservaEspaisModel extends BDD {

    const ReservaEspaiId = "ReservaEspaiId";
    const Representacio = "Representacio";
    const Responsable = "Responsable";
    const TelefonResponsable = "TelefonResponsable";
    const PersonalAutoritzat = "PersonalAutoritzat";
    const PrevisioAssistents = "PrevisioAssistents";
    const EsCicle = "EsCicle";
    const Comentaris = "Comentaris";
    const Estat = "Estat";
    const UsuariId = "UsuariId";
    const Organitzadors = "Organitzadors";
    const DataActivitat = "DataActivitat";
    const HorariActivitat = "HorariActivitat";
    const TipusActe = "TipusActe";
    const Nom = "Nom";
    const IsEnregistrable = "IsEnregistrable";
    const EspaisSolicitats = "EspaisSolicitats";
    const MaterialSolicitat = "MaterialSolicitat";
    const DataAlta = "DataAlta";
    const Compromis = "Compromis";
    const Codi = "Codi";
    const Condicions = "Condicions";
    const DataAcceptacioCondicions = "DataAcceptacioCondicions";
    const ObservacionsCondicions = "ObservacionsCondicions";
    const HasDifusio = "HasDifusio";
    const WebDescripcio = "WebDescripcio";
    const SiteId = "SiteId";
    const Actiu = "Actiu";
    const IsTractada = "IsTractada";        

    public function __construct() {                      

        $OldFields = array("ReservaEspaiID", "Representacio", "Responsable", "TelefonResponsable", "PersonalAutoritzat", "PrevisioAssistents","EsCicle", "Comentaris", "Estat", "Usuaris_usuariID", "Organitzadors", "DataActivitat", "HorariActivitat", "TipusActe", "Nom", "isEnregistrable", "EspaisSolicitats", "MaterialSolicitat", "DataAlta", "Compromis", "Codi", "CondicionsCCG", "DataAcceptacioCondicions", "ObservacionsCondicions", "HasDifusio","WebDescripcio", "site_id", "actiu","tractada");
        
        $NewFields = array(self::ReservaEspaiId, self::Representacio, self::Responsable, self::TelefonResponsable, self::PersonalAutoritzat, self::PrevisioAssistents, self::EsCicle, self::Comentaris, self::Estat, self::UsuariId, self::Organitzadors, self::DataActivitat, self::HorariActivitat, self::TipusActe, self::Nom, self::IsEnregistrable, self::EspaisSolicitats, self::MaterialSolicitat, self::DataAlta, self::Compromis, self::Codi, self::Condicions, self::DataAcceptacioCondicions, self::ObservacionsCondicions, self::HasDifusio, self::WebDescripcio, self::SiteId, self::Actiu, self::IsTractada);
        
        parent::__construct("reservaespais", "RESERVAESPAIS", $OldFields, $NewFields );                        

    }
    
    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

    public function insert($ORE) {           

        $ORE[$this->gnfnwt(self::EspaisSolicitats)] = $this->ReturnWithArrobas($ORE[$this->gnfnwt(self::EspaisSolicitats)]);
        $this->_doInsert($ORE);

    }

    public function ReturnWithArrobas($ElementArray) {
        $RET = array();        
        foreach($ElementArray as $id => $val) $RET[] = $val;
        return implode('@', $RET);
    }


        // Carrego la informació d'un espai i els seus horaris ocupats ( si n'hi ha )
//    public function getEspaiDetall($idEspai) {
//        return $this->_getRowWhere( array( $this->gofnwt('EspaiId') => intval($idEspai)) );
//    }

}

?>