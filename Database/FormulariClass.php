<?php 

require_once BASEDIR."Database/DB.php";

class OptionClass {
    public $id = '';
    public $text = '';

    public function __construct($id, $text) {
        $this->id = $id; 
        $this->text = $text;
    }

    public function getJson() {
        return json_encode(array('id' => $id, 'text' => $text));
    }
}

class FormulariClass extends BDD {

    const CONST_INPUT_HELPER = 'input-helper';
    const CONST_SELECT_HELPER = 'select-helper';

    public $Titol = '';
    public $Tipus = 'input-helper';
    public $ValorDefecte = null;
    public $Id = '';
    public $OnChange = '';
    public $Options = array();

    public function __construct($Titol, $Tipus, $ValorDefecte, $Id) {
        $this->Titol = $Titol; 
        $this->Tipus = $Tipus;
        $this->ValorDefecte = $ValorDefecte;
        $this->Id = $Id;                
    }                

    public function setOnChange($OnChange) {
        $this->OnChange = $OnChange;
    }

    public function setOptions($Options) {
        $this->Options = $Options;
    }

    public function getArrayObject() {
        
        return array(
                'Titol' => $this->Titol,
                'Tipus' => $this->Tipus,
                'ValorDefecte' => $this->ValorDefecte,
                'Id' => $this->Id,
                'OnChange' => $this->OnChange,
                'Options' => $this->Options            
        );    
    }

}

?>