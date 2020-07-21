<?php

require_once BASEDIR."Database/DB.php";


class DescomptesModel extends BDD {


    public function __construct() {
        
        $OldFields = array('idDescompte', 'idExtern', 'Nom', 'Percentatge',  'Preu', 'Tipus','actiu', 'site_id');
        $NewFields = array("IdDescompte", "ExternId", "Nom", "Percentatge" , "Preu", "Tipus", "Actiu", "SiteId");        
        parent::__construct("descomptes", "DESCOMPTES", $OldFields, $NewFields );

    }

    public function getEmptyObject($SiteId) {
        $OC = array();
        
        foreach($this->NewFieldsWithTableArray as $K => $V) {
            $OC[$V] = '';
        }
        $OC[$this->getNewFieldNameWithTable('IdDescompte')] = -1;
        $OC[$this->getNewFieldNameWithTable('Nom')] = " -- Cap descompte -- ";
        $OC[$this->getNewFieldNameWithTable('Percentatge')] = 0;
        $OC[$this->getNewFieldNameWithTable('Tipus')] = 1;
        $OC[$this->getNewFieldNameWithTable('Actiu')] = 1;
        $OC[$this->getNewFieldNameWithTable('SiteId')] = $SiteId;
        return $OC;
    }    

    public function getDescompteById($idDescompte) { 
        return $this->_getRowWhere( array( $this->gofnwt('IdDescompte') => intval($idDescompte) ), false );         
    }    

    public function getDescomptesByCurs($idCurs) { 
        return $this->_getRowWhere( 
            array( 
                $this->gofnwt('ExternId') => intval($idCurs),
                $this->gofnwt('Tipus') => 1,
                $this->gofnwt('Actiu') => 1                
            ), true 
        ); 
    }    


}

?>