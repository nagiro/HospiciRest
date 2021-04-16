<?php 

require_once BASEDIR."Database/DB.php";

class ReservaEspaisModel extends BDD {

    // Variables de la taula    
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
    
    // Hi ha possibles camps temp per càrrega d'arxius
    const TmpArxiuImatge = "TMP_ArxiuImatge";
    const TmpArxiuPdf = "TMP_ArxiuPdf";

    
    const LlistatEstatsReserva = array(0 => "En espera", 1 => "Acceptada", 2 => "Denegada", 3 => "Anul·lada", 4 => "Pendent d'acceptar condicions", 5 => "Esborrada");

    public function __construct() {                      

        $OldFields = array("ReservaEspaiID", "Representacio", "Responsable", "TelefonResponsable", "PersonalAutoritzat", "PrevisioAssistents","EsCicle", "Comentaris", "Estat", "Usuaris_usuariID", "Organitzadors", "DataActivitat", "HorariActivitat", "TipusActe", "Nom", "isEnregistrable", "EspaisSolicitats", "MaterialSolicitat", "DataAlta", "Compromis", "Codi", "CondicionsCCG", "DataAcceptacioCondicions", "ObservacionsCondicions", "HasDifusio","WebDescripcio", "site_id", "actiu","tractada");        
        $NewFields = array(self::ReservaEspaiId, self::Representacio, self::Responsable, self::TelefonResponsable, self::PersonalAutoritzat, self::PrevisioAssistents, self::EsCicle, self::Comentaris, self::Estat, self::UsuariId, self::Organitzadors, self::DataActivitat, self::HorariActivitat, self::TipusActe, self::Nom, self::IsEnregistrable, self::EspaisSolicitats, self::MaterialSolicitat, self::DataAlta, self::Compromis, self::Codi, self::Condicions, self::DataAcceptacioCondicions, self::ObservacionsCondicions, self::HasDifusio, self::WebDescripcio, self::SiteId, self::Actiu, self::IsTractada);        
        parent::__construct("reservaespais", "RESERVAESPAIS", $OldFields, $NewFields );                        

    }
    
    // Retorna el camp estats per a un formulari json
    public function getEstatsForForm() {
        $RET = array();
        foreach(self::LlistatEstatsReserva as $K => $L) {
            $RET[] = array( 'id' => $K, 'text' => $L );
        } 
        return json_encode($RET);
    }

    public function getEmptyObject($EspaiId) {

        // Busco a quin site pertany l'Espai
        $EM = new EspaisModel();
        $OEM = $EM->getEspaiDetall($EspaiId);

        $O = $this->getDefaultObject();           
        $O[$this->gnfnwt(self::DataAlta)] = date('Y-m-d H:i:s', time());
        $O[$this->gnfnwt(self::SiteId)] = $EM->getSiteId($OEM);
        $O[$this->gnfnwt(self::Actiu)] = "1";
        $O[$this->gnfnwt(self::IsTractada)] = "0";
        $O[$this->gnfnwt(self::UsuariId)] = "0";
        $O[$this->gnfnwt(self::Estat)] = "0";
        $O[$this->gnfnwt(self::EspaisSolicitats)] = array();
        $O[$this->gnfnwt(self::MaterialSolicitat)] = array();        
        $O[$this->gnfnwt(self::EsCicle)] = "";
        $O[$this->gnfnwt(self::IsEnregistrable)] = "";
        $O[$this->gnfnwt(self::HasDifusio)] = "";
        $O[$this->gnfnwt(self::PrevisioAssistents)] = "";        
        $O[$this->gnfnwt(self::Compromis)] = "";        
        $O[$this->gnfnwt(self::Codi)] = "";        

        $O[self::TmpArxiuImatge] = HelperForm_DefaultValueForFileUploadForm();
        $O[self::TmpArxiuPdf] = HelperForm_DefaultValueForFileUploadForm();

        return $O;
    }

    public function adaptFromFormFields($FieldsFromForm, $isNew = false) {
                
        $FieldsFromForm[ $this->gnfnwt( self::UsuariId ) ] = HelperForm_Decrypt( $this->getUsuariId($FieldsFromForm) );
        $FieldsFromForm[ $this->gnfnwt( self::EspaisSolicitats ) ] = implode('@', $FieldsFromForm[ $this->gnfnwt( self::EspaisSolicitats ) ]);
        $FieldsFromForm[ $this->gnfnwt( self::MaterialSolicitat ) ] = implode('@', $FieldsFromForm[ $this->gnfnwt( self::MaterialSolicitat ) ]);
                
        $imageName = $this->getId($FieldsFromForm);
        
        // Si l'arxiu és nou, esborrem arxius antics
        if($isNew) HelperForm_FileCleanFromPostParameterBase64(DOCUMENTS_RESERVAESPAIS_DIR, $imageName);        
        
        // Guardem els arxius annexats al formulari
        $FieldsFromForm[ self::TmpArxiuImatge ] =   HelperForm_FileConvertAndSaveFromPostParameterBase64(DOCUMENTS_RESERVAESPAIS_DIR, DOCUMENTS_RESERVAESPAIS_URL, $FieldsFromForm[ self::TmpArxiuImatge ], $imageName);                        
        $FieldsFromForm[ self::TmpArxiuPdf ] =      HelperForm_FileConvertAndSaveFromPostParameterBase64(DOCUMENTS_RESERVAESPAIS_DIR, DOCUMENTS_RESERVAESPAIS_URL, $FieldsFromForm[ self::TmpArxiuPdf ], $imageName);
                
        return $FieldsFromForm;
        
    }

    // Abans d'inserir cal aplicar la funció adaptFromFormFields si venim d'un formulari
    public function insert($ORE) {           

        //Copio només els camps que vull guardar i que corresponen a la taula
        $ORE_To_Insert = array();
        foreach($this->NewFieldsWithTableArray as $Field) {
            $ORE_To_Insert[$Field] = $ORE[$Field];        
        }
        
        $id = $this->_doInsert($ORE);

        if( is_numeric( $id ) && $id > 0 ) $ORE[ $this->gnfnwt( self::ReservaEspaiId ) ] = $id;
        else throw new Exception('Hi ha hagut algun problema guardant la reserva.');

        $ORE = $this->setCodi($ORE, $id . date('mY'));

        return $ORE;

    }
    
    public function getId($ORE) {
        return $ORE[$this->gnfnwt(self::ReservaEspaiId)];
    }

    public function getUsuariId($ORE){ return $ORE[$this->gnfnwt(self::UsuariId)]; } 
    
    public function setReservaEspaiId($ORE, $id) { $ORE[$this->gnfnwt(self::ReservaEspaiId)] = $id; return $ORE;  }
    public function getReservaEspaiId($ORE) { return $ORE[$this->gnfnwt(self::ReservaEspaiId)]; }

    public function setCodi($ORE, $id) { $ORE[$this->gnfnwt(self::Codi)] = $id; return $ORE; }
    public function getCodi($ORE) { return $ORE[$this->gnfnwt(self::Codi)]; }

        // Carrego la informació d'un espai i els seus horaris ocupats ( si n'hi ha )
//    public function getEspaiDetall($idEspai) {
//        return $this->_getRowWhere( array( $this->gofnwt('EspaiId') => intval($idEspai)) );
//    }

}

?>