<?php 

require_once BASEDIR."Database/DB.php";

class OptionClass {
    public $id = '';
    public $text = '';

    public function __construct($id, $text) {
        $this->id = $id; 
        $this->text = $text;
    }

    public function getJson($id, $text) {
        return json_encode(array('id' => $id, 'text' => $text));
    }
}

class FormulariClass {

    private $ModelObject = array();      //L'objecte que guarda les dades del formulari
    private $FormFields = array();   //Un objecte del tipus FormItemClass

    /**
    * Afegim l'objecte de la taula on hi haurà les dades
    */
    
    public function setModelObject($Object) {
        $this->ModelObject = $Object;
    }
    public function  addItem($FormItemElement) {
        $this->FormFields[] = $FormItemElement;
    }

    public  function toArray() {
        $FF = array();
        foreach($this->FormFields as $K => $R):
            $FF[] = $R->getArrayObject();
        endforeach;
        
        return array('FormFields' => $FF, 'ModelObject' => $this->ModelObject);

    }




}

class FormItemClass {

    const CONST_INPUT_HELPER = 'input-helper';
    const CONST_TEXTAREA_HELPER = 'textarea-helper';
    const CONST_SELECT_HELPER = 'select-helper';
    const CONST_IMATGE_HELPER = 'image-helper-cropper';
    const CONST_MULTIPLE_SELECT_HELPER = 'multiple-select-helper';
    const CONST_UPLOAD_HELPER = 'upload-helper';
    
            

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
        if(is_array($OptionActual) && sizeof($OptionActual) > 0) {
            foreach($Options as $K => $O) if ($O->id == $OptionActual->id) unset($Options[$K]);
            $this->Options = array_merge(array($OptionActual), $Options);        
        } else {
            $this->Options = $Options;        
        }
    }

    public function setOptionsSiNo() {
        $this->Options[] = new OptionClass(1, 'Sí'); 
        $this->Options[] = new OptionClass(0, 'No'); 
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

    public function setUpload( $idElement, $Url, $accio_guarda, $accio_esborra ) {
        $this->Imatge_url_a_mostrar = $Url;        
        $this->Imatge_accio_guarda = $accio_guarda;
        $this->Imatge_accio_esborra = $accio_esborra;        
        $this->Imatge_id_element = $idElement;
    }
}

?>