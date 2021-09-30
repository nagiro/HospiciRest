<?php 

require_once BASEDIR."Database/DB.php";

class SitesModel extends BDD {
    
    const FIELD_SiteId = "SiteId";
    const FIELD_Nom = "Nom";
    const FIELD_Actiu = "Actiu";
    const FIELD_Poble = "Poble";
    const FIELD_LogoUrl = "LogoUrl";
    const FIELD_WebUrl = "WebUrl";
    const FIELD_Telefon = "Telefon";
    const FIELD_Email = "Email";    

    public function __construct() {
        
                            
        $OldFields = array("site_id","nom","actiu","poble","logoUrl","webUrl","telefon","email");
        $NewFields = array( self::FIELD_SiteId, self::FIELD_Nom, self::FIELD_Actiu, self::FIELD_Poble, self::FIELD_LogoUrl, self::FIELD_WebUrl, self::FIELD_Telefon, self::FIELD_Email );
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