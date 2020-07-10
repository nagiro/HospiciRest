<?php 

require_once BASEDIR."Database/DB.php";

class OptionClass {
    public $id = '';
    public $nom = '';

    public function __construct($id, $nom) {
        $this->id = $id; 
        $this->nom = $nom;
    }

    public function getJson() {
        return json_encode(array('id' => $id, 'nom' => $nom));
    }
}

class FormulariClass {

    const CONST_INPUT_HELPER = 'input-helper';
    const CONST_TEXTAREA_HELPER = 'textarea-helper';
    const CONST_SELECT_HELPER = 'select-helper';
    const CONST_IMATGE_HELPER = 'image-helper-cropper';
            

    public $Titol = '';
    public $Tipus = 'input-helper';
    public $ValorDefecte = null;
    public $Id = '';                    // NewFieldNameWithTable
    public $OnChange = '';
    public $Options = array();
    public $Validacions = array();
    public $Imatge_url_a_mostrar = "getUrlImatge('m')";
    public $Imatge_Mida = "m";
    public $Imatge_accio_guarda = "Guarda";
    public $Imatge_accio_esborra = "Esborra";    
    public $Imatge_id_element = 0;    
    
    public function __construct($Titol, $Tipus, $ValorDefecte, $Id, $Objecte, $Validacions) {        
        $this->Titol = $Titol; 
        $this->Tipus = $Tipus;
        if(strlen($Objecte[$Id]) > 0) $this->ValorDefecte = $Objecte[$Id]; 
        else $this->ValorDefecte = $ValorDefecte;
        $this->Id = $Id;          
        $this->Validacions = $Validacions;      
    }                

    public function setOnChange($OnChange) {
        $this->OnChange = $OnChange;
    }

    public function setOptions($Options, $OptionActual) {
        // Trec si existeix l'opció actual i després l'afegeixo. Si no existeix només l'afegeixo
        foreach($Options as $K => $O) if ($O->id == $OptionActual->id) unset($Options[$K]);
        $this->Options = array_merge(array($OptionActual), $Options);        
    }

    public function getArrayObject() {
        
        return array(
                'Titol' => $this->Titol,
                'Tipus' => $this->Tipus,
                'ValorDefecte' => $this->ValorDefecte,
                'Id' => $this->Id,
                'OnChange' => $this->OnChange,
                'Options' => $this->Options,
                'Imatge' => array(
                    'Url_a_mostrar' => $this->Imatge_url_a_mostrar,
                    'Mida' => $this->Imatge_Mida,
                    'Accio_Guarda' => $this->Imatge_accio_guarda,
                    'Accio_Esborra' => $this->Imatge_accio_esborra,
                    'Imatge_Id_Element' => $this->Imatge_id_element
                )
        );    
    }

    public function setImage( $Imatge_id_element, $Imatge_url_a_mostrar, $Imatge_Mida, $Imatge_accio_guarda, $Imatge_accio_esborra ) {
        $this->Imatge_url_a_mostrar = $Imatge_url_a_mostrar;
        $this->Imatge_Mida = $Imatge_Mida;
        $this->Imatge_accio_guarda = $Imatge_accio_guarda;
        $this->Imatge_accio_esborra = $Imatge_accio_esborra;        
        $this->Imatge_id_element = $Imatge_id_element;
    }
}

?>