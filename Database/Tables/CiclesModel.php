<?php 

require_once BASEDIR."Database/DB.php";
require_once BASEDIR."Database/FormulariClass.php";

class CiclesModel extends BDD {

    public function __construct() {
        
        $OldFields = array('CicleID', 'Nom', 'Imatge', 'PDF',  'tCurt', 'dCurt','tMig', 'dMig' ,'tComplet', 'dComplet', 'extingit', 'Visibleweb', 'site_id', 'actiu');
        $NewFields = array('IdCicle', 'Nom', 'Imatge', 'Pdf',  'TCurt', 'DCurt','Tmig', 'DMig' ,'TComplet', 'DComplet', 'Extingit', 'VisibleWeb', 'SiteId', 'Actiu');
        parent::__construct("cicles", "CICLES", $OldFields, $NewFields );

    }

    public function getEmptyObject($SiteId) {
        $OC = array();
        
        foreach($this->NewFieldsWithTableArray as $K => $V) {
            $OC[$V] = '';
        }
        return $OC;
    }    
        
    public function getCiclesActiusSelect($idS) {
        $Options = array();
        foreach($this->getCiclesActius($idS) as $OC):
            $Options[] = new OptionClass($OC[$this->gnfnwt('IdCicle')], $OC[$this->gnfnwt('Nom')]);
        endforeach;
        return $Options;
    }

    public function getCiclesActius($SiteId) { 
        return $this->_getRowWhere( array( $this->gofnwt('SiteId') => intval($SiteId), $this->gofnwt('Extingit') => 0 ), true ); 
    }
    
}

?>