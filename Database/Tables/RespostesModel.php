<?php 

require_once BASEDIR."Database/DB.php";
require_once BASEDIR."Database/Tables/UsuarisModel.php";

class RespostesModel extends BDD {

    public function __construct() {

        $OldFields = array("idResposta",    "idPare",   "IdUsuari", "Tipus",    "Data", "Text", "idSite",   "Actiu");    
        $NewFields = array("RespostaId",    "PareId",   "UsuariId", "Tipus",    "Data", "Text", "SiteId",   "Actiu");
        parent::__construct("respostes", "Respostes", $OldFields, $NewFields );
            
    }        

    public function getById($idResposta) {

        return $this->runQuery("Select ".$this->getSelectFieldsNames().
                        " from ".$this->getTableName().
                        " where {$this->getOldFieldNameWithTable("RespostaId")} = :id", 
                    array('id'=>$idResposta), true);

    }

    public function getFromMissatge($idMissatge) {
        
        if($idMissatge > 0) {
        
            $UM = new UsuarisModel();
            return $this->runQuery("Select 
                            {$this->getSelectFieldsNames()},
                            {$UM->getNomCompletFields()}
                            from {$this->getTableName()} LEFT JOIN {$UM->getTableName()} ON ({$this->getOldFieldNameWithTable('UsuariId')} = {$UM->getOldFieldNameWithTable('IdUsuari')})
                            where {$this->getOldFieldNameWithTable("PareId")} = :id
                              AND {$this->getOldFieldNameWithTable("Actiu")} = 1
                            order by {$this->getOldFieldNameWithTable("Data")} desc
                        ", 
                        array('id'=>$idMissatge), false);

        } else {

            return array();

        }

    }

    public function doUpdate($RespostaDetall) {        
        return $this->_doUpdate($RespostaDetall, array('RespostaId'));        
    }
    
    public function getSQLQuantesRespostes($NomCampAltraConsulta) {

        $SQL = "
            (Select count(*)
            from {$this->getOldTableName()}
            Where {$this->getOldFieldNameWithTable('PareId')} = {$NomCampAltraConsulta}
              AND {$this->getOldFieldNameWithTable('Actiu')} = 1
            ) as {$this->getNewFieldNameWithTable('QuantsMissatges', false)}
        ";

        return $SQL;

    }

    public function getNew($idMissatge, $idUsuari, $idSite) {                          
        $D = date('Y-m-d H:i:s', time());
        $SQL = "
                INSERT INTO {$this->getOldTableName()} 
                (
                    {$this->getOldFieldNameWithTable('Text')},
                    {$this->getOldFieldNameWithTable('PareId')},
                    {$this->getOldFieldNameWithTable('UsuariId')},
                    {$this->getOldFieldNameWithTable('Actiu')},
                    {$this->getOldFieldNameWithTable('Data')},
                    {$this->getOldFieldNameWithTable('SiteId')}
                
                ) VALUES ('', {$idMissatge}, {$idUsuari}, 1, '{$D}', {$idSite}) 
            ";
        
        $lastId= $this->runQuery($SQL, array(), false, false, 'A');                
        return $this->getById($lastId);
    }


    public function doDelete($RespostaModel) {                
        $RespostaModel[$this->getNewFieldNameWithTable('Actiu')] = 0;                
        return $this->doUpdate($RespostaModel);        
    }


}

?>