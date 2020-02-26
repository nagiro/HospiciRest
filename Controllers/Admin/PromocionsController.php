<?php 

require_once BASEDIR.'Database/Tables/PromocionsModel.php';
require_once CONTROLLERSDIR.'FileController.php';

class PromocionsController
{
    private $PromocionsModel;

    public function __construct() {
        $this->PromocionsModel = new PromocionsModel();
    }
        
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
    
    public function getLlistaPromocions($idS, $paraula, $estat) {
        
        $RET = $this->PromocionsModel->getLlistaPromocions($idS, $paraula, $estat);

        // Fem això perquè les files sempre tinguin un ordre sense repeticions
        $OrderIndex = 1;
        foreach($RET as $Index => $ROW) $RET[$Index]['PROMOCIONS_ORDRE'] = $OrderIndex++;        

        return $RET;        
    }

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

 }

 ?>
