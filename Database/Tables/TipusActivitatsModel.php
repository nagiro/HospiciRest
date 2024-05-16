<?php

require_once BASEDIR."Database/DB.php";


class TipusActivitatsModel extends BDD {

    const FIELD_IdTipusActivitat = "IdTipusActivitat";
    const FIELD_Nom = "Nom";
    const FIELD_CategoriaVinculada = "CategoriaVinculada";
    const FIELD_SiteId  = "SiteId ";
    const FIELD_Actiu = "Actiu";

    public function __construct() {
        
        $OldFields = array('idTipusActivitat', 'Nom', 'CategoriaVinculada', 'site_id',  'actiu');
        $NewFields = array(self::FIELD_IdTipusActivitat, self::FIELD_Nom, self::FIELD_CategoriaVinculada, self::FIELD_SiteId , self::FIELD_Actiu );        
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
            usort($Options, function($a, $b) { return ($b->text <=> $a->text); });
        endforeach;
        if(empty($Options)) $Options[] = new OptionClass(98, 'Altres');
        return $Options;
    }

    public function getTipusActius($SiteId) { 
        return $this->_getRowWhere( 
            array( 
                $this->gofnwt(self::FIELD_SiteId ) => intval($SiteId),                 
                $this->gofnwt(self::FIELD_Actiu) => intval(1)
            ), true ); 
    }


}

?>