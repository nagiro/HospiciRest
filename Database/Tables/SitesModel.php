<?php 

require_once BASEDIR."Database/DB.php";

class SitesModel extends BDD {
    
    public function __construct() {
        
                            
        $OldFields = array("site_id","nom","actiu","poble","logoUrl","webUrl","telefon","email");
        $NewFields = array("SiteId", "Nom", "Actiu", "Poble", "LogoUrl", "WebUrl", "Telefon", "Email");
        parent::__construct("sites", "SITES", $OldFields, $NewFields );        
        
    }

    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

    public function getAllSites() {

        $SQL = "Select {$this->getSelectFieldsNames()} from {$this->getTableName()}";                
                
        return $this->runQuery($SQL, array());
        
    }

    public function loadNom($IdSite) {
        $OS = $this->getById($IdSite);
        if(empty($OS)) return "n/d";
        else return $OS[$this->gnfnwt('Nom')];
    }

    public function getById($SiteId = 0) {
        return $this->runQuery("Select ".$this->getSelectFieldsNames().
                        " from ".$this->getTableName().
                        " where {$this->getOldFieldNameWithTable("SiteId")} = :id", 
                    array('id'=>$SiteId), true);
    }

    public function getSitesActius() {

        $SQL = "
                Select ".$this->getSelectFieldsNames()." 
                from ".$this->getTableName()." 
                where 
                        {$this->getOldFieldNameWithTable('Actiu')} = 1                
                ORDER BY {$this->getOldFieldNameWithTable('SiteId')} asc
            ";
        return $this->runQuery($SQL, array());
    }

    public function doUpdate($SiteDetall) {        
        return $this->_doUpdate($SiteDetall, array('SiteId'));        
    }

    public function doDelete($SiteDetall) {                
        $SiteDetall[$this->getNewFieldNameWithTable('Actiu')] = 0;        
        return $this->doUpdate($SiteDetall);        
    }

    public function getNew() {          
        $SQL = "
                INSERT INTO {$this->getOldTableName()} 
                ({$this->getOldFieldNameWithTable('Nom')}) VALUES ('Entra el nom...') 
            ";
        
        $lastId= $this->runQuery($SQL, array(), false, false, 'A');        
        return $this->getById($lastId);
    }

}

?>