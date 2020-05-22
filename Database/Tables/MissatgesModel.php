<?php 

require_once BASEDIR."Database/DB.php";

class MissatgesModel extends BDD {

    public function __construct() {

        $OldFields = array("MissatgeID", "Usuaris_UsuariID" , "Titol"   , "Text", "Date", "Publicacio", "site_id"   , "actiu"   , "isGlobal");
        $NewFields = array("MissatgeId", "UsuariId"         , "Titol"   , "Text", "Date", "Publicacio", "SiteId"    , "Actiu"   , "IsGlobal");
        parent::__construct("missatges", "Missatges", $OldFields, $NewFields );
            
    }        

    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

    /**
    * Aquesta taula sempre va associada amb les respostes que hi ha al missatge, que poden ser 0..N
    */
    public function getById($idMissatge = 0) {                

        return $this->runQuery("Select ".$this->getSelectFieldsNames().
                        " from ".$this->getTableName().
                        " where {$this->getOldFieldNameWithTable("MissatgeId")} = :id", 
                    array('id'=>$idMissatge), true);

    }

    public function getLlistaMissatges($idS = 1, $paraula = '', $lim = 10) {
        $UM = new UsuarisModel();   
        $RM = new RespostesModel();
        $LimInferior = $lim - 20;

        $W = ''; $WA = array();
        if(strlen($paraula) > 0) { $W = " AND ({$this->getOldFieldNameWithTable('Titol')} like :paraula1 
                                            OR {$this->getOldFieldNameWithTable('Text')} like :paraula2                                            
                                        ) ";
                                    $WA['paraula1'] = '%'.$paraula.'%';
                                    $WA['paraula2'] = '%'.$paraula.'%';
                                }
                        
        $SQL = "
                 Select {$this->getSelectFieldName('MissatgeId')}, 
                        {$this->getSelectFieldName('Titol')},
                        {$this->getSelectFieldName('Text')},
                        {$this->getSelectFieldName('Publicacio')},
                        {$this->getSelectFieldName('Titol')},
                        {$UM->getNomCompletFields()},
                        {$RM->getSQLQuantesRespostes($this->getOldFieldNameWithTable('MissatgeId'))}
                from ".$this->getTableName()." LEFT JOIN {$UM->getTableName()} ON ({$this->getOldFieldNameWithTable('UsuariId')} = {$UM->getOldFieldNameWithTable('IdUsuari')})
                where 
                        {$this->getOldFieldNameWithTable('SiteId')} = :site_id
                AND     {$this->getOldFieldNameWithTable('Actiu')} = 1                
                AND     {$this->getOldFieldNameWithTable('Publicacio')} <= :data_actual 
                        {$W}
                ORDER BY    {$this->getOldFieldNameWithTable('Publicacio')} desc, 
                            {$this->getOldFieldNameWithTable('MissatgeId')} desc
                LIMIT :limInf, 20
            ";        
                    
        $SQLW = array('site_id'=>$idS, 'data_actual'=> date('Y-m-d', time()));
        
        return $this->runQuery($SQL, array_merge( $SQLW , $WA, array('limInf'=>$LimInferior) ) );

    }

    public function doUpdate($MissatgeDetall) {        
        $MissatgeDetall[$this->getNewFieldNameWithTable('Actiu')] = 1;                
        return $this->_doUpdate($MissatgeDetall, array('MissatgeId'));
    }

    public function doDelete($MissatgeDetall) {                
        $MissatgeDetall[$this->getNewFieldNameWithTable('Actiu')] = 0;                
        return $this->doUpdate($MissatgeDetall);        
    }

    public function getNew($idU, $idS) {          
        $D = date("Y-m-d H:i:s", time());
        $SQL = "
                INSERT INTO {$this->getOldTableName()} 
                ({$this->getOldFieldNameWithTable('Titol')},
                {$this->getOldFieldNameWithTable('SiteId')}, 
                {$this->getOldFieldNameWithTable('UsuariId')},
                {$this->getOldFieldNameWithTable('Date')},
                {$this->getOldFieldNameWithTable('Publicacio')},
                {$this->getOldFieldNameWithTable('Actiu')},
                {$this->getOldFieldNameWithTable('IsGlobal')}
                ) VALUES ('', {$idS}, {$idU}, '{$D}', '{$D}', 0, 0) 
            ";
        
        $lastId= $this->runQuery($SQL, array(), false, false, 'A');        
        return $this->getById($lastId);
    }
}

?>