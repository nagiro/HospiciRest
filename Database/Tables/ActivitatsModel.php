<?php

require_once BASEDIR."Database/Tables/HorarisModel.php";
require_once BASEDIR."Database/Tables/HorarisEspaisModel.php";
require_once BASEDIR."Database/Tables/EspaisModel.php";
require_once BASEDIR."Database/Tables/CiclesModel.php";
require_once BASEDIR."Database/FormulariClass.php";

require_once BASEDIR."Database/DB.php";


class ActivitatsModel extends BDD {


    public function __construct() {

        $OldFields = array("actiu", "ActivitatID", "Categories", "Cicles_CicleID", "dComplet",           "dCurt",           "Definiciohoraris", "Descripcio", "dMig",           "Estat", "Imatge", "InfoPractica",       "isEntrada", "isImportant", "Nom", "Organitzador", "PDF", "Places", "Preu", "PreuReduit", "Publicable", "PublicaWEB",    "Responsable", "site_id", "tComplet",     "tCurt",     "TipusActivitat_idTipusActivitat", "tipusEnviament", "tMig");
        $NewFields = array("Actiu", "ActivitatId", "Categories", "CiclesCicleId" , "DescripcioCompleta", "DescripcioCurta", "DefinicioHoraris", "Descripcio", "DescripcioMig",  "Estat", "Imatge", "InformacioPractica", "IsEntrada", "IsImportant", "Nom", "Organitzador", "Pdf", "Places", "Preu", "PreuReduit", "Publicable", "PublicableWeb", "Responsable", "SiteId",  "TitolComplet", "TitolComplet", "TipusActivitatId",                "TipusEnviament", "TitolMig");
        parent::__construct("activitats", "ACTIVITATS", $OldFields, $NewFields );

    }

    public function getEmptyObject($idS) {
        $O = $this->getDefaultObject();      
        $O[$this->gnfnwt('SiteId')] = $idS;
        $RET = array('ACTIVITAT' => $O, 'HORARIS' => array());
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
                
                if( stripos($FieldName, $this->getNewTableName()) !== false ):
                    $RET['ACTIVITAT'][ $FieldName ] = $Field;
                elseif( stripos( $FieldName , $HM->getNewTableName() ) !== false ):
                    $idH = $Row[ $HM->gnfnwt( 'HorariId' ) ] ;                    
                    $idE = $Row[ $EM->gnfnwt( 'EspaiId' ) ] ;
                    if( !isset( $RET['HORARIS'][$idH] ) ) $RET['HORARIS'][$idH] = array( 'HORARI' => array(), 'ESPAIS' => array() );
                    $RET['HORARIS'][$idH]['HORARI'][$FieldName] = $Field; 
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
    function Form($idActivitat, $idSite) {
        
        $OA = $this->getEmptyObject($idSite);
        if($idActivitat > 0) $OA = $this->getActivitatById($idActivitat);

        $CM = new CiclesModel();
        
        $RET = array();
        $RET[] = new FormulariClass("Nom", FormulariClass::CONST_INPUT_HELPER, "", $this->gnfnwt('Nom'));
        $F = new FormulariClass("Cicle vinculat", FormulariClass::CONST_SELECT_HELPER, "1", $this->gnfnwt('CiclesCicleId'));
        $F->setOptions($CM->getCiclesActiusSelect($idSite));
        $RET[] = $F;
        $RET[] = new FormulariClass("Tipus activitat", FormulariClass::CONST_SELECT_HELPER, "0", $this->gnfnwt('TipusActivitatId'));

        foreach($RET as $K => $R):
            $RET[] = $R->getArrayObject();
        endforeach;

        return $RET;
        
    }

}

?>