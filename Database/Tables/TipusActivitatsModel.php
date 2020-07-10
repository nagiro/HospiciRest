<?php

require_once BASEDIR."Database/DB.php";


class TipusActivitatsModel extends BDD {


    public function __construct() {
        
        $OldFields = array('idTipusActivitat', 'Nom', 'CategoriaVinculada', 'site_id',  'actiu');
        $NewFields = array("IdTipusActivitat", "Nom", "CategoriaVinculada", "SiteId" , "Actiu");        
        parent::__construct("tipusactivitat", "TIPUS_ACTIVITATS", $OldFields, $NewFields );

    }

    public function getEmptyObject($SiteId) {
        $OC = array();
        
        foreach($this->NewFieldsWithTableArray as $K => $V) {
            $OC[$V] = '';
        }
        $OC[$this->gnfnwt('SiteId')] = $SiteId;
        return $OC;
    }

    public function getTipusById($idTipus) { return $this->_getRowWhere( array( $this->gofnwt('IdTipusActivitat') => intval($idTipus)) ); }  

    public function getTipusActivitatsSelect($idS) {
        $Options = array();
        foreach($this->getTipusActius($idS) as $OT):
            $Options[] = new OptionClass($OT[$this->gnfnwt('IdTipusActivitat')], $OT[$this->gnfnwt('Nom')]);
        endforeach;
        return $Options;
    }

    public function getTipusActius($SiteId) { 
        return $this->_getRowWhere( 
            array( 
                $this->gofnwt('SiteId') => intval($SiteId),                 
                $this->gofnwt('Actiu') => intval(1)
            ), true ); 
    }


}

?>