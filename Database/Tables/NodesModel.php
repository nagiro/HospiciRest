<?php 

require_once BASEDIR."Database/DB.php";

class NodesModel extends BDD {


    public function __construct() {

        $OldFields = array("idNodes", "idPare", "TitolMenu", "HTML", "isCategoria", "isPhp", "isActiva", "Ordre", "Nivell", "Url", "Categories", "site_id", "actiu");
        $NewFields = array("idNodes", "idPare", "TitolMenu", "Html", "isCategoria", "isPhp", "isActiva", "Ordre", "Nivell", "Url", "Categories", "idSite", "Actiu");
        parent::__construct("nodes", "Nodes", $OldFields, $NewFields );                        

    }

    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }

    public function getNodesCerca($Paraules) {
        
        $ParaulesComodins = "";
        foreach($Paraules as $P) {
            $ParaulesComodins .= '%'.$P.'%';
        }

        $SQL = "Select {$this->getSelectFieldsNames()}
                from {$this->getTableName()} 
                where {$this->getOldFieldNameWithTable('isActiva')} = 1
                  AND {$this->getOldFieldNameWithTable('idSite')} = 10
                  AND {$this->getOldFieldNameWithTable('Actiu')} = 1
                  AND ( 
                       {$this->getOldFieldNameWithTable('Html')} like :paraula1
                    OR {$this->getOldFieldNameWithTable('TitolMenu')} like :paraula2
                      )                  
                ORDER BY {$this->getOldFieldNameWithTable('Ordre')} desc  
                ";
        
        return $this->runQuery($SQL, array('paraula1' => $ParaulesComodins, 'paraula2' => $ParaulesComodins));
        
    }

    public function getTitolMenu( $ON ) { return $ON[$this->gnfnwt("TitolMenu")]; }
    public function getIdNodes( $ON ) { return $ON[$this->gnfnwt("idNodes")]; }
    public function getIdPare( $ON ) { return $ON[$this->gnfnwt("idPare")]; }
    public function getNivell( $ON ) { return $ON[$this->gnfnwt("Nivell")]; }
    public function getIsPhp( $ON ) { return $ON[$this->gnfnwt("isPhp")]; }
    public function getHtml( $ON ) { return $ON[$this->gnfnwt("Html")]; }

    public function setHtml( $ON, $Value ) { $ON[$this->gnfnwt('Html')] = $Value; return $ON; }

}

?>