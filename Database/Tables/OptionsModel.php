<?php

require_once BASEDIR."Database/DB.php";


class OptionsModel extends BDD {


    public function __construct() {
        
        $OldFields = array('option_id', 'site_id', 'valor');
        $NewFields = array("IdOption", "SiteId", "Valor");        
        parent::__construct("options", "OPTIONS", $OldFields, $NewFields );

    }

    public function getEmptyObject($SiteId) {
        $OC = array();
        
        foreach($this->NewFieldsWithTableArray as $K => $V) {
            $OC[$V] = '';
        }                
        $OC[$this->getNewFieldNameWithTable('SiteId')] = $SiteId;
        return $OU;
    }

    public function getOption($id, $SiteId) {
        $RET = $this->_getRowWhere( array( $this->gofnwt('IdOption') => $id, $this->gofnwt('SiteId') => $SiteId ) );                
        if(sizeof($RET) > 0) return $RET['OPTIONS_Valor'];
        else {
            $RET = $this->_getRowWhere( array( $this->gofnwt('IdOption') => $id, $this->gofnwt('SiteId') => 1 ) );                
            if(sizeof($RET) > 0) return $RET['OPTIONS_Valor'];
            else return false;        
        }
    }

}

?>