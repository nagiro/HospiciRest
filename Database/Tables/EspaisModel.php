<?php 

require_once BASEDIR."Database/DB.php";

class EspaisModel extends BDD {

    public function __construct() {

        $OldFields = array("actiu", "descripcio", "EspaiID", "isLlogable", "Nom", "Ordre", "site_id");
        $NewFields = array("Actiu", "Descripcio", "EspaiId", "IsLlogable", "Nom", "Ordre", "SiteId");
        parent::__construct("espais", "ESPAIS", $OldFields, $NewFields );                        

    }
    
    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

    /**
    * Retorna els espais disponibles del SiteId
    */
    public function getEspaisDisponiblesSite($idSite) {
        
        $LlistatEspais = $this->_getRowWhere( 
            array( 
                $this->gofnwt('SiteId') => $idSite,
                $this->gofnwt('Actiu') => 1,
                $this->gofnwt('IsLlogable') => 1
            ) , true );        
            
        //Els ordenem per ordre sortida        
        usort($LlistatEspais, function ($a, $b) {
            return $a[$this->gnfnwt('Ordre')] - $b[$this->gnfnwt('Ordre')];
        });

        return $LlistatEspais;
        
    }

    // Carrego la informació d'un espai i els seus horaris ocupats ( si n'hi ha )
    public function getEspaiDetall($idEspai) {
        return $this->_getRowWhere( array( $this->gofnwt('EspaiId') => intval($idEspai)) );
    }

    public function getImageUrlsFromEspai($idEspai) {
        
        $LlistatImatges = array();        
        $LlistatImatges['ImatgesS'] = array();
        
        foreach( scandir( IMATGES_DIR_ESPAIS . 'S/' ) as $I):
            if(strstr($I, "E-{$idEspai}-")) 
                $LlistatImatges['ImatgesS'][] = IMATGES_URL_ESPAIS . 'S/' . $I;
        endforeach;                        

        $LlistatImatges['ImatgesL'] = array();
        foreach( scandir( IMATGES_DIR_ESPAIS . 'L/' ) as $I):
            if(strstr($I, "E-{$idEspai}-")) 
                $LlistatImatges['ImatgesL'][] = IMATGES_URL_ESPAIS . 'L/' . $I;
        endforeach;                        

        return $LlistatImatges;

    }
}

?>