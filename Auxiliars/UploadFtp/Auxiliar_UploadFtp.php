<?php 
require_once DATABASEDIR . 'Tables/ActivitatsModel.php';
require_once DATABASEDIR . 'Tables/OptionsModel.php';


class Auxiliar_UploadFtp {
    
    public $FTP = array();        

    public function __construct($idSite) {
        $OM = new OptionsModel();        
        $this->FTP = json_decode($OM->getOption('AUX_UPLOADFTP', $idSite), true);
        if($this->FTP === FALSE) $this->guardoLogError("No he pogut carregar les dades d'upload ftp.");
    }

    /**
     * Funció que carrega els arxius nous del servidor nostre, al servidor web per mostrar-lo a la web.
     */
    public function loadArxiusNous( $idNode ) {
        
        $conn = $this->ConnectaFTP();        
        $baseUrl = DOCUMENTSDIR . $idNode . '/';
        $ftpUrl = $this->FTP['NODESURL'][$idNode];
        $NewFiles = $this->getNewFiles( $conn , $baseUrl, $ftpUrl );

        $this->DownloadFiles( $conn, $NewFiles , $baseUrl, $ftpUrl );

        ftp_close($conn);
        
    }

    /**
     * Funció que connecta al servidor Ftp mitjançant dades guardades a l'opció AUX_UPLOADFTP
     */
    function ConnectaFTP() {
        $conn = ftp_connect($this->FTP['IP'], $this->FTP['PORT']);
        if($conn !== FALSE) {
            ftp_login($conn, $this->FTP['USER'], $this->FTP['PASSWORD']);
            return $conn;
        } else {
            $this->guardoLogError("No he pogut connectar al ftp. " . print_r($this->FTP, true));
        }
    }

    /**
     * Identifico els arxius nous i els carregaré. 
     */    
    function getNewFiles( $conn , $baseUrl, $ftpUrl ) {        
        $ArxiusAlWeb = array_diff(scandir($baseUrl), array('..', '.'));        
        $ArxiusAlFtp = ftp_nlist($conn, $ftpUrl);     // Agafo els arxius de l'ftp ( el servidor local )
        $NewFiles = array();
        foreach( $ArxiusAlFtp as $FileAtFTP ) {            //Per cada arxiu que no sigui al web o que sigui més nou al nostre servidor, el carrego. 
            $file_at_ftp = basename($FileAtFTP);            
            $timeFtp = ftp_mdtm($conn, $ftpUrl . $file_at_ftp);
            $timeLocal = 0;
            if( !is_dir($baseUrl)) mkdir($baseUrl);
            if( file_exists( $baseUrl . $file_at_ftp ) ) $timeLocal = filemtime( $baseUrl . $file_at_ftp );            
            if($timeLocal < $timeFtp ) $NewFiles[] = $file_at_ftp;
        }

        //Per tots els arxius del web que no siguin al ftp, els esborro per no mostrar-los.                
        foreach($ArxiusAlWeb as $ArxiuWeb) {
            $trobat = false;
            foreach($ArxiusAlFtp as $ArxiuFtp) {
                if($ArxiuWeb == basename($ArxiuFtp)) $trobat = true;
                continue;
            }
            if(!$trobat) unlink( $baseUrl . $ArxiuWeb );
        }            

        return $NewFiles;
    }    

    function DownloadFiles( $conn, $NewFiles, $baseUrl, $ftpUrl ) {
        foreach( $NewFiles as $file ) {
            $result = ftp_get($conn, $baseUrl . $file, $ftpUrl . $file, FTP_BINARY);
            if($result === FALSE) $this->guardoLogError("No he pogut carregar l'arxiu: {$file}");
        }
    }

    function guardoLogError($Missatge) {
        $Data = date("d/M/Y H:i:s");
        $TextToSave = $Data . ' | '.$Missatge;

        $FileName = date('Y-m') . '-log.txt';

        file_put_contents( LOGSDIR . $FileName , $TextToSave, FILE_APPEND );

        die;
        
    }

}

?>