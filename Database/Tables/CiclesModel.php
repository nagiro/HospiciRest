<?php 

require_once BASEDIR."Database/DB.php";
require_once BASEDIR."Database/FormulariClass.php";

class CiclesModel extends BDD {

    const FIELD_IdCicle = "IdCicle";
    const FIELD_Nom = "Nom";
    const FIELD_Imatge = "Imatge";
    const FIELD_Pdf = "Pdf";
    const FIELD_TCurt = "TCurt";
    const FIELD_DCurt = "DCurt";
    const FIELD_Tmig = "Tmig";
    const FIELD_DMig  = "DMig ";
    const FIELD_TComplet = "TComplet";
    const FIELD_DComplet = "DComplet";
    const FIELD_Extingit = "Extingit";
    const FIELD_VisibleWeb = "VisibleWeb";
    const FIELD_SiteId = "SiteId";
    const FIELD_Actiu = "Actiu";    

    public function __construct() {
        
        $OldFields = array('CicleID', 'Nom', 'Imatge', 'PDF',  'tCurt', 'dCurt','tMig', 'dMig' ,'tComplet', 'dComplet', 'extingit', 'Visibleweb', 'site_id', 'actiu');
        $NewFields = array( self::FIELD_IdCicle, self::FIELD_Nom, self::FIELD_Imatge, self::FIELD_Pdf, self::FIELD_TCurt, self::FIELD_DCurt, self::FIELD_Tmig, self::FIELD_DMig , self::FIELD_TComplet, self::FIELD_DComplet, self::FIELD_Extingit, self::FIELD_VisibleWeb, self::FIELD_SiteId, self::FIELD_Actiu );
        parent::__construct("cicles", "CICLES", $OldFields, $NewFields );

    }

    public function getEmptyObject($SiteId) {
        $OC = array();
        
        foreach($this->NewFieldsWithTableArray as $K => $V) {
            $OC[$V] = '';
        }
        return $OC;
    }    
        
    public function getCicleById($idCicle) { return $this->_getRowWhere( array( $this->gofnwt('IdCicle') => intval($idCicle)) ); }    

    public function getCiclesActiusSelect($idS) {
        $Options = array();
        foreach($this->getCiclesActius($idS) as $OC):
            $Options[] = new OptionClass($OC[$this->gnfnwt('IdCicle')], $OC[$this->gnfnwt('Nom')]);
        endforeach;
        return $Options;
    }

    public function getCiclesActius($SiteId) { 
        return $this->_getRowWhere( 
            array( 
                $this->gofnwt('SiteId') => intval($SiteId), 
                $this->gofnwt('Extingit') => 0,
                $this->gofnwt('Actiu') => 1
            ), true ); 
    }
    
}

?>