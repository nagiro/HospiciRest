<?php 

// require_once BASEDIR.'/Database/Tables/UsuarisModel.php';

class FileController {

    public function __construct() {}

    /**
     * $modul = Promocio, Cursos,
     * $file  = Arxiu carregant
     * $tipus = S, L, XL
     * $idU   = Id Usuari
     * $idS   = Id Site 
     * @Return Retornem el nom de l'arxiu a web o bé false si no s'ha pogut guardar
     * */
    public function doUpload($modul, $file, $tipus, $idElement, $idU, $idS) {
        
        $Dir = "";
        $Url = "";
        $imageFileType = strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));        
        $File = '';

        switch($modul) {
            case 'Promocio':    $Dir = IMATGES_DIR_PROMOCIONS;                                 
                                $File = $idElement.'-'.strtoupper($tipus).'.'.$imageFileType;        
                                $Dir  = $Dir . $File;        
                                $Url  = IMATGES_URL_PROMOCIONS . $File;
                                $NameToSave = $File;
                                break;
            case 'Activitat':   $Dir = IMATGES_DIR_ACTIVITATS_NW;                                
                                $File = 'A-'.$idElement.'-'.strtoupper($tipus).'.jpg';                                
                                $Dir  = $Dir . $File;        
                                $Url  = IMATGES_URL_ACTIVITATS_NW . $File;
                                $NameToSave = $File;                                
                                break;
        }                        

        if (!file_exists($Dir)) touch($Dir); 
        if ( move_uploaded_file($file['tmp_name'], $Dir) ) {            
            return array('Filename' => $NameToSave, 'Url' => $Url);
        } else {
            throw new Exception("No he pogut guardar l'arxiu {$File}");
        }
    }    

}


?>