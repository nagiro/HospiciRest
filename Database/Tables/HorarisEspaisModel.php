<?php 

require_once BASEDIR."Database/DB.php";

class HorarisEspaisModel extends BDD {

    public function __construct() {

        $OldFields = array("actiu", "Espais_EspaiID", "Horaris_HorarisID", "idEspaiextern", "idHorarisEspais", "Material_idMaterial", "site_id");
        $NewFields = array("Actiu", "EspaiId", "HorariId", "EspaiExternId", "HorariEspaiId", "MaterialId", "SiteId");
        parent::__construct("horarisespais", "HORARIS_ESPAIS", $OldFields, $NewFields );                        

    }
    
    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

    /**
    * Funció que retorna els dies ocupats
    */
    public function getHorarisEspaisOcupats($idEspai, $DiaInicial) {
        require_once BASEDIR."Database/Tables/HorarisModel.php";
        $HM = new HorarisModel();                        
        $PartsDataDiaInicial = explode('-',$DiaInicial);        
        $DiaInicial = $PartsDataDiaInicial[0] . '-' . $PartsDataDiaInicial[1] . '-01';
        $DiaFinal = $PartsDataDiaInicial[0] . '-' . $PartsDataDiaInicial[1] . '-31';
        
        $W = array('espai' => $idEspai, 'diai' => $DiaInicial, 'diaf' => $DiaFinal);
        $SQL = "SELECT {$HM->gsfn('Dia')}, {$HM->gsfn('HoraPre')}, {$HM->gsfn('HoraPost')} 
                  FROM {$HM->getTableName()} 
                  LEFT JOIN {$this->getTableName()}
                    ON {$HM->gofnwt('HorariId')} = {$this->gofnwt('HorariId')}
                 WHERE {$this->gofnwt('Actiu')} = 1 AND {$HM->gofnwt('Actiu')} = 1 AND {$this->gofnwt('EspaiId')} = :espai
                   AND {$HM->gofnwt('Dia')} > :diai AND {$HM->gofnwt('Dia')} < :diaf
                   GROUP BY {$HM->gnfnwt('Dia')}, {$HM->gnfnwt('HoraPre')}, {$HM->gnfnwt('HoraPost')}
                 ";                                  
        
        return $this->runQuery($SQL, array_merge( $W ) );        
    }    
}

?>