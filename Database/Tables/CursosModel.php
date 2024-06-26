<?php

require_once BASEDIR."Database/DB.php";


class CursosModel extends BDD {

    const RESTRINGIT_EXCEL        = "1";
    const RESTRINGIT_NOMES_UNA    = "2";    
    const RESTRINGIT_NOMES_UN_COP = "3";    

    const FIELD_IdCurs = "IdCurs";
    const FIELD_TitolCurs = "TitolCurs";
    const FIELD_IsActiu = "IsActiu";
    const FIELD_Places = "Places";
    const FIELD_Codi = "Codi";
    const FIELD_Descripcio = "Descripcio";
    const FIELD_Preu = "Preu";
    const FIELD_Horaris = "Horaris";
    const FIELD_Categoria = "Categoria";
    const FIELD_OrdreSortida = "OrdreSortida";
    const FIELD_DataAparicio = "DataAparicio";
    const FIELD_DataDesaparicio = "DataDesaparicio";
    const FIELD_DataInMatricula = "DataInMatricula";
    const FIELD_DataFiMatricula = "DataFiMatricula";
    const FIELD_DataInici = "DataInici";
    const FIELD_VisibleWeb = "VisibleWeb";
    const FIELD_SiteId = "SiteId";
    const FIELD_Actiu = "Actiu";
    const FIELD_CicleId = "CicleId";
    const FIELD_ActivitatId = "ActivitatId";
    const FIELD_Pdf = "Pdf";
    const FIELD_ADescomptes = "ADescomptes";
    const FIELD_PagamentExtern = "PagamentExtern";
    const FIELD_PagamentIntern = "PagamentIntern";
    const FIELD_IsRestringit = "IsRestringit";
    const FIELD_DadesExtres = "DadesExtres";
    const FIELD_Teatre = "Teatre";


    public function __construct() {
        
        $OldFields = array('idCursos', 'TitolCurs', 'isActiu', 'Places',  'Codi', 'Descripcio','Preu', 'Horaris' ,'Categoria', 'OrdreSortida', 'DataAparicio', 'DataDesaparicio', 'DataInMatricula', 'DataFiMatricula', 'DataInici', 'VisibleWEB', 'site_id', 'actiu', 'cicle_id', 'activitat_id' ,'PDF', 'ADescomptes' ,'PagamentExtern' ,'PagamentIntern' ,'isRestringit' ,'DadesExtres', 'Teatre');
        $NewFields = array(self::FIELD_IdCurs,	self::FIELD_TitolCurs,	self::FIELD_IsActiu,	self::FIELD_Places,	self::FIELD_Codi,	self::FIELD_Descripcio,	self::FIELD_Preu,	self::FIELD_Horaris,	self::FIELD_Categoria,	self::FIELD_OrdreSortida,	self::FIELD_DataAparicio,	self::FIELD_DataDesaparicio,	self::FIELD_DataInMatricula,	self::FIELD_DataFiMatricula,	self::FIELD_DataInici,	self::FIELD_VisibleWeb,	self::FIELD_SiteId,	self::FIELD_Actiu,	self::FIELD_CicleId,	self::FIELD_ActivitatId,	self::FIELD_Pdf,	self::FIELD_ADescomptes,	self::FIELD_PagamentExtern,	self::FIELD_PagamentIntern,	self::FIELD_IsRestringit,	self::FIELD_DadesExtres,	self::FIELD_Teatre );        
        parent::__construct("cursos", "CURSOS", $OldFields, $NewFields );

    }

    /**
     * Funció que retorna si a un curs se li aplica una restricció específica
     */
    public function getIsRestringit($OCurs, $QuinaRestriccio) {
        return ( stripos( $OCurs[$this->gnfnwt('IsRestringit')] , $QuinaRestriccio ) !== false );        
    }

    public function getEmptyObject($SiteId) {
        $OC = array();
        
        foreach($this->NewFieldsWithTableArray as $K => $V) {
            $OC[$V] = '';
        }
        $OC[$this->getNewFieldNameWithTable('IdNivell')] = 2;
        $OC[$this->getNewFieldNameWithTable('Actualitzacio')] = date('Y-m-d', time());
        $OC[$this->getNewFieldNameWithTable('Actiu')] = 1;
        $OC[$this->getNewFieldNameWithTable('SiteId')] = $SiteId;
        $OC[$this->getNewFieldNameWithTable('Teatre')] = null;        
        return $OC;
    }

    public function getCursId($OC) { return $OC[$this->gnfnwt('IdCurs')]; }
    public function getCursById($idCurs) { return $this->_getRowWhere( array( $this->gofnwt('IdCurs') => intval($idCurs), $this->gofnwt('IsActiu') => '1' ) ); }    
    public function getRowCicleId($CicleId) { return $this->_getRowWhere( array( $this->gofnwt('CicleId') => intval($CicleId)) ); }
    public function getRowActivitatId($ActivitatId) { return $this->_getRowWhere( array( $this->gofnwt('ActivitatId') => intval($ActivitatId), $this->gofnwt('IsActiu') => '1', $this->gofnwt('Actiu') => '1') ); }    
    
    public function getDescomptes($CursObject) {
        require_once BASEDIR."Database/Tables/DescomptesModel.php";
        $DM = new DescomptesModel();
        $CursBuit = $DM->getEmptyObject($CursObject[$this->gnfnwt('SiteId')]);
        $Descomptes = array( $CursBuit );
        return array_merge( $Descomptes , $DM->getDescomptesByCurs($CursObject[$this->gnfnwt('IdCurs')]) );        
    }    

    public function getPreuAplicantDescompte($CursObject, $idDescompte) {        
        require_once BASEDIR."Database/Tables/DescomptesModel.php";
        $DM = new DescomptesModel();

        $DetallDescompteAplicat = $DM->getDescompteById($idDescompte);
        $PreuCurs = $CursObject[$this->gnfnwt('Preu')];
        
        if( $DetallDescompteAplicat[$DM->gnfnwt('Percentatge')] > 0 ) $PreuCurs = round( $PreuCurs - ($PreuCurs * $DetallDescompteAplicat[$DM->gnfnwt('Percentatge')] / 100), 2); 
        elseif( $DetallDescompteAplicat[$DM->gnfnwt('Preu')] > 0 ) $PreuCurs = $DetallDescompteAplicat[$DM->gnfnwt('Preu')]; 

        return $PreuCurs;
    }

    public function getLlistatCursosWeb($SiteId) { 

        $DataInicial = date('Y-m-d', time());

        $SQL = "
                Select {$this->getSelectFieldsNames()}
                from {$this->getTableName()}                 
                where 
                         {$this->getOldFieldNameWithTable('SiteId')} = :site_id
                AND      {$this->getOldFieldNameWithTable('Actiu')} = 1                
                AND      {$this->getOldFieldNameWithTable('DataFiMatricula')} >= :Data1
--                AND      {$this->getOldFieldNameWithTable('DataInMatricula')} <= :Data2
                AND      {$this->getOldFieldNameWithTable('VisibleWeb')} = 1 ";

        if($SiteId != 5):            
            $SQL .= " ORDER BY {$this->getOldFieldNameWithTable('DataInici')} asc; ";
        else:
            $SQL .= " ORDER BY {$this->getOldFieldNameWithTable(self::FIELD_Categoria)} asc, {$this->getOldFieldNameWithTable(self::FIELD_Codi)} asc; ";
        endif;            

    $SQLW = array('site_id'=>$SiteId, 'Data1' => $DataInicial /*, 'Data2' => $DataInicial */);        
                
        return $this->runQuery($SQL, $SQLW);

    }

    public function getSeientsOcupats($ObjecteCurs) {
        require_once BASEDIR."Database/Tables/MatriculesModel.php";
        $RET = array('QuantesMatricules' => 0, 'Localitats' => array());
        $MM = new MatriculesModel();
        
        if(strlen($ObjecteCurs[$this->gnfnwt('Teatre')]) > 0) {
            $MatriculesLocalitats = $MM->getMatriculesByCurs( $this->getCursId($ObjecteCurs));
            $RET['QuantesMatricules'] = sizeof($MatriculesLocalitats);
            foreach($MatriculesLocalitats as $MatriculesObject) {
                $RET['Localitats'][] = $MM->getLocalitatArray($MatriculesObject);
            }            
        } else {
            $RET['QuantesMatricules'] = $MM->getQuantesMatriculesHiHa( $this->getCursid($ObjecteCurs));
        }
                
        return $RET;
        
    }

    public function hasEscullLocalitats($CursObject) {
        return (strlen($CursObject[$this->gnfnwt('Teatre')]) > 0);
    }    

    /**
    * Funció que carrega el teatre escollit pel curs en qüestió
    **/
    public function getTeatre( $CursObject ) {        
        if( isset($CursObject[$this->gnfnwt('Teatre')]) && strlen($CursObject[$this->gnfnwt('Teatre')]) > 0 ){
            $TeatreJson = file_get_contents( TEATRES . $CursObject[$this->gnfnwt('SiteId')] . '-' . $CursObject[$this->gnfnwt('Teatre')] . '.json' );
            return json_decode($TeatreJson, true);        
        } else {
            return array('Seients' => array());
        }
    }

    public function potMatricularSegonsRestriccio($DNI, $idCurs = 0, $idSite = 1) {

        //Carrego el curs i miro les dades.        
        $OCurs = ($idCurs > 0) ? $this->getCursById($idCurs) : array();        
        $idS = ($idCurs > 0) ? $OCurs[$this->gnfnwt('SiteId')] : $idSite;                
        $EsRestringitExcel = ($idCurs == 0) ? true : $this->getIsRestringit( $OCurs, self::RESTRINGIT_EXCEL );

        $Return = array('IsOk' => false, 'CursosOk' => array());

        $file = AUXDIR . "Restriccions/Notes-{$idS}.csv";                 

        if( $EsRestringitExcel && file_exists($file) ) {

            $ArxiuCSV = fopen($file, "r");
            while (($datos = fgetcsv($ArxiuCSV, 1000, ";","'")) !== FALSE) {                
                if(strtoupper($datos[4]) == strtoupper($DNI)) {                    
                    for($i = 15; $i < 25; $i = $i+2 ) {                        
                        if( !empty($datos[$i+1]) &&  intval($datos[$i+1]) > 0 ) {
                            if( $datos[$i+1] == $this->getCursId($OCurs) ) $Return['IsOk'] = true;                            
                            else $Return['CursosOk'][] = array('id'=> $datos[$i + 1], 'nom'=> $datos[$i]);
                        }
                    }                    
                }
            }
            fclose($ArxiuCSV);

        } elseif( ! $EsRestringitExcel ) { 

            $Return['IsOk'] = true;

        } else { 

            throw new Exception("L'arxiu de restriccions és inexistent. Consulti amb la seva entitat."); 

        }
        
        return $Return;
    }

    public function getTodayCursosAndMatricules( $idSite ) {

        require_once 'MatriculesModel.php';

        $MM = new MatriculesModel();

        $SQL = "
            SELECT c.idCursos, c.TitolCurs, m.idMatricules, m.data_hora_entrada, m.Fila, m.Seient, m.GrupMatricules, u.Nom, u.Cog1, u.Cog2, m.Comentari, m.tPagament, m.Estat
            FROM cursos c LEFT JOIN matricules m ON (c.idCursos = m.Cursos_idCursos)
            LEFT JOIN usuaris u ON (m.Usuaris_UsuariID = u.UsuariID)
            WHERE m.Estat IN {$MM->ReturnEstatsCorrectesSQL()}
            AND c.actiu = 1
            AND m.actiu = 1            
            AND c.DataInici = CURDATE()
            AND c.site_id = :siteid
            ORDER BY c.TitolCurs, u.Cog1, u.Cog2, u.Nom
        ";                            

        return $this->runQuery($SQL, array('siteid' => $idSite ));

    }

    public function getMatriculesByCursAndUserData($idCurs) {

        require_once 'MatriculesModel.php';

        $MM = new MatriculesModel();

        $SQL = "
            SELECT c.idCursos, c.TitolCurs, m.idMatricules, m.data_hora_entrada, m.Fila, m.Seient, m.GrupMatricules, u.Nom, u.Cog1, u.Cog2, u.Email, u.Telefon, m.Comentari, m.tPagament
            FROM cursos c LEFT JOIN matricules m ON (c.idCursos = m.Cursos_idCursos)
            LEFT JOIN usuaris u ON (m.Usuaris_UsuariID = u.UsuariID)
            WHERE m.Estat IN {$MM->ReturnEstatsCorrectesSQL()}
            AND c.actiu = 1
            AND c.idCursos = :idcurs
            AND m.actiu = 1
            ORDER BY c.TitolCurs, u.Cog1, u.Cog2, u.Nom
        ";
        
        return $this->runQuery($SQL, array('idcurs' => $idCurs ));

    }

}

?>