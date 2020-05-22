<?php 

require_once BASEDIR."Database/DB.php";

class MenuModel extends BDD {


    public function __construct() {

        $OldFields = array("menu_id", "actiu", "categoria", "ordre", "tipus", "titol", "url");
        $NewFields = array("MenuId", "Actiu", "Categoria", "Ordre", "Tipus", "Titol", "Url");
        parent::__construct("gestio_menus", "MENUS", $OldFields, $NewFields );                        

    }
    
    public function getEmptyObject() {
        $O = $this->getDefaultObject();        
    }
    
    public function getMenusByUser($idUsuari = 0, $idSite = 0) {

        $SQL = "Select {$this->getSelectFieldsNames()}
                from gestio_menus left join usuaris_menus on (usuaris_menus.menu_id = gestio_menus.menu_id) 
                where usuaris_menus.usuari_id = :UsuariId
                  AND usuaris_menus.actiu = 1 
                  AND usuaris_menus.site_id = :SiteId 
                  AND gestio_menus.url NOT like 'gestio/%'  
                  AND gestio_menus.actiu = 1                  
                ORDER BY gestio_menus.ordre";        
        return $this->runQuery($SQL, array('UsuariId'=>$idUsuari, 'SiteId' => $idSite));
        
    }
}

?>