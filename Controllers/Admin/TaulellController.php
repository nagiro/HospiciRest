<?php 

require_once BASEDIR.'Database/Tables/MissatgesModel.php';
require_once BASEDIR."Database/Tables/RespostesModel.php";
require_once BASEDIR.'Controllers/DateController.php';

class TaulellController
{
    private $MissatgesModel;
    private $RespostesModel; 

    public function __construct() {
        $this->MissatgesModel = new MissatgesModel();
        $this->RespostesModel = new RespostesModel();        
    }    

    public function getById($idMissatge = 0, $idUsuari = 0, $WithRespostes = true) {        
        $MISSATGE = $this->MissatgesModel->getById($idMissatge);
        if($MISSATGE['Missatges_UsuariId'] == $idUsuari) $MISSATGE['GEN_POT_EDITAR'] = true;
        else $MISSATGE['GEN_POT_EDITAR'] = false;        
        
        $RESPOSTES = array();        
        if($WithRespostes) {            
            $RESPOSTES = $this->RespostesModel->getFromMissatge($idMissatge);            
            foreach($RESPOSTES as $K=>$R): 
                $RESPOSTES[$K]['GEN_POT_EDITAR'] = ( $R['Respostes_UsuariId'] ==  $idUsuari);
            endforeach;
        }

        return array('MISSATGE'=>$MISSATGE, 'RESPOSTES'=>$RESPOSTES);
        
        
    }
    
    public function getLlistaMissatges($idS, $paraula, $limitCerca) {
        
        $T = $this->MissatgesModel->getLlistaMissatges($idS, $paraula, $limitCerca);
        $RET = array();

        //Agafo els missatges i els ordeno per dates
        foreach($T as $Row) {            
            $RET[DateController::getDataLlarga($Row['Missatges_Publicacio'])][] = $Row;
        }

        $RET2 = array();
        foreach($RET as $K => $R) {            
            $RET2[] = array('Data'=> $K, 'Missatge' => $R);
        }

        return $RET2;        
    }

    public function getNewMissatge($idU, $idS) {                
        return $this->MissatgesModel->getNew($idU, $idS);
    }

    public function getNewResposta($idMissatge, $idUsuari, $idSite) {
        return $this->RespostesModel->getNew($idMissatge, $idUsuari, $idSite);
    }

    public function getRespostesFromMissatge($idMissatge) {
        $RET = $this->RespostesModel->getFromMissatge($idMissatge);        
        return $RET;
    }

    public function doUpdate($MissatgesModel) {
        return $this->MissatgesModel->doUpdate($MissatgesModel);        
    }

    public function doUpdateResposta($RespostaDetall) {
        return $this->RespostesModel->doUpdate($RespostaDetall);        
    }

    public function doDeleteMissatge($MissatgesModel) {
        return $this->MissatgesModel->doDelete($MissatgesModel);        
    }

    public function doDeleteResposta($RespostaModel) {
        return $this->RespostesModel->doDelete($RespostaModel);        
    }

 }

 ?>
