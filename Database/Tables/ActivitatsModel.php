<?php

require_once BASEDIR."Database/Tables/HorarisModel.php";
require_once BASEDIR."Database/Tables/HorarisEspaisModel.php";
require_once BASEDIR."Database/Tables/EspaisModel.php";
require_once BASEDIR."Database/Tables/CiclesModel.php";
require_once BASEDIR."Database/Tables/TipusActivitatsModel.php";
require_once BASEDIR."Database/Tables/TipusModel.php";
require_once BASEDIR."Database/FormulariClass.php";

require_once BASEDIR."Database/DB.php";


class ActivitatsModel extends BDD {

    const FIELD_Actiu = "Actiu";
    const FIELD_ActivitatId = "ActivitatId";
    const FIELD_Categories = "Categories";
    const FIELD_CiclesCicleId = "CiclesCicleId";
    const FIELD_DescripcioCompleta = "DescripcioCompleta";
    const FIELD_DescripcioCurta = "DescripcioCurta";
    const FIELD_DefinicioHoraris = "DefinicioHoraris";
    const FIELD_Descripcio = "Descripcio";
    const FIELD_DescripcioMig = "DescripcioMig";
    const FIELD_Estat = "Estat";
    const FIELD_Imatge = "Imatge";
    const FIELD_InformacioPractica = "InformacioPractica";
    const FIELD_IsEntrada = "IsEntrada";
    const FIELD_IsImportant = "IsImportant";
    const FIELD_Nom = "Nom";
    const FIELD_Organitzador = "Organitzador";
    const FIELD_Pdf = "Pdf";
    const FIELD_Places = "Places";
    const FIELD_Preu = "Preu";
    const FIELD_PreuReduit = "PreuReduit";
    const FIELD_Publicable = "Publicable";
    const FIELD_PublicableWeb = "PublicableWeb";
    const FIELD_Responsable = "Responsable";
    const FIELD_SiteId = "SiteId";
    const FIELD_TitolComplet = "TitolComplet";
    const FIELD_TitolCurt = "TitolCurt";
    const FIELD_TipusActivitatId = "TipusActivitatId";
    const FIELD_TipusEnviament = "TipusEnviament";
    const FIELD_TitolMig = "TitolMig";
    const FIELD_ImatgeS = "ImatgeS";
    const FIELD_ImatgeM = "ImatgeM";
    const FIELD_ImatgeL = "ImatgeL";
    const FIELD_WufooFormStatus = "WufooFormStatus";
    

    public function __construct() {

        $OldFields = array("actiu", "ActivitatID", "Categories", "Cicles_CicleID", "dComplet",           "dCurt",           "Definiciohoraris", "Descripcio", "dMig",           "Estat", "Imatge", "InfoPractica",       "isEntrada", "isImportant", "Nom", "Organitzador", "PDF", "Places", "Preu", "PreuReduit", "Publicable", "PublicaWEB",    "Responsable", "site_id", "tComplet",     "tCurt",     "TipusActivitat_idTipusActivitat", "tipusEnviament", "tMig", "ImatgeS", "ImatgeM", "ImatgeL", "WufooFormStatus");
        $NewFields = array( self::FIELD_Actiu, self::FIELD_ActivitatId, self::FIELD_Categories, self::FIELD_CiclesCicleId, self::FIELD_DescripcioCompleta, self::FIELD_DescripcioCurta, self::FIELD_DefinicioHoraris, self::FIELD_Descripcio, self::FIELD_DescripcioMig, self::FIELD_Estat, self::FIELD_Imatge, self::FIELD_InformacioPractica, self::FIELD_IsEntrada, self::FIELD_IsImportant, self::FIELD_Nom, self::FIELD_Organitzador, self::FIELD_Pdf,         self::FIELD_Places, self::FIELD_Preu,         self::FIELD_PreuReduit, self::FIELD_Publicable,         self::FIELD_PublicableWeb, self::FIELD_Responsable,         self::FIELD_SiteId, self::FIELD_TitolComplet,         self::FIELD_TitolCurt, self::FIELD_TipusActivitatId,         self::FIELD_TipusEnviament, self::FIELD_TitolMig, self::FIELD_ImatgeS, self::FIELD_ImatgeM, self::FIELD_ImatgeL, self::FIELD_WufooFormStatus );
        parent::__construct("activitats", "ACTIVITATS", $OldFields, $NewFields );

    }

    public function getEmptyObject($idS) {
        $O = $this->getDefaultObject();      
        $O[$this->gnfnwt('SiteId')] = $idS;
        $RET = array('ACTIVITAT' => $O, 'HORARIS' => array());
    }

    public function getActivitatByIdObject($idActivitat) {
        return $this->_getRowWhere( array( $this->gofnwt('ActivitatId') => intval($idActivitat), $this->gofnwt('Actiu') => '1' ) ); 
    }

    public function getActivitatById($idA) {

        $HM = new HorarisModel();
        $HEM = new HorarisEspaisModel();
        $EM = new EspaisModel();
        $W = ''; $WA = array();        

        $SQL = "
                Select {$this->getSelectFieldsNames()}, {$HM->getSelectFieldsNames()}, {$EM->getSelectFieldsNames()}
                from {$this->getTableName()} 
                LEFT JOIN {$HM->getTableName()} ON ( {$this->getOldFieldNameWithTable('ActivitatId')} = {$HM->getOldFieldNameWithTable('ActivitatId')} )
                LEFT JOIN {$HEM->getTableName()} ON ( {$HM->getOldFieldNameWithTable('HorariId')} = {$HEM->getOldFieldNameWithTable('HorariId')} )
                LEFT JOIN {$EM->getTableName()} ON ( {$EM->getOldFieldNameWithTable('EspaiId')} = {$HEM->getOldFieldNameWithTable('EspaiId')} )
                where    {$this->getOldFieldNameWithTable('ActivitatId')} = :idActivitat                         
                AND      {$this->getOldFieldNameWithTable('Actiu')} = 1                                
                AND      {$HM->getOldFieldNameWithTable('Actiu')} = 1                                         
                ORDER BY {$HM->getOldFieldNameWithTable('Dia')} asc
            ";
        
        $Rows = $this->runQuery($SQL, array('idActivitat' => $idA ) );
        
        $RET = array();
        
        foreach($Rows as $Row) {
            foreach($Row as $FieldName => $Field ) {
                
                $idH = $Row[ $HM->gnfnwt( 'HorariId' ) ] ;                    
                $idE = $Row[ $EM->gnfnwt( 'EspaiId' ) ] ;

                // Si el camp és de la taula Activitats
                if( stripos($FieldName, $this->getNewTableName()) !== false ):
                    $RET['ACTIVITAT'][ $FieldName ] = $Field;
                
                // Si el camp és de la taula Horaris
                elseif( stripos( $FieldName , $HM->getNewTableName() ) !== false ):                    
                    if( !isset( $RET['HORARIS'][$idH] ) ) $RET['HORARIS'][$idH] = array( 'HORARI' => array(), 'ESPAIS' => array() );
                    $RET['HORARIS'][$idH]['HORARI'][$FieldName] = $Field; 
                
                // Si el camp és de la taula Espais
                elseif( stripos($FieldName, $EM->getNewTableName() ) !== false ):
                    if( !isset( $RET['HORARIS'][$idH]['ESPAIS'][$idE] ) ) $RET['HORARIS'][$idH]['ESPAIS'][$idE] = array();
                    $RET['HORARIS'][$idH]['ESPAIS'][$idE][$FieldName] = $Field;                    
                endif;

            }
        }
    
        return $RET;

    }

    public function getLlistatActivitatsCalendari( $idS, $paraules, $DataInicial, $DataFinal ) {    
        
        $HM = new HorarisModel();
        $HEM = new HorarisEspaisModel();
        $EM = new EspaisModel();

        $W = ''; $WA = array();        
        if(strlen($paraules) > 0) { $W = " AND ({$this->getOldFieldNameWithTable('Nom')} like :paraula1 
                                            OR {$this->getOldFieldNameWithTable('Organitzador')} like :paraula2
                                            OR {$this->getOldFieldNameWithTable('tMig')} like :paraula3
                                        ) "; 
                                    $WA['paraula1'] = '%'.$paraula.'%';
                                    $WA['paraula2'] = '%'.$paraula.'%';
                                    $WA['paraula3'] = '%'.$paraula.'%';
                                }

        $SQL = "
                Select {$this->gsfn('ActivitatId')}, {$HM->gsfn('Dia')}, {$HM->gsfn('HoraInici')}, {$HM->gsfn('HoraFi')},
                       {$this->gsfn('Nom')}, {$this->gsfn('Organitzador')}, {$EM->gsfn('Nom')}
                from {$this->getTableName()} 
                LEFT JOIN {$HM->getTableName()} ON ( {$this->getOldFieldNameWithTable('ActivitatId')} = {$HM->getOldFieldNameWithTable('ActivitatId')} )
                LEFT JOIN {$HEM->getTableName()} ON ( {$HM->getOldFieldNameWithTable('HorariId')} = {$HEM->getOldFieldNameWithTable('HorariId')} )
                LEFT JOIN {$EM->getTableName()} ON ( {$EM->getOldFieldNameWithTable('EspaiId')} = {$HEM->getOldFieldNameWithTable('EspaiId')} )
                where 
                         {$this->getOldFieldNameWithTable('SiteId')} = :site_id
                AND      {$this->getOldFieldNameWithTable('Actiu')} = 1                
                AND      {$HM->getOldFieldNameWithTable('Dia')} > :DataInicial
                AND      {$HM->getOldFieldNameWithTable('Dia')} < :DataFinal
                AND      {$HM->getOldFieldNameWithTable('Actiu')} = 1                                         
                         {$W}
                ORDER BY {$HM->getOldFieldNameWithTable('Dia')} asc
            ";

        $SQLW = array('site_id'=>$idS, 'DataInicial' => $DataInicial, 'DataFinal' => $DataFinal );        
                
        return $this->runQuery($SQL, array_merge( $SQLW , $WA ) );
        
    }

    public function getActivitatsFranja( $idS, $DataInicial, $DataFinal, $PublicableWeb = true ) {    
        
        $HM = new HorarisModel(); $HEM = new HorarisEspaisModel(); 
        $EM = new EspaisModel(); $TAM = new TipusActivitatsModel();
        $CM = new CiclesModel();
        $WA = array();
        $W = "";
        $W .= ($PublicableWeb) ? " AND {$this->getOldFieldNameWithTable(self::FIELD_PublicableWeb)} = 1" : " AND {$this->getOldFieldNameWithTable(self::FIELD_PublicableWeb)} = 0";        


        $SQL = "
                Select {$HM->getSelectFieldsNames()},{$EM->getSelectFieldsNames()},{$this->getSelectFieldsNames()}, 
                        {$TAM->gsfn(TipusActivitatsModel::FIELD_Nom)}, {$CM->gsfn(CiclesModel::FIELD_Tmig)},
                        {$CM->gsfn(EspaisModel::FIELD_Nom)} 
                from {$this->getTableName()} 
                LEFT JOIN {$HM->getTableName()} ON ( {$this->getOldFieldNameWithTable('ActivitatId')} = {$HM->getOldFieldNameWithTable('ActivitatId')} )
                LEFT JOIN {$HEM->getTableName()} ON ( {$HM->getOldFieldNameWithTable('HorariId')} = {$HEM->getOldFieldNameWithTable('HorariId')} )
                LEFT JOIN {$EM->getTableName()} ON ( {$EM->getOldFieldNameWithTable('EspaiId')} = {$HEM->getOldFieldNameWithTable('EspaiId')} )
                LEFT JOIN {$TAM->getTableName()} ON ( {$TAM->getOldFieldNameWithTable(TipusActivitatsModel::FIELD_IdTipusActivitat)} = {$this->getOldFieldNameWithTable(self::FIELD_TipusActivitatId)} )
                LEFT JOIN {$CM->getTableName()} ON ( {$CM->getOldFieldNameWithTable(CiclesModel::FIELD_IdCicle)} = {$this->getOldFieldNameWithTable(self::FIELD_CiclesCicleId)} )
                where 
                         {$this->getOldFieldNameWithTable('SiteId')} = :site_id
                AND      {$this->getOldFieldNameWithTable('Actiu')} = 1                
                AND      {$HM->getOldFieldNameWithTable('Dia')} > :DataInicial
                AND      {$HM->getOldFieldNameWithTable('Dia')} < :DataFinal
                AND      {$HM->getOldFieldNameWithTable('Actiu')} = 1                      
                {$W}                                           
                ORDER BY {$HM->getOldFieldNameWithTable('Dia')} asc
            ";

        $SQLW = array('site_id'=>$idS, 'DataInicial' => $DataInicial, 'DataFinal' => $DataFinal );        
                
        return $this->runQuery($SQL, array_merge( $SQLW , $WA ) );
        
    }    

    /**
     * Funció complement de getActivitatsFranja
     */
    private function getHorarisFromActivitat($LOA, $idA) {
        $RET = array('DiaMax' => '0000-00-00', 'DiaMin' => '9999-99-99', 'Espais' => array());
        $OM = new HorarisModel(); $EM = new EspaisModel();

        foreach($LOA as $OA) {
            if($OA[ $OM->gnfnwt( HorarisModel::FIELD_ActivitatId ) ] == $idA){
                if($OA[ $OM->gnfnwt( HorarisModel::FIELD_Dia )] > $RET['DiaMax']) $RET['DiaMax'] = $OA[ $OM->gnfnwt( HorarisModel::FIELD_Dia )];
                if($OA[ $OM->gnfnwt( HorarisModel::FIELD_Dia )] < $RET['DiaMin']) $RET['DiaMin'] = $OA[ $OM->gnfnwt( HorarisModel::FIELD_Dia )];                
                $RET['Espais'][$OA[ $EM->gnfnwt( EspaisModel::FIELD_Nom ) ]] = $OA[ $EM->gnfnwt( EspaisModel::FIELD_Nom ) ];
            }
        }
        return $RET;
    }

    public function genXML( $DataInicial, $DataFinal, $SiteId ){
        
        $document = "<document>\n";

        $LOA = $this->getActivitatsFranja( $SiteId, $DataInicial, $DataFinal );
        $HM = new HorarisModel(); $TAM = new TipusActivitatsModel(); $CM = new CiclesModel();

        $PerTractarXML = array();
        $ActivitatsTractades = array();
        $idAntActivitat = 0;
        // Per totes les activitats
        foreach($LOA as $OA) {
            
            $idA = $OA[$this->gnfnwt(self::FIELD_ActivitatId)];            

            if( !array_key_exists( $idA, $ActivitatsTractades ) ) {
                
                $ActivitatsTractades[ $idA ] = $idA;

                $document .= "<caixa>";
                
                $T = $this->getHorarisFromActivitat($LOA, $idA);
                    
                $PerTractarXML["data_inicial"] = $T["DiaMin"];
                $PerTractarXML["data_fi"] = $T["DiaMax"];
                $PerTractarXML["tipus_activitat"] = $OA[$TAM->gnfnwt(TipusActivitatsModel::FIELD_Nom)];
                $PerTractarXML["cicle"] = $OA[$CM->gnfnwt(CiclesModel::FIELD_Tmig)];
                $PerTractarXML["tipologia"] = $OA[$this->gnfnwt(self::FIELD_Categories)];
                $PerTractarXML["importancia"] = $this->isImportant($OA);
                $PerTractarXML["titol"] = $OA[$this->gnfnwt(self::FIELD_TitolMig)];
                $PerTractarXML["text"] = $OA[$this->gnfnwt(self::FIELD_DescripcioMig)];
                $PerTractarXML["url"] = "https://www.casadecultura.cat/detall/".$idA;
                $PerTractarXML["hora_inici"] = $OA[$HM->gnfnwt(HorarisModel::FIELD_HoraInici)];
                $PerTractarXML["hora_fi"] = $OA[$HM->gnfnwt(HorarisModel::FIELD_HoraFi)];
                $PerTractarXML["espais"] = implode(',', $T["Espais"]);
                $PerTractarXML["organitzador"] = $OA[$this->gnfnwt(self::FIELD_Organitzador)];
                $PerTractarXML["info_practica"] = $OA[$this->gnfnwt(self::FIELD_InformacioPractica)];
                $PerTractarXML["url_img_s"] = "https://www.casadecultura.cat/images/activitats/A-".$idA."-XL.jpg";
                $PerTractarXML["url_img_m"] = "https://www.casadecultura.cat/images/activitats/A-".$idA."-L.jpg";
                $PerTractarXML["url_img_l"] = "https://www.casadecultura.cat/images/activitats/A-".$idA."-M.jpg";

                foreach($PerTractarXML as $Tag => $V) $document .= "  <{$Tag}>{$V}</{$Tag}>\n";
                $document .= "</caixa>\n";                

            }
            
        }
        
        $document .= "</document>\n";                                            

        return $document;

    }

    private function isImportant($OA) {
        $cat = $OA[$this->gnfnwt(self::FIELD_Categories)];

        if( substr_count($cat, '54') || substr_count($cat, '50') ) return 3;
        if( substr_count($cat, '53') || substr_count($cat, '47') ) return 2;
        if( substr_count($cat, '52') || substr_count($cat, '49') ) return 1;        
        
    }

    /**
     * idActivitat = 0 per defecte, si no hi ha activitat
     * $idSite = Lloc on pertany l'activitat
     * */
    function Formulari($idActivitat, $idSite) {
        
        $CM = new CiclesModel();
        $TM = new TipusActivitatsModel();
        $TiM = new TipusModel();
        
        $ObjecteActivitatsHoraris = $this->getEmptyObject($idSite);        
        if($idActivitat > 0) {
            $ObjecteActivitatsHoraris = $this->getActivitatById($idActivitat);           
            $OA = $ObjecteActivitatsHoraris['ACTIVITAT'];

            $OC = $CM->getCicleById( $OA[$this->gnfnwt('CiclesCicleId')] );
            $OptionCicleActual = new OptionClass($OA[$this->gnfnwt('CiclesCicleId')], $OC[$CM->gnfnwt('Nom')]);

            $OT = $TM->getTipusById( $OA[$this->gnfnwt('TipusActivitatId')] );
            $OptionTipusActivitatActual = new OptionClass($OT[$TM->gnfnwt('IdTipusActivitat')], $OT[$TM->gnfnwt('Nom')]);            

        }                                

        // Els camps Preu, PreuReduit, Publicable, tipusEnviament, places, isImportant, DefinicioHoraris Estat no s'utilitzen        

        $FormGeneral = new FormulariClass();
        $FormGeneral->setModelObject($OA);                
                
        $FormGeneral->addItem( new FormItemClass("Nom", FormItemClass::CONST_INPUT_HELPER, "", $this->gnfnwt('Nom'), $OA, array()) );                
        
        $I = new FormItemClass("Cicle vinculat", FormItemClass::CONST_SELECT_HELPER, "1", $this->gnfnwt('CiclesCicleId'), $OA, array());
        $I->setOptions( $CM->getCiclesActiusSelect($idSite), $OptionCicleActual );
        $FormGeneral->addItem( $I );
                
        $I = new FormItemClass("Tipus activitat", FormItemClass::CONST_SELECT_HELPER, "0", $this->gnfnwt('TipusActivitatId'), $OA, array());
        $I->setOptions( $TM->getTipusActivitatsSelect($idSite), $OptionTipusActivitatActual );
        $FormGeneral->addItem( $I );
        
        $I = new FormItemClass("Visible web?", FormItemClass::CONST_SELECT_HELPER, "0", $this->gnfnwt('PublicableWeb'), $OA, array());
        $I->setOptionsSiNo();
        $FormGeneral->addItem( $I );
                
        $I = new FormItemClass("Tags relatius", FormItemClass::CONST_MULTIPLE_SELECT_HELPER, "", $this->gnfnwt('Categories'), $OA, array());
        $I->setOptions( $TiM->getTipusSelect('class_activitat', $idSite), array() ); 
        $FormGeneral->addItem($I);        
        
        $I = new FormItemClass("Té inscripció?", FormItemClass::CONST_SELECT_HELPER, "0", $this->gnfnwt('IsEntrada'), $OA, array());
        $I->setOptionsSiNo();
        $FormGeneral->addItem( $I );

        $RET["FormGeneral"] = $FormGeneral->toArray();

        
        /************************************** FORMULARI 2 *********************************************************/

        $FormDescripcio = new FormulariClass();
        $FormDescripcio->setModelObject($OA);                
        
        $FormDescripcio->addItem( new FormItemClass("Titol curt", FormItemClass::CONST_INPUT_HELPER , "", $this->gnfnwt('TitolCurt'), $OA, array()) );                
        $FormDescripcio->addItem( new FormItemClass("Descripció curta", FormItemClass::CONST_TEXTAREA_HELPER , "", $this->gnfnwt('DescripcioCurta'), $OA, array()) );                

        $FormDescripcio->addItem( new FormItemClass("Titol web", FormItemClass::CONST_INPUT_HELPER , "", $this->gnfnwt('TitolMig'), $OA, array()) );                
        $FormDescripcio->addItem( new FormItemClass("Descripció web", FormItemClass::CONST_TEXTAREA_HELPER , "", $this->gnfnwt('DescripcioMig'), $OA, array()) );                

        $FormDescripcio->addItem( new FormItemClass("Titol complet", FormItemClass::CONST_INPUT_HELPER , "", $this->gnfnwt('TitolComplet'), $OA, array()) );                
        $FormDescripcio->addItem( new FormItemClass("Descripció completa", FormItemClass::CONST_TEXTAREA_HELPER , "", $this->gnfnwt('DescripcioCompleta'), $OA, array()) );                
                
        $I = new FormItemClass("Imatge S", FormItemClass::CONST_IMATGE_HELPER, "", $this->gnfnwt('ImatgeS'), $OA, array());
        $I->setImage(    $OA[ $this->gnfnwt('ActivitatId') ], $this->getUrlByMida($OA, 'S'), 's', 'Activitat', 'Activitat_Delete' );
        $FormDescripcio->addItem( $I );

        $I = new FormItemClass("Imatge M", FormItemClass::CONST_IMATGE_HELPER, "", $this->gnfnwt('ImatgeM'), $OA, array());
        $I->setImage(    $OA[ $this->gnfnwt('ActivitatId') ], $this->getUrlByMida($OA, 'M'), 'm', 'Activitat', 'Activitat_Delete' );
        $FormDescripcio->addItem( $I );

        $I = new FormItemClass("Imatge L", FormItemClass::CONST_IMATGE_HELPER, "", $this->gnfnwt('ImatgeL'), $OA, array());
        $I->setImage(    $OA[ $this->gnfnwt('ActivitatId') ], $this->getUrlByMida($OA, 'L'), 'l', 'Activitat', 'Activitat_Delete' );
        $FormDescripcio->addItem( $I );
        
        $I = new FormItemClass("Pdf", FormItemClass::CONST_UPLOAD_HELPER, "", $this->gnfnwt('Pdf'), $OA, array());
        $I->setUpload(    $OA[ $this->gnfnwt('ActivitatId') ], $OA[ $this->gnfnwt('Pdf') ], 'Activitat', 'Activitat_Delete' );
        $FormDescripcio->addItem( $I );                
                                                      
        $RET["FormDescripcio"] = $FormDescripcio->toArray();
    
        /************************************** FORMULARI 3 *********************************************************/
/*        
        $FID = 'F3';
        $RET[$FID] = array();
        $id = $this->gnfnwt('Imatge');
        $RET[$FID][$id.'s'] = new FormItemClass("Imatge", FormItemClass::CONST_IMATGE_HELPER, "", $id, $OA, array());
        $RET[$FID][$id.'s']->setImage(    $OA[ $this->gnfnwt('ActivitatId') ], 
                                $this->getUrlByMida($OA, 'S'), 
                                's', 
                                'Activitat', 
                                'Activitat_Delete' );




        /************************************** FI FORMULARI 2 *********************************************************/

        return $RET;
        
    }

    public function getUrlByMida($OA, $mida) {        
        $Img = $OA[ $this->gnfnwt('Imatge') ];
        if(strlen($Img) > 0) {
            $ImgParts = explode(".", $Img);        
            return $ImgParts[0].$mida.'.jpg';
        } else {
            return '';
        }
    }

    const WUFOO_RES = 0;
    const WUFOO_ENVIAT = 1;
    const WUFOO_CONTESTAT = 2;
    public function getWufooStatus( $OA ) {
        return $OA[ $this->gnfnwt('WufooFormStatus') ];
    }
    public function setWufooStatus( $NouEstat, $OA ) {
        $OA[ $this->gnfnwt('WufooFormStatus') ] = $NouEstat;
        return $this->doUpdate($OA);        
    }

    public function doUpdate($ActivitatDetall) {        
        return $this->_doUpdate($ActivitatDetall, array('ActivitatId'));        
    }

    public function doDelete($ActivitatDetall) {                
        $ActivitatDetall[$this->getNewFieldNameWithTable('Actiu')] = 0;        
        return $this->doUpdate($ActivitatDetall);        
    }


}

?>