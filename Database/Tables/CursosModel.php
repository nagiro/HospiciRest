<?php

require_once BASEDIR."Database/DB.php";


class CursosModel extends BDD {


    public function __construct() {
        
        $OldFields = array('idCursos', 'TitolCurs', 'isActiu', 'Places',  'Codi', 'Descripcio','Preu', 'Horaris' ,'Categoria', 'OrdreSortida', 'DataAparicio', 'DataDesaparicio', 'DataInMatricula', 'DataFiMatricula', 'DataInici', 'VisibleWEB', 'site_id', 'actiu', 'cicle_id', 'activitat_id' ,'PDF', 'ADescomptes' ,'PagamentExtern' ,'PagamentIntern' ,'isRestringit' ,'DadesExtres');
        $NewFields = array("IdCurs", "TitolCurs", "IsActiu", "Places" , "Codi", "Descripcio", "Preu", "Horaris", "Categoria",  "OrdreSortida", 'DataAparicio', 'DataDesaparicio', 'DataInMatricula', 'DataFiMatricula', 'DataInici', 'VisibleWeb', 'SiteId', 'Actiu', 'CicleId', 'ActivitatId' ,'Pdf', 'ADescomptes' ,'PagamentExtern' ,'PagamentIntern' ,'IsRestringit' ,'DadesExtres');        
        parent::__construct("cursos", "CURSOS", $OldFields, $NewFields );

    }

    public function getEmptyObject($SiteId) {
        $OC = array();
        
        foreach($this->NewFieldsWithTableArray as $K => $V) {
            $OC[$V] = '';
        }
        $OC[$this->getNewFieldNameWithTable('IdNivell')] = 2;
        $OC[$this->getNewFieldNameWithTable('Actualitzacio')] = date('Y-m-d', time());
        $OC[$this->getNewFieldNameWithTable('Actiu')] = 1;
        $OC[$this->getNewFieldNameWithTable('SiteId')] = $SiteId;
        return $OC;
    }

    public function getCursById($idCurs) { return $this->_getRowWhere( array( $this->gofnwt('IdCurs') => intval($idCurs)) ); }    
    public function getRowCicleId($CicleId) { return $this->_getRowWhere( array( $this->gofnwt('CicleId') => intval($CicleId)) ); }
    public function getRowActivitatId($ActivitatId) { return $this->_getRowWhere( array( $this->gofnwt('ActivitatId') => intval($ActivitatId)) ); }

    public function getLlistatCursosCalendari( $idS, $paraules, $DataInicial, $DataFinal, $Estat ) {    
        

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