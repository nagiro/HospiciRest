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


    public function __construct() {

        $OldFields = array("actiu", "ActivitatID", "Categories", "Cicles_CicleID", "dComplet",           "dCurt",           "Definiciohoraris", "Descripcio", "dMig",           "Estat", "Imatge", "InfoPractica",       "isEntrada", "isImportant", "Nom", "Organitzador", "PDF", "Places", "Preu", "PreuReduit", "Publicable", "PublicaWEB",    "Responsable", "site_id", "tComplet",     "tCurt",     "TipusActivitat_idTipusActivitat", "tipusEnviament", "tMig", "ImatgeS", "ImatgeM", "ImatgeL", "WufooFormStatus");
        $NewFields = array("Actiu", "ActivitatId", "Categories", "CiclesCicleId" , "DescripcioCompleta", "DescripcioCurta", "DefinicioHoraris", "Descripcio", "DescripcioMig",  "Estat", "Imatge", "InformacioPractica", "IsEntrada", "IsImportant", "Nom", "Organitzador", "Pdf", "Places", "Preu", "PreuReduit", "Publicable", "PublicableWeb", "Responsable", "SiteId",  "TitolComplet", "TitolCurt", "TipusActivitatId",                "TipusEnviament", "TitolMig", "ImatgeS", "ImatgeM", "ImatgeL", "WufooFormStatus");
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

    public function genXML(){
/*
        $this->setLayout(null);
        $this->setTemplate(null);
        $LOH = ActivitatsPeer::getLlistatWord($this->FACTIVITATS,$this->IDS,false);
                    
        //Creem l'objecte XML
        $i = 1;  
        $document = "<document>\n";                    
        foreach($LOH as $OH):
                                                        
            $OA = $OH->getActivitats();
            $LE = $OH->getArrayEspais();
                                                                                                            
            $document .= "<caixa>\n";
            $document .= "  <data_inicial>".$OA->getPrimerHorari()->getDia('Y-m-d')."</data_inicial>\n";
            $document .= "  <data_fi>".$OA->getUltimHorari()->getDia('Y-m-d')."</data_fi>\n";
            $document .= "  <tipus_activitat>".$OA->getNomTipusActivitat()."</tipus_activitat>\n";
            $document .= "  <cicle>".$OA->getCicles()->getTmig()."</cicle>\n";
            $document .= "  <tipologia>".$OA->getCategories()."</tipologia>\n";
            $document .= "  <importancia>".$OA->getImportancia()."</importancia>\n";                        
            $document .= "  <titol>".$OA->getTmig()."</titol>\n";
            $document .= "  <text>".strip_tags(html_entity_decode($OA->getDmig()))."</text>\n";
            $document .= "  <url>".$this->getController()->genUrl('@web_menu_click_activitat?idCicle='.$OA->getCiclesCicleid().'&idActivitat='.$OA->getActivitatid().'&titol='.$OA->getNomForUrl() , true )."</url>\n";
            $document .= "  <hora_inici>".$OH->getHorainici("H.i")."</hora_inici>\n";
            $document .= "  <hora_fi>".$OH->getHorafi("H.i")."</hora_fi>\n";
            $document .= "  <espais>".implode(",",$LE)."</espais>\n";
            $document .= "  <organitzador>".html_entity_decode( $OA->getOrganitzador() )."</organitzador>\n";
            $document .= "  <info_practica>".strip_tags( html_entity_decode( $OA->getInfopractica() ) )."</info_practica>\n";
            $document .= "  <url_img_s>http://www.hospici.cat/images/activitats/A-".$OA->getActivitatid()."-M.jpg</url_img_s>\n";
            $document .= "  <url_img_m>http://www.hospici.cat/images/activitats/A-".$OA->getActivitatid()."-L.jpg</url_img_m>\n";
            $document .= "  <url_img_l>http://www.hospici.cat/images/activitats/A-".$OA->getActivitatid()."-XL.jpg</url_img_l>\n";                                                
            $document .= "</caixa>\n";                                                                                                
                                                                                                                                    
        endforeach;            
        $document .= "</document>\n";                  
*/
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