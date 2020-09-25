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
/*        
    const REDUCCIO_CAP              = '16';
    const REDUCCIO_MENOR_25_ANYS    = '18';
    const REDUCCIO_JUBILAT          = '17';
    const REDUCCIO_ATURAT           = '19';
    const REDUCCIO_GRATUIT          = '24';
    const REDUCCIO_ESPECIAL         = '28';
  */  
    const PAGAMENT_CAP              = '0';
    const PAGAMENT_METALIC          = '21';
    const PAGAMENT_DATAFON          = '61';     //Nou pagament amb targeta i TPV físic
    const PAGAMENT_TARGETA          = '20';
    const PAGAMENT_TELEFON          = '23';
    const PAGAMENT_TRANSFERENCIA    = '24';
    const PAGAMENT_DOMICILIACIO     = '33';
    const PAGAMENT_CODI_DE_BARRES   = '34';
    const PAGAMENT_RESERVA          = '35';
    const PAGAMENT_LLISTA_ESPERA    = '36';
    const PAGAMENT_INVITACIO        = '60';      

    public function __construct() {        
              
        $OldFields = array('idMatricules', 'Usuaris_UsuariID', 'Cursos_idCursos', 'Estat','Comentari', 'DataInscripcio', 'data_baixa', 'Pagat', 'tReduccio', 'tPagament', 'site_id', 'actiu', 'tpv_operacio', 'tpv_order', 'idDadesBancaries', 'tutor_dni','tutor_nom', 'Data_pagament', 'data_hora_entrada', 'rebut', 'GrupMatricules', 'Fila', 'Seient');
        $NewFields = array('IdMatricula', 'UsuariId', 'CursId', 'Estat','Comentari', 'DataInscripcio', 'DataBaixa', 'Pagat', 'TipusReduccio', 'TipusPagament', 'SiteId', 'Actiu', 'TpvOperacio', 'TpvOrder', 'DadesBancariesId', 'TutorDni','TutorNom', 'DataPagament', 'DataHoraEntrada', 'Rebut', 'GrupMatricules', 'Fila', 'Seient');
        parent::__construct("matricules", "MATRICULES", $OldFields, $NewFields );

    }

    /**
     * Quan entra un pagament determinat, en quin estat es posa la matrícula
     * $OM: Objecte Matrícula
     * Pagament: Tipus de pagament escollit
     * @return OM: Objecte Matrícula
     */
    public function setEstatByTipusPagament($OM) {

        switch($OM[$this->gnfnwt('TipusPagament')]) {
            case self::PAGAMENT_INVITACIO:      $OM[$this->gnfnwt('Estat')] = self::ESTAT_RESERVAT; break;
            case self::PAGAMENT_METALIC:        $OM[$this->gnfnwt('Estat')] = self::ESTAT_ACCEPTAT_PAGAT; break;
            case self::PAGAMENT_DATAFON:        $OM[$this->gnfnwt('Estat')] = self::ESTAT_EN_PROCES; break;
            case self::PAGAMENT_TARGETA:        $OM[$this->gnfnwt('Estat')] = self::ESTAT_EN_PROCES; break;
            case self::PAGAMENT_TELEFON:        $OM[$this->gnfnwt('Estat')] = self::ESTAT_ACCEPTAT_NO_PAGAT; break;
            case self::PAGAMENT_TRANSFERENCIA:  $OM[$this->gnfnwt('Estat')] = self::ESTAT_ACCEPTAT_NO_PAGAT; break;
            case self::PAGAMENT_DOMICILIACIO:   $OM[$this->gnfnwt('Estat')] = self::ESTAT_ACCEPTAT_NO_PAGAT; break;
            case self::PAGAMENT_CODI_DE_BARRES: $OM[$this->gnfnwt('Estat')] = self::ESTAT_ACCEPTAT_NO_PAGAT; break;
            case self::PAGAMENT_RESERVA:        $OM[$this->gnfnwt('Estat')] = self::ESTAT_RESERVAT; break;
            case self::PAGAMENT_LLISTA_ESPERA:  $OM[$this->gnfnwt('Estat')] = self::ESTAT_EN_ESPERA; break;
        }

        return $OM;
    }

    public function isMatriculaEstatOk($OM) {
        if( $OM[$this->gnfnwt('Estat')] == self::ESTAT_EN_ESPERA
            || $OM[$this->gnfnwt('Estat')] == self::ESTAT_ERROR
            || $OM[$this->gnfnwt('Estat')] == self::ESTAT_BAIXA
            || $OM[$this->gnfnwt('Estat')] == self::ESTAT_EN_PROCES
            || $OM[$this->gnfnwt('Estat')] == self::ESTAT_DEVOLUCIO
        ) { return false; }
        else return true;
    }

    /**
     * Retorna un text de l'estat de la matrícula
     */
    public function getEstatString($OM) {
        switch($OM[$this->gnfnwt('Estat')]) {
            case self::ESTAT_ACCEPTAT_PAGAT: return 'Acceptat i pagat'; break;
            case self::ESTAT_ACCEPTAT_NO_PAGAT: return 'Acceptat i no pagat'; break;
            case self::ESTAT_RESERVAT: return 'Reservat'; break;
            case self::ESTAT_EN_ESPERA: return 'En espera'; break;
            case self::ESTAT_ERROR: return 'Error'; break;
            case self::ESTAT_BAIXA: return 'Baixa'; break;
            case self::ESTAT_EN_PROCES: return 'En procès de pagament'; break;
            case self::ESTAT_DEVOLUCIO: return 'Devolució'; break;
            default: return 'n/d'; break;
        }
    }    

    /**
     * Aplico el preu que requereix la matrícula
     * $OM: Objecte Matrícula
     * Preu: Preu base del curs
     * @return OM: Objecte Matrícula
     */
    public function setPreuMatricula($OM, $Preu) {
        
        //Falta posar el tema descomptes
        $OM[$this->gnfnwt('Pagat')] = $Preu;

        return $OM; 
    }    

    public function getEmptyObject($UsuariId, $CursId, $SiteId) {
        $O = $this->getDefaultObject();   
        $O[$this->gnfnwt('UsuariId')] = $UsuariId;      
        $O[$this->gnfnwt('CursId')] = $CursId; 
        $O[$this->gnfnwt('Estat')] = self::ESTAT_EN_PROCES; // Estats 
        $O[$this->gnfnwt('DataInscripcio')] = date('Y-m-d H:i', time());        
        $O[$this->gnfnwt('TipusReduccio')] = -1; 
        $O[$this->gnfnwt('Pagat')] = 0; 
        $O[$this->gnfnwt('TipusPagament')] = self::PAGAMENT_RESERVA; 
        $O[$this->gnfnwt('SiteId')] = $SiteId;         
        $O[$this->gnfnwt('DataBaixa')] = NULL;
        $O[$this->gnfnwt('Comentari')] = NULL;
        $O[$this->gnfnwt('TpvOperacio')] = NULL;
        $O[$this->gnfnwt('TpvOrder')] = NULL;
        $O[$this->gnfnwt('DadesBancariesId')] = NULL;
        $O[$this->gnfnwt('TutorDni')] = NULL;
        $O[$this->gnfnwt('TutorNom')] = NULL;
        $O[$this->gnfnwt('DataPagament')] = NULL;
        $O[$this->gnfnwt('DataHoraEntrada')] = NULL;        
        $O[$this->gnfnwt('Rebut')] = NULL;        
        $O[$this->gnfnwt('Fila')] = NULL;        
        $O[$this->gnfnwt('Seient')] = NULL;        
        return $O;
    }
    
    public function doInsert($ObjecteMatricula) {
        return $this->_doInsert($ObjecteMatricula);        
    }

    public function IsMatriculaCorrectaPerImprimirResguard($ObjecteMatricula) {
    
        return (    $ObjecteMatricula[$this->gnfnwt('Estat')] == self::ESTAT_ACCEPTAT_PAGAT
                ||  $ObjecteMatricula[$this->gnfnwt('Estat')] == self::ESTAT_ACCEPTAT_NO_PAGAT
                ||  $ObjecteMatricula[$this->gnfnwt('Estat')] == self::ESTAT_RESERVAT
                ||  $ObjecteMatricula[$this->gnfnwt('Estat')] == self::ESTAT_EN_ESPERA
            );
    }    

    public function getIdMatriculaGrup($idMatricula) {
                        
        $OM = $this->getMatriculaById($idMatricula);
        if(!empty($OM)) { return $OM[ $this->gnfnwt('GrupMatricules') ]; }
        else throw new Exception('No he trobat la matrícula grup de : ' . $idMatricula);
                
    }


    /**
     * A partir d'un ID de matrícjula, retorna les que tenen el mateix GRUPMATRICULES que ella
     * idMatricula: Number
     */
    public function getMatriculesVinculades($idMatricula, $ReturnOnlyId = true ) {
        
        $RETURN = array();

        // Carrego la primera matrícula. 
        $OM = $this->getMatriculaById($idMatricula);

        if(!empty($OM)) {
            $idGrupMatricules = $OM[ $this->gnfnwt('GrupMatricules') ];            
            $Matricules_GrupMatricules = $this->_getRowWhere( array( $this->gofnwt('GrupMatricules') => $idGrupMatricules ), true );        
            foreach($Matricules_GrupMatricules as $M) {                
                if($ReturnOnlyId) $RETURN[] = $this->getId($M);
                else $RETURN[] = $M;                
            }
        }
        
        return $RETURN;        

    }

    /**
     * Retorna un IDmatricula
     * OM: Object Matrícula
     */
    public function getId($OM) {        
        if( isset( $OM[ $this->gnfnwt('IdMatricula') ] ) ) return $OM[ $this->gnfnwt('IdMatricula') ]; 
        else throw new Exception('No hi ha cap codi de matrícula'); 
    }

    public function getMatriculaById($idMatricula) {
        return $this->_getRowWhere( array( $this->gofnwt('IdMatricula') => $idMatricula ) );        
    }

    public function getMatriculesByCurs($idCurs) {
        return $this->_getRowWhere( 
            array( 
                $this->gofnwt('CursId') => $idCurs,
                $this->gofnwt('Actiu') => 1,
                $this->gofnwt('Estat') => array(self::ESTAT_ACCEPTAT_PAGAT, self::ESTAT_ACCEPTAT_NO_PAGAT, self::ESTAT_RESERVAT, self::ESTAT_EN_ESPERA)
            ) , true );        
    }

    public function getUsuariHasMatricula($idC, $idU) {
        $W = array();
        $W[ $this->gofnwt('CursId') ] = $idC;
        $W[ $this->gofnwt('UsuariId') ] = $idU;
        $W[ $this->gofnwt('Actiu') ] = 1;   
        $W[ $this->gofnwt('Estat')] = array(self::ESTAT_ACCEPTAT_PAGAT, self::ESTAT_ACCEPTAT_NO_PAGAT, self::ESTAT_RESERVAT, self::ESTAT_EN_ESPERA);
        return sizeof($this->_getRowWhere( $W , true )) > 0;
    }

    public function getQuantesMatriculesHiHa($idC) { 
        $W = array();
        $W[ $this->gofnwt('CursId') ] = $idC;
        $W[ $this->gofnwt('Estat') ] = array(self::ESTAT_ACCEPTAT_PAGAT, self::ESTAT_ACCEPTAT_NO_PAGAT, self::ESTAT_RESERVAT, self::ESTAT_EN_ESPERA);
        $W[ $this->gofnwt('Actiu') ] = 1;        
        return sizeof($this->_getRowWhere( $W , true ));
    }
    
    public function setGrupMatricula($OM, $idMatricula) {
        $OM[$this->gnfnwt('GrupMatricules')] = $idMatricula;
        return $OM;
    }

    public function updateMatricula($OM) {                
        $this->_doUpdate($OM, array('IdMatricula'));
    }

    public function getUserEmail($OM) {
        $MM = new UsuarisModel();
        $OU = $MM->getUsuariId( $OM[$this->gnfnwt('UsuariId')] );
        return $MM->getEmail($OU);
    }


    public function getDescompteString($MatriculaObject) {        
        require_once BASEDIR."Database/Tables/DescomptesModel.php";
        $DM = new DescomptesModel();

        if( $MatriculaObject[$this->gnfnwt('TipusReduccio')] > -1) {
            $DetallDescompteAplicat = $DM->getDescompteById( $MatriculaObject[$this->gnfnwt('TipusReduccio')] );
            return $DetallDescompteAplicat[$DM->gnfnwt('Nom')];
        } else {
            return 'Cap';
        }
        
    }

    public function setLocalitat($ObjecteMatricula, $ArrayLocalitatFilaColumna) {
        $ObjecteMatricula[$this->gnfnwt('Fila')] = $ArrayLocalitatFilaColumna[0];
        $ObjecteMatricula[$this->gnfnwt('Seient')] = $ArrayLocalitatFilaColumna[1];
        return $ObjecteMatricula;
    }

    public function getLocalitatArray($ObjecteMatricula) {
        return array( $ObjecteMatricula[$this->gnfnwt('Fila')], $ObjecteMatricula[$this->gnfnwt('Seient')] );
    }
    public function getLocalitatString($ObjecteMatricula) {
        return "Fila: " . $ObjecteMatricula[$this->gnfnwt('Fila')]. " | Seient: " . $ObjecteMatricula[$this->gnfnwt('Seient')];
    }


        /**
    * $Localitats = array (fila, seient)
    **/
    public function hasSeientsSonLliures($Localitats, $idCurs) {
        
        if( sizeof($Localitats) > 0 ) {
            $W = array();
            foreach($Localitats as $L) {
                $W[] = "({$this->gofnwt('Fila')} = {$L[0]} AND {$this->gofnwt('Seient')} = {$L[1]})";
            }

            $SQL = "
                Select count(*)
                from {$this->getTableName()}             
                where 
                        {$this->getOldFieldNameWithTable('CursId')} = {$idCurs}
                AND     {$this->getOldFieldNameWithTable('Actiu')} = 1         
                AND  (" . implode(" OR ", $W).')';

            $QuantsNiHa = $this->runQuery($SQL, array());
                
            return ($QuantsNiHa[0]['count(*)'] == 0);
        } else {
            return true;
        }


    }

    public function getSiteValue($OM){
        return $OM[$this->gnfnwt('SiteId')];
    }

    /**
     * Validem que el QR sigui correcte i la matrícula existeixi
     */
    public function validaQR($idMatricula) {
        
        $RET = array('estat' => false, 'error' => '');

        // Si la matrícula és false, vol dir que no hem pogut desencriptar-la
        if($idMatricula) {

            $OMatricula = $this->getMatriculaById($idMatricula);
            if(!empty($OMatricula)) {

                $isCodiCorrecte = (!empty($OMatricula) && $OMatricula[$this->gnfnwt('IdMatricula')] == $idMatricula);        
                $isMatriculaEstatCorrecte = $this->isMatriculaEstatOk($OMatricula);
                $isMatriculaNoEntrada = strlen($OMatricula[$this->gnfnwt('DataHoraEntrada')]) == 0;
                
                if( $isCodiCorrecte && $isMatriculaEstatCorrecte && $isMatriculaNoEntrada ) {            
                    $OMatricula[$this->gnfnwt('DataHoraEntrada')] = date('Y-m-d H:i:s',time());
                    $this->updateMatricula($OMatricula);                                    
                    $RET['estat'] = true;
                    return $RET;
                } else {
                    if(!$isCodiCorrecte) $RET['error'] = 'Numero matrícula incorrecte';
                    if(!$isMatriculaEstatCorrecte) $RET['error'] = "Estat incorrecte";
                    if(!$isMatriculaNoEntrada) $RET['error'] = "Codi repetit";
                    return $RET;
                }
            } else {
                $RET['error'] = 'Codi inexistent';
                return $RET;
            }

        } else {
            $RET['error'] = 'Codi incorrecte';
            return $RET;
        }
        
    }

}

?>