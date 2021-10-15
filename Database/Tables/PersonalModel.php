<?php 

require_once BASEDIR."Database/DB.php";

class PersonalModel extends BDD {
    
    const FIELD_idPersonal = "idPersonal";
    const FIELD_idUsuari = "idUsuari";
    const FIELD_idData = "idData";
    const FIELD_Tipus = "Tipus";
    const FIELD_Text = "Text";
    const FIELD_DataRevisio = "DataRevisio";
    const FIELD_DataAlta = "DataAlta";
    const FIELD_DataBaixa = "DataBaixa";
    const FIELD_UsuariUpdateId = "UsuariUpdateId";
    const FIELD_SiteId = "SiteId";
    const FIELD_Actiu = "Actiu";
    const FIELD_DataFinalitzada = "DataFinalitzada";
    

    public function __construct() {
        
                            
        $OldFields = array("idPersonal", "idUsuari", "idData", "tipus", "text", "data_revisio",	"data_alta", "data_baixa", "usuariUpdateId", "site_id", "actiu", "data_finalitzada");
        $NewFields = array( self::FIELD_idPersonal,	self::FIELD_idUsuari,	self::FIELD_idData,	self::FIELD_Tipus,	self::FIELD_Text,	self::FIELD_DataRevisio,	self::FIELD_DataAlta,	self::FIELD_DataBaixa,	self::FIELD_UsuariUpdateId,	self::FIELD_SiteId,	self::FIELD_Actiu,	self::FIELD_DataFinalitzada );
        parent::__construct("personal", "PERSONAL", $OldFields, $NewFields );        
        
    }

    public function doUpdate($PersonalDetall) {        
        return $this->_doUpdate($PersonalDetall, array('idPersonal'));        
    }

    public function doDelete($PersonalDetall) {                
        $PersonalDetall[$this->getNewFieldNameWithTable('Actiu')] = 0;        
        return $this->doUpdate($PersonalDetall);        
    }

    public function getFestiusUsuariSite($idUsuari, $idSite, $idAny) {
                    
        $SQL = "
                Select {$this->getSelectFieldsNames()}
                from {$this->getTableName()}                 
                where 
                         {$this->gofnwt(self::FIELD_idUsuari)} = :idUsuari
                AND      {$this->gofnwt(self::FIELD_SiteId)} = :idSite                
                AND      {$this->gofnwt(self::FIELD_idData)} like :idAny
                ORDER BY {$this->getOldFieldNameWithTable('DataInici')} asc
            ";
    
        $SQLW = array('idUsuari'=> $idUsuari, 'idSite' => $idSite, 'idAny' => $idAny );        
                    
        return $this->runQuery($SQL, $SQLW);
            
    }

}

?>