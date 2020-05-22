<?php 

require_once BASEDIR.'Database/Tables/HorarisModel.php';
require_once BASEDIR.'Database/Tables/ActivitatsModel.php';
require_once CONTROLLERSDIR.'FileController.php';

class HorarisController
{
    private $HorarisModel;
    private $ActivitatsModel;

    public function __construct() {
        $this->HorarisModel = new HorarisModel();        
        $this->ActivitatsModel = new ActivitatsModel();        

    }
/*
    public function doUpload($modul, $file, $extensio, $tipus, $idElement, $idU, $idS){

        $FC = new FileController();                
        $WebUrl = "";
        $tipus = strtoupper($tipus);
        
        try {
            $FileName = $FC->doUpload($modul, $file, $extensio, $tipus, $idElement, $idU, $idS);                                
        } catch (Exception $e) { throw $e; } //Propaguem l'excepció

        //Carrego la promoció        
        $Promocio = $this->PromocionsModel->getById($idElement);
        
        //Guardo que la imatge en qüestió està entrada
        $Promocio[$this->PromocionsModel->getNewFieldNameWithTable('IMATGE_'.$tipus)] = $FileName;
        $this->PromocionsModel->doUpdate($Promocio);     

    }

    /**
     * $tipus és la mida de la imatge 's', m, l
     *  */
/*    
    public function doUploadDelete($tipus, $idElement, $idU, $idS){
                                
        //Carrego la promoció        
        $Promocio = $this->PromocionsModel->getById($idElement);
        
        //Indico la nova URL de la imatge
        if($tipus == 's' || $tipus == 'l' || $tipus == 'm'){
            $Promocio[$this->PromocionsModel->getNewFieldNameWithTable('IMATGE_'.strtoupper($tipus))] = '';
            $this->PromocionsModel->doUpdate($Promocio);     
        }

    }

    public function getById($idPromocio = 0) {        
        return $this->PromocionsModel->getById($idPromocio);                
    }
*/        

    public function GeneroCalendari($Di, $Df) {
        // 0 => Year, 1 => Month , 2 => Day
        $CAL = array();
        $DiA = explode("-", $Di);
        $DfA = explode("-", $Df);

        $any = $DiA[0];
        for($mes = intval($DiA[1]); ($mes <= intval($DfA[1]) && $any <= $DfA[0]); $mes++ ) {
            if($mes == 13) { $any++; $mes = 1; }
            $DiesAlMes = cal_days_in_month( CAL_GREGORIAN, $mes, $any );    
            $Dia1DeLaSetmana = date('w', mktime(0,0,0, $mes, 1, $any));
            $Dia1DeLaSetmana = ($Dia1DeLaSetmana == 0) ? 7 : $Dia1DeLaSetmana;
            $Dia1DeLaSetmana = 2 - $Dia1DeLaSetmana;

            for($dia = $Dia1DeLaSetmana; $dia <= $DiesAlMes; $dia++) {                
                
                //Si el primer dia no és 1 ( dilluns ) omplim els que faltin.                 

                $time = mktime(0,0,0, $mes, ( $dia < 1 ) ? 1 : $dia, $any);
                $IndexMes = $any.$mes;                
                $IndexSetmana = date('W', $time);
                $IndexDia = $dia;
                $DiaSetmana = date('w', $time );
                

                if(!isset($CAL[ $IndexMes ])) $CAL[ $IndexMes ] = array();
                if(!isset($CAL[ $IndexMes ][ $IndexSetmana ])) $CAL[$IndexMes][ $IndexSetmana ] = array();
                if(!isset($CAL[ $IndexMes ][ $IndexSetmana ][ $dia ])) $CAL[ $IndexMes ][ $IndexSetmana ][ $dia ] = array();
                $Propietats = array('DIA_SETMANA' => $DiaSetmana, 'DIA' => $any.'-'.$mes.'-'.$dia);
                $CAL[ $IndexMes ][ $IndexSetmana ][ $dia ]['PROPIETATS'] = $Propietats;                
            }
        }

        $CALA = array();
        foreach($CAL as $IndexMes => $V) {
            $ArrayMes = array();
            foreach($V as $IndexSetmana => $V2) {
                $ArraySetmana = array();
                foreach($V2 as $Dia => $V3) {
                    $ArraySetmana[] = array('Dia' => $Dia, 'Propietats' => $V3['PROPIETATS']);                    
                }
                $ArrayMes[] = array('Setmana' => $IndexSetmana, 'D' => $ArraySetmana);
            }            
            $CALA[] = array('AnyMes' => $IndexMes, 'D' => $ArrayMes);
        }        
        
        return $CALA;
        
    }

    // Ha de retornar, cada dia, de cada mes de cada any, tant si hi ha com si no, què hi ha i quin tipus de dia és
    public function getLlistaHoraris($idS, $paraules, $DataInicial, $DataFinal) {
                
        $CAL = $this->GeneroCalendari($DataInicial, $DataFinal);        

        $RET = array();
        $ROWS = $this->ActivitatsModel->getLlistatActivitatsCalendari( $idS, $paraules, $DataInicial, $DataFinal );
        foreach($ROWS as $R ) {
            
            $I = $this->HorarisModel->getNewFieldNameWithTable('Dia');            
            $E = explode("-" , $R[ $I ]); $E[1] = intval($E[1]); $E[2] = intval($E[2]);
            $Index = implode('-', $E);
            if(!isset( $RET[ $Index ] )) $RET[ $Index ] = array();
            else $RET[ $Index ][] = $R;
        }
                        
        return array('CAL'=>$CAL, 'HORARIS' => $RET);
    }
/*
    public function getPromocionsActives($idS) {
        $RET = $this->PromocionsModel->getPromocionsActives($idS);        
    }

    public function getNewPromocio($idS) {                
        return $this->PromocionsModel->getNew();
    }

    /**
     * Fem un update de totes les promocions amb el nou ordre
     * Enviem un array de Id => Ordre actual
     */    
/*    
    public function doOrderChange($PromocionsArray) {        
        
        foreach(json_decode($PromocionsArray, true) as $PromocioObject) {
            $this->PromocionsModel->doUpdate($PromocioObject);    
        }
        
    }

    public function doUpdate($PromocionsModel) {
        return $this->PromocionsModel->doUpdate($PromocionsModel);        
    }
    
    public function doDelete($PromocionsModel) {
        return $this->PromocionsModel->doDelete($PromocionsModel);        
    }

    */
 }

 ?>
