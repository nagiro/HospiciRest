<?php

require_once BASEDIR."Database/Tables/HorarisModel.php";
require_once BASEDIR."Database/Tables/HorarisEspaisModel.php";
require_once BASEDIR."Database/Tables/EspaisModel.php";

require_once BASEDIR."Database/DB.php";


class ActivitatsModel extends BDD {


    public function __construct() {

        $OldFields = array("actiu", "ActivitatID", "Categories", "Cicles_CicleID", "dComplet",           "dCurt",           "Definiciohoraris", "Descripcio", "dMig",           "Estat", "Imatge", "InfoPractica",       "isEntrada", "isImportant", "Nom", "Organitzador", "PDF", "Places", "Preu", "PreuReduit", "Publicable", "PublicaWEB",    "Responsable", "site_id", "tComplet",     "tCurt",     "TipusActivitat_idTipusActivitat", "tipusEnviament", "tMig");
        $NewFields = array("Actiu", "ActivitatId", "Categories", "CiclesCicleId" , "DescripcioCompleta", "DescripcioCurta", "DefinicioHoraris", "Descripcio", "DescripcioMig",  "Estat", "Imatge", "InformacioPractica", "IsEntrada", "IsImportant", "Nom", "Organitzador", "Pdf", "Places", "Preu", "PreuReduit", "Publicable", "PublicableWeb", "Responsable", "SiteId",  "TitolComplet", "TitolComplet", "TipusActivitatId",                "TipusEnviament", "TitolMig");
        parent::__construct("activitats", "ACTIVITATS", $OldFields, $NewFields );

    }

    public function getEmptyObject() {
        $O = $this->getDefaultObject();      
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

}

?>