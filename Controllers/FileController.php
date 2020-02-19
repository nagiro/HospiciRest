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
    public function doUpload($modul, $file, $extensio, $tipus, $idElement, $idU, $idS) {
        // Fem el login, i aconseguim un token a partir del seu usuari                
         
        // Extrec el base64 i json values... i deixo només la imatge
        list($type, $data) = explode(';', $file);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);        
        
        $Dir = "";
        $Url = "";

        switch($modul) {
            case 'Promocio':    $Dir = IMATGES_DIR_PROMOCIONS; 
                                $Url = IMATGES_URL_PROMOCIONS;
                                break;
        }

        $File = $idElement.'-'.$tipus.'.'.$extensio;        
        $Dir  = $Dir . $File;        

        if (!file_exists($Dir)) touch($Dir);         
        if ( file_put_contents($Dir, $data) ) {
            return $File;
        } else {
            throw new Exception("No he pogut guardar l'arxiu {$WebName}");
        }
    }    

}


?>