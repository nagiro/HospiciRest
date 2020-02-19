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
        
        $SQL = "
                Select ".$this->getSelectFieldsNames()." 
                from ".$this->getTableName()." 
                where 
                        {$this->getOldFieldNameWithTable('SITE_ID')} = :site_id
                AND     {$this->getOldFieldNameWithTable('ACTIU')} = 1
                AND     {$this->getOldFieldNameWithTable('IS_ACTIVA')} = :estat
                ORDER BY {$this->getOldFieldNameWithTable('ORDRE')} asc
            ";
                    
        return $this->runQuery($SQL, array('site_id'=>$idS, 'estat' => $estat));

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