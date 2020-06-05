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

        switch($modul) {
            case 'Promocio':    $Dir = IMATGES_DIR_PROMOCIONS; 
                                $Url = IMATGES_URL_PROMOCIONS;
                                break;
        }

        $File = $idElement.'-'.strtoupper($tipus).'.'.$imageFileType;
        $Dir  = $Dir . $File;        

        if (!file_exists($Dir)) touch($Dir); 
        if ( move_uploaded_file($file['tmp_name'], $Dir) ) {
            return $File;
        } else {
            throw new Exception("No he pogut guardar l'arxiu {$File}");
        }
    }    

}


?>