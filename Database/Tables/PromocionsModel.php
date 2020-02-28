<?php 

require_once BASEDIR."Database/DB.php";

class PromocionsModel extends BDD {

    public function __construct() {
        
        $OldFields = array("PromocioID", "Nom", "Titol", "SubTitol", "Ordre", "isActiva", "URL", "ImatgeS", "ImatgeM", "ImatgeL", "site_id", "actiu");
        $NewFields = array("PROMOCIO_ID", "NOM", "TITOL", "SUBTITOL", "ORDRE", "IS_ACTIVA", "URL", "IMATGE_S", "IMATGE_M", "IMATGE_L", "SITE_ID", "ACTIU");
        parent::__construct("promocions", "PROMOCIONS", $OldFields, $NewFields );
            
    }        

    public function getById($idPromocio = 0) {
        return $this->runQuery("Select ".$this->getSelectFieldsNames().
                        " from ".$this->getTableName().
                        " where {$this->getOldFieldNameWithTable("PROMOCIO_ID")} = :id", 
                    array('id'=>$idPromocio), true);
    }

    public function getLlistaPromocions($idS, $paraula, $estat) {
        
        $W = ''; $WA = array();        
        if(strlen($paraula) > 0) { $W = " AND ({$this->getOldFieldNameWithTable('TITOL')} like :paraula1 
                                            OR {$this->getOldFieldNameWithTable('SUBTITOL')} like :paraula2
                                            OR {$this->getOldFieldNameWithTable('NOM')} like :paraula3
                                        ) "; 
                                    $WA['paraula1'] = '%'.$paraula.'%';
                                    $WA['paraula2'] = '%'.$paraula.'%';
                                    $WA['paraula3'] = '%'.$paraula.'%';
                                }

        $SQL = "
                Select ".$this->getSelectFieldsNames()." 
                from ".$this->getTableName()." 
                where 
                        {$this->getOldFieldNameWithTable('SITE_ID')} = :site_id
                AND     {$this->getOldFieldNameWithTable('ACTIU')} = 1
                AND     {$this->getOldFieldNameWithTable('IS_ACTIVA')} = :estat
                        {$W}
                ORDER BY {$this->getOldFieldNameWithTable('ORDRE')} asc
            ";

        $SQLW = array('site_id'=>$idS, 'estat' => $estat);
        
        return $this->runQuery($SQL, array_merge( $SQLW , $WA ) );

    }

    public function getPromocionsActives($idS) {

        $SQL = "
                Select ".$this->getSelectFieldsNames()." 
                from ".$this->getTableName()." 
                where 
                        {$this->getOldFieldNameWithTable('SITE_ID')} = :site_id
                AND     {$this->getOldFieldNameWithTable('ACTIU')} = 1
                AND     {$this->getOldFieldNameWithTable('IS_ACTIVA')} = 1
                ORDER BY {$this->getOldFieldNameWithTable('ORDER')} asc
            ";

        return $this->runQuery($SQL, array('id'=>$idPromocio), true);
    }

    public function doUpdate($PromocioDetall) {        
        return $this->_doUpdate($PromocioDetall, array('PROMOCIO_ID'));        
    }

    public function doDelete($PromocioDetall) {                
        $PromocioDetall[$this->getNewFieldNameWithTable('ACTIU')] = 0;        
        return $this->doUpdate($PromocioDetall);        
    }

    public function getNew() {          
        $SQL = "
                INSERT INTO {$this->getOldTableName()} 
                ({$this->getOldFieldNameWithTable('NOM')}) VALUES ('Entra el nom...') 
            ";
        
        $lastId= $this->runQuery($SQL, array(), false, false, 'A');        
        return $this->getById($lastId);
    }
}

?>