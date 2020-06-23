<?php

require_once BASEDIR."Database/DB.php";


class MatriculesModel extends BDD {

    const ESTAT_ACCEPTAT_PAGAT      = "8";
    const ESTAT_ACCEPTAT_NO_PAGAT   = "12";
    const ESTAT_RESERVAT            = "26";
    const ESTAT_EN_ESPERA           = "14";   
    const ESTAT_ERROR               = "10";
    const ESTAT_BAIXA               = "9";
    const ESTAT_EN_PROCES           = "25";
    const ESTAT_DEVOLUCIO           = '11';
        
    const REDUCCIO_CAP              = '16';
    const REDUCCIO_MENOR_25_ANYS    = '18';
    const REDUCCIO_JUBILAT          = '17';
    const REDUCCIO_ATURAT           = '19';
    const REDUCCIO_GRATUIT          = '24';
    const REDUCCIO_ESPECIAL         = '28';
    
    const PAGAMENT_METALIC          = '21';
    const PAGAMENT_TARGETA          = '20';
    const PAGAMENT_TELEFON          = '23';
    const PAGAMENT_TRANSFERENCIA    = '24';
    const PAGAMENT_DOMICILIACIO     = '33';
    const PAGAMENT_CODI_DE_BARRES   = '34';
    const PAGAMENT_RESERVA          = '35';
    const PAGAMENT_LLISTA_ESPERA    = '36';      

    public function __construct() {        
              
        $OldFields = array('idMatricules', 'Usuaris_UsuariID', 'Cursos_idCursos', 'Estat','Comentari', 'DataInscripcio', 'data_baixa', 'Pagat', 'tReduccio', 'tPagament', 'site_id', 'actiu', 'tpv_operacio', 'tpv_order', 'idDadesBancaries', 'tutor_dni','tutor_nom', 'Data_pagament', 'rebut');
        $NewFields = array('IdMatricula', 'UsuariId', 'CursId', 'Estat','Comentari', 'DataInscripcio', 'DataBaixa', 'Pagat', 'TipusReduccio', 'TipusPagament', 'SiteId', 'Actiu', 'TpvOperacio', 'TpvOrder', 'DadesBancariesId', 'TutorDni','TutorNom', 'DataPagament', 'Rebut');
        parent::__construct("matricules", "MATRICULES", $OldFields, $NewFields );

    }

    public function getEmptyObject($UsuariId, $CursId, $SiteId) {
        $O = $this->getDefaultObject();   
        $O[$this->gnfnwt('UsuariId')] = $UsuariId;      
        $O[$this->gnfnwt('CursId')] = $CursId; 
        $O[$this->gnfnwt('Estat')] = self::ESTAT_EN_PROCES; // Estats 
        $O[$this->gnfnwt('DataInscripcio')] = date('Y-m-d H:i', time());        
        $O[$this->gnfnwt('TipusReduccio')] = REDUCCIO_CAP; 
        $O[$this->gnfnwt('Pagat')] = 0; 
        $O[$this->gnfnwt('TipusPagament')] = self::PAGAMENT_RESERVA; 
        $O[$this->gnfnwt('SiteId')] = $SiteId; 
        return $O;
    }

    public function doInsert($ObjecteMatricula) {
        return $this->_doInsert($ObjecteMatricula);        
    }

    public function getMatriculaById($idMatricula) {
        return $this->_getRowWhere( array( $this->gofnwt('IdMatricula') => $idMatricula ) );        
    }

    public function getUsuariHasMatricula($idC, $idU) {
        $W = array();
        $W[ $this->gofnwt('CursId') ] = $idC;
        $W[ $this->gofnwt('UsuariId') ] = $idU;
        $W[ $this->gofnwt('Actiu') ] = 1;        
        return sizeof($this->_getRowWhere( $W , true )) > 0;
    }

    public function getQuantesMatriculesHiHa($idC) { 
        $W = array();
        $W[ $this->gofnwt('CursId') ] = $idC;
        $W[ $this->gofnwt('Estat') ] = array(self::ESTAT_ACCEPTAT_PAGAT, self::ESTAT_ACCEPTAT_NO_PAGAT, self::ESTAT_RESERVAT, self::ESTAT_EN_ESPERA);
        $W[ $this->gofnwt('Actiu') ] = 1;        
        return sizeof($this->_getRowWhere( $W , true ));
    }
}

?>