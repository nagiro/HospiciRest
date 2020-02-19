<?php 

require_once BASEDIR."Database/DB.php";

class UsuarisModel extends BDD {
    
    public function __construct() {
                        
        $OldFields = array("UsuariID", "Nivells_idNivells","DNI","Passwd", "Nom", "Cog1", "Cog2", "Email", "Adreca", "CodiPostal", "Poblacio", "Poblaciotext", "Telefon", "Mobil", "Entitat", "Habilitat", "Actualitzacio", "site_id", "actiu", "facebook_id", "data_naixement");
        $NewFields = array("IdUsuari", "IdNivell", "Dni", "Password", "Nom", "Cog1", "Cog2", "Email", "Adreca", "CodiPostal", "Poblacio", "PoblacioText", "Telefon", "Mobil", "Entitat", "Habilitat", "Actualitzacio", "SiteId", "Actiu", "IdFacebook", "DataNaixement");
        parent::__construct("usuaris", "USUARIS", $OldFields, $NewFields );
            
    }

    public function doLogin($login, $Password, $SiteId) {

        $SQL = "Select {$this->getSelectFieldsNames()} 
                from {$this->getTableName()}
                where {$this->getOldFieldNameWithTable('Dni')} = :login
                  AND {$this->getOldFieldNameWithTable('Password')} = :Password
                  AND {$this->getOldFieldNameWithTable('Actiu')} = 1
                  AND {$this->getOldFieldNameWithTable('SiteId')} = :SiteId
                ";        
                
        return $this->runQuery($SQL, array('login'=>$login, 'Password'=>$Password, 'SiteId' => $SiteId));
        
    }
}

?>