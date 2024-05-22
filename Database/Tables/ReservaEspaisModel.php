<?php 

require_once BASEDIR."Database/DB.php";

class JSON_HorarisReservats_type {
    public $data = '';
    public $hora_inicial = '';
    public $hora_final = '';
    public $espai = '';

    function __construct(array $HorarisReservats = array()){        
        $this->data = $HorarisReservats['data'] ?? '';
        $this->hora_inicial = $HorarisReservats['hora_inicial'] ?? '';
        $this->hora_final = $HorarisReservats['hora_final'] ?? '';
        $this->espai = $HorarisReservats['espai'];        
    }

    public function fromJSON(string $json): void {
        $array = json_decode($json, true);
        if (is_array($array)) {
            $this->data = $array['data'] ?? '';
            $this->hora_inicial = $array['hora_inicial'] ?? '';
            $this->hora_final = $array['hora_final'] ?? '';
            $this->espai = $array['espai'] ?? '';            
        }
    }

    public function toJSON(): string {
        return json_encode($this->getArray());
    }

    public function getJson(): string {
        return $this->toJSON();
    }

    public function getArray(): array {
        return [
            'data' => $this->data,
            'hora_inicial' => $this->hora_inicial,
            'hora_final' => $this->hora_final,
            'espai' => $this->espai            
        ];
    }

}

class JSON_HorarisReservats {
    public array $horarisReservats = array();

    function __construct(string $Array_horaris_reservats_type = ""){
        $ArrayHorarisReservats = json_decode($Array_horaris_reservats_type, true);
        foreach($ArrayHorarisReservats as $A){
            array_push($this->horarisReservats, new JSON_HorarisReservats_type($A));
        }
        if(empty($Array_horaris_reservats_type)) array_push($this->horarisReservats, new JSON_HorarisReservats_type());
    }

    function fromJSON(string $json_horaris_reservats_field){
        $array = json_decode($json_horaris_reservats_field, true);
        if (is_array($array)) {
            foreach ($array as $item) {                
                $this->horarisReservats[] = new JSON_HorarisReservats_type($item);
            }
        }
    }

    function toJSON(){        
        $array_horaris_reservats_field = [];
        foreach ($this->horarisReservats as $horari) {
            if ($horari instanceof JSON_HorarisReservats_type) {
                $array_horaris_reservats_field[] = $horari->getArray();
            }
        }
        return json_encode($array_horaris_reservats_field);
    }

    function getHorarisReservatsArray() {return $this->horarisReservats; }
}

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
    const HorarisReservats = "HorarisReservats";
    
    // Hi ha possibles camps temp per càrrega d'arxius
    const TmpArxiuImatge = "TMP_ArxiuImatge";
    const TmpArxiuPdf = "TMP_ArxiuPdf";

    
    const LlistatEstatsReserva = array(0 => "En espera", 1 => "Acceptada", 2 => "Denegada", 3 => "Anul·lada", 4 => "Pendent d'acceptar condicions", 5 => "Esborrada");

    public function __construct() {                      

        $TextField = array("ID", "Representació", "Responsable", "Telèfon del responsable", "Personal autoritzat", "Previsio d'assistents","És un cicle?", "Comentaris", "Estat", "ID Usuari", "Organitzadors", "Data de l'activitat", "Horari de l'activitat", "Tipus d'acte", "Nom", "És enregistrable?", "Espais", "Material", "Data d'alta", "Compromís", "Codi", "Condicions", "Data d'acceptació de les condicions", "Observacions a les condicions", "Té difusió?","Té descripció web?", "Lloc", "Actiu?","Tractada?", "Horaris demanats");
        $OldFields = array("ReservaEspaiID", "Representacio", "Responsable", "TelefonResponsable", "PersonalAutoritzat", "PrevisioAssistents","EsCicle", "Comentaris", "Estat", "Usuaris_usuariID", "Organitzadors", "DataActivitat", "HorariActivitat", "TipusActe", "Nom", "isEnregistrable", "EspaisSolicitats", "MaterialSolicitat", "DataAlta", "Compromis", "Codi", "CondicionsCCG", "DataAcceptacioCondicions", "ObservacionsCondicions", "HasDifusio","WebDescripcio", "site_id", "actiu","tractada", "HorarisReservats");        
        $NewFields = array(self::ReservaEspaiId, self::Representacio, self::Responsable, self::TelefonResponsable, self::PersonalAutoritzat, self::PrevisioAssistents, self::EsCicle, self::Comentaris, self::Estat, self::UsuariId, self::Organitzadors, self::DataActivitat, self::HorariActivitat, self::TipusActe, self::Nom, self::IsEnregistrable, self::EspaisSolicitats, self::MaterialSolicitat, self::DataAlta, self::Compromis, self::Codi, self::Condicions, self::DataAcceptacioCondicions, self::ObservacionsCondicions, self::HasDifusio, self::WebDescripcio, self::SiteId, self::Actiu, self::IsTractada, self::HorarisReservats);
        parent::__construct("reservaespais", "RESERVAESPAIS", $OldFields, $NewFields, $TextField );

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
        $HRO = new JSON_HorarisReservats();
        $O[$this->gnfnwt(self::HorarisReservats)] = $HRO->toJSON();


        $O[self::TmpArxiuImatge] = HelperForm_DefaultValueForFileUploadForm();
        $O[self::TmpArxiuPdf] = HelperForm_DefaultValueForFileUploadForm();

        return $O;
    }

    public function adaptFromFormFields($FieldsFromForm, $isNew = false) {
                
        $FieldsFromForm[ $this->gnfnwt( self::UsuariId ) ] = HelperForm_Decrypt( $this->getUsuariId($FieldsFromForm) );
        $FieldsFromForm[ $this->gnfnwt( self::EspaisSolicitats ) ] = implode('@', $FieldsFromForm[ $this->gnfnwt( self::EspaisSolicitats ) ]);
        $FieldsFromForm[ $this->gnfnwt( self::MaterialSolicitat ) ] = implode('@', $FieldsFromForm[ $this->gnfnwt( self::MaterialSolicitat ) ]);
        $FieldsFromForm[ $this->gnfnwt( self::HorarisReservats ) ] = json_encode($FieldsFromForm[ $this->gnfnwt( self::HorarisReservats ) ]);
                        
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
        $this->_doUpdate($ORE, array(self::ReservaEspaiId));

        return $ORE;

    }
    
    public function getSiteId($ORE){
        return $ORE[$this->gnfnwt(self::SiteId)];
    }

    public function getId($ORE) {
        return $ORE[$this->gnfnwt(self::ReservaEspaiId)];
    }

    public function getNom($ORE) { return $ORE[$this->gnfnwt(self::Nom)]; }

//    public function getEspais($ORE) { return $ORE[$this->gnfnwt(self::EspaisSolicitats)]; }
//    public function setEspais($ORE, $Espais) { $ORE[$this->gnfnwt(self::EspaisSolicitats)] = $Espais; return $ORE; }
    
    public function getHorarisReservats($ORE): JSON_HorarisReservats {
        $HRO = new JSON_HorarisReservats($ORE[$this->gnfnwt(self::HorarisReservats)]);
        return $HRO;
    }

    public function getOrganitzadors($ORE) { return $ORE[$this->gnfnwt(self::Organitzadors)]; }

    public function getUsuariId($ORE){ return $ORE[$this->gnfnwt(self::UsuariId)]; } 
    
    public function setReservaEspaiId($ORE, $id) { $ORE[$this->gnfnwt(self::ReservaEspaiId)] = $id; return $ORE;  }
    public function getReservaEspaiId($ORE) { return $ORE[$this->gnfnwt(self::ReservaEspaiId)]; }
        
    public function setCodi($ORE, $id) { $ORE[$this->gnfnwt(self::Codi)] = $id; return $ORE; }
    public function getCodi($ORE) { return $ORE[$this->gnfnwt(self::Codi)]; }

    public function getReservaById($idReserva) {
        return $this->_getRowWhere( array( $this->gofnwt('ReservaEspaiId') => intval($idReserva) ) );
    }

    public function setReservaEspaiEstat($ORE, $Estat) { $ORE[$this->gnfnwt(self::Estat)] = $Estat; return $ORE; }
    public function getReservaEspaiEstat($ORE) { return $ORE[$this->gnfnwt(self::Estat)]; }

    public function getUrlCondicions($url) {
        require_once AUXDIR . "Encrypt/encrypt.php";
        $Data = Encrypt::Desencripta( $url );
        $ArrayData = unserialize($Data);    // ArrayData(formulari, id) Reserva_Espais_Mail_Accepta_Condicions o Reserva_Espais_Mail_Rebutja_Condicions
        if($ArrayData !== false):
            $REM = new ReservaEspaisModel();
            $REO = $REM->getReservaById($ArrayData['id']);
            if(sizeof($REO) > 0) {
                $nouEstat = 'error';
                if($ArrayData['formulari'] == 'Reserva_Espais_Mail_Accepta_Condicions'){ 
                    $REO = $REM->setReservaEspaiEstat($REO, 1);
                    $nouEstat = "acceptada";
                } elseif($ArrayData['formulari'] == 'Reserva_Espais_Mail_Rebutja_Condicions') { 
                    $REO = $REM->setReservaEspaiEstat($REO, 3);
                    $nouEstat = "rebutjada";
                }

                $this->_doUpdate($REO, array(self::ReservaEspaiId));
                
                // Enviem un correu
                require_once AUXDIR . "ElasticEmail/ElasticEmail.php";
                $EM = new ElasticEmail();                                
                $EM->SendEmailToAdmin($REM->getSiteId($REO), "Reserva amb codi ".$REM->getCodi($REO)." ha estat ".$nouEstat, "");
                                
                return $REO;
            } else return false;            
        endif;

        return false;
    }
    
    /**
     * Convertim els camps a text, per poder enviar per correu o altres i hi he afegit els camps visibles
     */
    public function convertAllFieldsToText($ORE) {
                
        $Text = array();
        $FinalFields = array();        

        //Carrego els espais del site
        $EM = new EspaisModel();
        $LlistatEspaisObjecte = $EM->getAllSiteEspais( $this->getSiteId($ORE) );
        $JSON_HorarisReservatsObject = $this->getHorarisReservats($ORE);
        
        foreach($JSON_HorarisReservatsObject->getHorarisReservatsArray() as $JSON_HorarisReservats_type):
            $EspaiNom = array_filter($LlistatEspaisObjecte, function ($EO) use ($JSON_HorarisReservats_type, $EM) {
                return $EM->getEspaiId($EO) === intval($JSON_HorarisReservats_type->espai);
            });
            $Text[] = $EM->getNom($EspaiNom[0]) . ' el dia ' . $JSON_HorarisReservats_type->data . ' de ' . $JSON_HorarisReservats_type->hora_inicial . ' a ' . $JSON_HorarisReservats_type->hora_final;
        endforeach;
        $ORE[$this->gnfnwt(self::HorarisReservats)] = $Text;            // Canviem el text del JSON per un text html per imprimir. 

        $OM = new OptionsModel();
        $VisibleFields = json_decode($OM->getOption('FORMULARI_CAMPS_VISIBLES', $this->getSiteId($ORE) ), true);        
        foreach($this->NewFieldsWithTableArray as $F){
            if( isset($VisibleFields[$F]) && $VisibleFields[$F] == 1 && isset($ORE[$F] ) ) $FinalFields[$F] = $ORE[$F];
        }        
                
        return $FinalFields;
    }
    public function sendEmailNewRegistre($ORE) {
                
        // Enviem un correu a la secretaria amb la nova reserva
        $OM = new OptionsModel();
        $idS = $this->getSiteId($ORE);
        $Email = $OM->getOption('MAIL_SECRETARIA', $idS);
        $Titol = $this->getNom($ORE);
        $Codi = $this->getCodi($ORE);        
        $Organitzadors = $this->getOrganitzadors($ORE);
        $HTML = "S'ha registrat una nova reserva d'espai amb el codi <b>{$Codi}</b> organitzada per <b>{$Organitzadors}</b> amb el títol <b>{$Titol}</b>";
        HelperForm_SendEmail($Email, $idS, "Nova reserva d'espai", $HTML);

        //Generem un correu amb una taula HTML per enviar a l'usuari final i amb els valors.        
        $FinalFields = $this->convertAllFieldsToText($ORE);
                                
        $html = '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';        
        foreach ($FinalFields as $key => $value) {
            $FieldLabel = $this->getFromNewFieldTableNameToTextFieldTableName($key);
            if($key == $this->gnfnwt(self::HorarisReservats)) $html .= '<tr><th>' . htmlspecialchars($FieldLabel) . '</th><td>' . implode("<br />", $value) . '</td>';
            else $html .= '<tr><th>' . htmlspecialchars($FieldLabel) . '</th><td>' . htmlspecialchars($value) . '</td>';
        }                        
        $html .= '</table>';
        
        $message = '<html><body>';
        $message .= '<h1>Detalls de la reserva</h1>';
        $message .= $html;
        $message .= '</body></html>';        

        $UM = new UsuarisModel();
        $OU = $UM->getUsuariId( $this->getUsuariId($ORE) );
        $Email = $UM->getEmail($OU);
        HelperForm_SendEmail($Email, $idS, "Nova reserva d'espai", $message);
        
    }

}

?>