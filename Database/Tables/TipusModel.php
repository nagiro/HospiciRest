<?php

require_once BASEDIR."Database/DB.php";


class TipusModel extends BDD {


    public function __construct() {
        
        $OldFields = array('idTipus', 'tipusNom', 'tipusDesc', 'site_id',  'actiu');
        $NewFields = array("IdTipus", "Nom", "Descripcio", "SiteId" , "Actiu");        
        parent::__construct("tipus", "TIPUS", $OldFields, $NewFields );

    }

    public function getEmptyObject($SiteId) {
        $OC = array();
        
        foreach($this->NewFieldsWithTableArray as $K => $V) {
            $OC[$V] = '';
        }
        $OC[$this->gnfnwt('SiteId')] = $SiteId;
        return $OC;
    }

    public function getTipusById($idTipus) { return $this->_getRowWhere( array( $this->gofnwt('IdTipus') => intval($idTipus)) ); }  
    public function getTipusByNom($idNom) { return $this->_getRowWhere( array( $this->gofnwt('Nom') => $idNom, $this->gofnwt('Actiu') => 1 ), true ); }  

    public function getTipusSelect($Nom, $idS) {
        $Options = array();
        foreach($this->getTipusByNom($Nom, $idS) as $OT):            
            $Options[] = new OptionClass($OT[$this->gnfnwt('IdTipus')], $OT[$this->gnfnwt('Descripcio')]);
        endforeach;
        return $Options;
    }


}

?>