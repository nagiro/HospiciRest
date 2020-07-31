<?php

require_once BASEDIR."Database/DB.php";


class CursosModel extends BDD {


    public function __construct() {
        
        $OldFields = array('idCursos', 'TitolCurs', 'isActiu', 'Places',  'Codi', 'Descripcio','Preu', 'Horaris' ,'Categoria', 'OrdreSortida', 'DataAparicio', 'DataDesaparicio', 'DataInMatricula', 'DataFiMatricula', 'DataInici', 'VisibleWEB', 'site_id', 'actiu', 'cicle_id', 'activitat_id' ,'PDF', 'ADescomptes' ,'PagamentExtern' ,'PagamentIntern' ,'isRestringit' ,'DadesExtres', 'Teatre');
        $NewFields = array("IdCurs", "TitolCurs", "IsActiu", "Places" , "Codi", "Descripcio", "Preu", "Horaris", "Categoria",  "OrdreSortida", 'DataAparicio', 'DataDesaparicio', 'DataInMatricula', 'DataFiMatricula', 'DataInici', 'VisibleWeb', 'SiteId', 'Actiu', 'CicleId', 'ActivitatId' ,'Pdf', 'ADescomptes' ,'PagamentExtern' ,'PagamentIntern' ,'IsRestringit' ,'DadesExtres', 'Teatre');        
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

    public function getCursId($OC) { return $OC[$this->gnfnwt('IdCurs')]; }
    public function getCursById($idCurs) { return $this->_getRowWhere( array( $this->gofnwt('IdCurs') => intval($idCurs)) ); }    
    public function getRowCicleId($CicleId) { return $this->_getRowWhere( array( $this->gofnwt('CicleId') => intval($CicleId)) ); }
    public function getRowActivitatId($ActivitatId) { return $this->_getRowWhere( array( $this->gofnwt('ActivitatId') => intval($ActivitatId)) ); }
    
    public function getDescomptes($CursObject) {
        require_once BASEDIR."Database/Tables/DescomptesModel.php";
        $DM = new DescomptesModel();
        $CursBuit = $DM->getEmptyObject($CursObject[$this->gnfnwt('SiteId')]);
        $Descomptes = array( $CursBuit );
        return array_merge( $Descomptes , $DM->getDescomptesByCurs($CursObject[$this->gnfnwt('IdCurs')]) );        
    }    

    public function getPreuAplicantDescompte($CursObject, $idDescompte) {        
        require_once BASEDIR."Database/Tables/DescomptesModel.php";
        $DM = new DescomptesModel();

        $DetallDescompteAplicat = $DM->getDescompteById($idDescompte);
        $PreuCurs = $CursObject[$this->gnfnwt('Preu')];
        
        if( $DetallDescompteAplicat[$DM->gnfnwt('Percentatge')] > 0 ) $PreuCurs = round( $PreuCurs - ($PreuCurs * $DetallDescompteAplicat[$DM->gnfnwt('Percentatge')] / 100), 2); 
        elseif( $DetallDescompteAplicat[$DM->gnfnwt('Preu')] > 0 ) $PreuCurs = $DetallDescompteAplicat[$DM->gnfnwt('Preu')]; 

        return $PreuCurs;
    }

    public function getLlistatCursosWeb($SiteId) { 

        $DataInicial = date('Y-m-d', time());

        $SQL = "
                Select {$this->getSelectFieldsNames()}
                from {$this->getTableName()}                 
                where 
                         {$this->getOldFieldNameWithTable('SiteId')} = :site_id
                AND      {$this->getOldFieldNameWithTable('Actiu')} = 1                
                AND      {$this->getOldFieldNameWithTable('DataFiMatricula')} >= :Data1
                AND      {$this->getOldFieldNameWithTable('DataInMatricula')} <= :Data2
                AND      {$this->getOldFieldNameWithTable('VisibleWeb')} = 1
                ORDER BY {$this->getOldFieldNameWithTable('DataInMatricula')} asc
            ";

        $SQLW = array('site_id'=>$SiteId, 'Data1' => $DataInicial, 'Data2' => $DataInicial );        
                
        return $this->runQuery($SQL, $SQLW);

    }


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

    public function getSeientsOcupats($ObjecteCurs) {
        require_once BASEDIR."Database/Tables/MatriculesModel.php";
        $RET = array('QuantesMatricules' => 0, 'Localitats' => array());
        $MM = new MatriculesModel();
        
        if(strlen($ObjecteCurs[$this->gnfnwt('Teatre')]) > 0) {
            $MatriculesLocalitats = $MM->getMatriculesByCurs( $this->getCursId($ObjecteCurs));
            $RET['QuantesMatricules'] = sizeof($MatriculesLocalitats);
            foreach($MatriculesLocalitats as $MatriculesObject) {
                $RET['Localitats'][] = $MM->getLocalitatArray($MatriculesObject);
            }            
        } else {
            $RET['QuantesMatricules'] = $MM->getQuantesMatriculesHiHa( $this->getCursid($ObjecteCurs));
        }
                
        return $RET;
        
    }

    public function hasEscullLocalitats($CursObject) {
        return (strlen($CursObject[$this->gnfnwt('Teatre')]) > 0);
    }    

    /**
    * Funció que carrega el teatre escollit pel curs en qüestió
    **/
    public function getTeatre( $CursObject ) {
        if( strlen($CursObject[$this->gnfnwt('Teatre')]) > 0 ){
            $TeatreJson = file_get_contents( TEATRES . $CursObject[$this->gnfnwt('SiteId')] . '-' . $CursObject[$this->gnfnwt('Teatre')] . '.json' );
            return json_decode($TeatreJson, true);        
        } else {
            return array('Seients'=> array());
        }
    }

    public function potMatricularSegonsRestriccio($DNI, $idCurs) {
        return false;
    }

}

?>