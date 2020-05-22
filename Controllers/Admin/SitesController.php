<?php 

require_once BASEDIR.'Database/Tables/SitesModel.php';
require_once CONTROLLERSDIR.'FileController.php';

class SitesController
{
    private $SitesModel;

    public function __construct() {
        $this->SitesModel = new SitesModel();
    }
        
    public function getById($idSite = 0) {        
        return $this->SitesModel->getById($idSite);                
    }
    
    public function getSitesActius() {                
        $RET = $this->SitesModel->getSitesActius();                
        return $RET;
    }

    public function getNewSite() {                
        return $this->SitesModel->getNew();
    }

    public function doUpdate($SitesModel) {
        return $this->SitesModel->doUpdate($SitesModel);        
    }
    
    public function doDelete($SitesModel) {
        return $this->SitesModel->doDelete($SitesModel);        
    }

 }

 ?>
