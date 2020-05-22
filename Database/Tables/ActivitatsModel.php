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
                         {$W}
                ORDER BY {$HM->getOldFieldNameWithTable('Dia')} asc
            ";

        $SQLW = array('site_id'=>$idS, 'DataInicial' => $DataInicial, 'DataFinal' => $DataFinal );        
                
        return $this->runQuery($SQL, array_merge( $SQLW , $WA ) );
        
    }

}

?>