<?php 

require_once BASEDIR.'Database/Tables/MenuModel.php';

class MenusController
{
    private $MenuModel;

    public function __construct() {
        $this->MenuModel = new MenuModel();
    }
        
/*
    public function consulta() {}
    public function edita() {}
    public function actualitza() {}
    public function esborra() {}
*/

    public function getMenusByUser($IdUsuari, $IdSite) {
        // Consultem els menus que hi ha per l'usuari        
        $RET = $this->MenuModel->getMenusByUser($IdUsuari, $IdSite);
        $RET2 = array();
        // Convertim tot el que hem consultat i ho agrupem per "temes"        
        foreach($RET as $Row) {
            $RET2[$Row[$this->MenuModel->getNewFieldNameWithTable('Categoria')]][] = $Row;
        }
        $RET = array();
        foreach($RET2 as $K => $Row) {
            $RET[] = array('Tipus' => $K, 'Dades' => $Row);
        }
        return $RET;
    }
    
}

 ?>
