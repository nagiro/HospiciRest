<?php 

require_once BASEDIR."Database/DB.php";

class UsuarisModel extends BDD {
    
    public function __construct() {
                        
        $OldFields = array("UsuariID", "Nivells_idNivells","DNI","Passwd", "Nom", "Cog1", "Cog2", "Genere", "Email", "Adreca", "CodiPostal", "Poblacio", "Poblaciotext", "Telefon", "Mobil", "Entitat", "Habilitat", "Actualitzacio", "site_id", "actiu", "facebook_id", "data_naixement");
        $NewFields = array("IdUsuari", "IdNivell", "Dni", "Password", "Nom", "Cog1", "Cog2", "Genere", "Email", "Adreca", "CodiPostal", "Poblacio", "PoblacioText", "Telefon", "Mobil", "Entitat", "Habilitat", "Actualitzacio", "SiteId", "Actiu", "IdFacebook", "DataNaixement");
        parent::__construct("usuaris", "USUARIS", $OldFields, $NewFields );
            
    }

    public function getEmptyObject($SiteId) {
        $OU = $this->getDefaultObject();        
        $OU[$this->getNewFieldNameWithTable('IdNivell')] = 2;
        $OU[$this->getNewFieldNameWithTable('Actualitzacio')] = date('Y-m-d', time());
        $OU[$this->getNewFieldNameWithTable('Actiu')] = 1;
        $OU[$this->getNewFieldNameWithTable('SiteId')] = $SiteId;
        return $OU;
    }


    public function doInsert($OU) {
        return $this->_doInsert($OU);        
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

    public function getNomCompletFields() {
        return "CONCAT({$this->getOldFieldNameWithTable('Cog1')},' ',{$this->getOldFieldNameWithTable('Cog2')},', ',{$this->getOldFieldNameWithTable('Nom')}) as USUARIS_NomComplet";
    }    
    public function getEmail($OU) { return $OU[$this->gnfnwt('Email')]; }
    public function getId($OU) { return $OU[$this->gnfnwt('IdUsuari')]; }
    public function getNomComplet($OU) { return $OU[$this->gnfnwt('Cog1')].' '.$OU[$this->gnfnwt('Cog2')].', '.$OU[$this->gnfnwt('Nom')]; }

    public function getUsuariRow($W) { return $this->_getRowWhere($W); }
    public function getUsuariId($Id) { return $this->_getRowWhere( array($this->gofnwt('IdUsuari') => $Id) ); }                
    public function getUsuariDNI($DNI) { return $this->_getRowWhere( array($this->gofnwt('Dni') => $DNI) ); }                

    public function ExisteixDNI($DNI = '') {
    
        $W = array();
        $W[ $this->getOldFieldNameWithTable('Dni') ] = $DNI;
        $W[ $this->getOldFieldNameWithTable('Actiu') ] = 1;
        $RET = $this->getUsuariRow( $W );            
        return (sizeof($RET) > 0) ? $RET[$this->gnfnwt('IdUsuari')] : 0;                
    }

}

?>
