<?php 

require_once BASEDIR."Database/DB.php";

class UsuarisSitesModel extends BDD {
    
    public function __construct() {
                        
        $OldFields = array("usuari_id", "site_id","nivell_id","actiu");
        $NewFields = array("UsuariId", "SiteId","NivellId","Actiu");
        parent::__construct("usuaris_sites", "USUARIS_SITES", $OldFields, $NewFields );
            
    }

    public function getEmptyObject($SiteId) {
        $O = $this->getDefaultObject();        
        $O[$this->gnfnwt('NivellId')] = 2;        
        $O[$this->gnfnwt('SiteId')] = $SiteId;
        $O[$this->gnfnwt('Actiu')] = 1;
        return $O;
    }

    public function doInsert($O) { return $this->_doInsert($O); }
    public function doUpdate($O) {
        $W = array();
        $W[] = 'UsuariId';
        $W[] = 'SiteId';
        return $this->_doUpdate($O, $W); 
    }

    public function addUsuariASite($idU, $idS) {
        //Mirem si ja hi ha la relació.
        $OUS = $this->getUsuariSite($idU, $idS);
        if( sizeof($OUS) == 0 ) { 
            $OUS = $this->getEmptyObject($idS); 
            $OUS[ $this->gnfnwt('Actiu') ] = 1;
            $OUS[ $this->gnfnwt('UsuariId') ] = $idU;                        
            $this->doInsert($OUS);
        } else {
            if($OUS[ $this->gnfnwt('Actiu') ] == 0) {
                $OUS[ $this->gnfnwt('Actiu') ] = 1;
                $this->doUpdate($OUS);
            }
        }
        

    }

    public function getUsuariRow($W) { return $this->_getRowWhere($W); }
    public function getUsuariSite($idU, $idS) {
        $W = array( $this->gofnwt('UsuariId') => $idU );
        $W[ $this->gofnwt('SiteId') ] = $idS; 
        return $this->_getRowWhere($W);
    }
    

}

?>
