<?php 
require_once DATABASEDIR . 'Tables/ActivitatsModel.php';


class Auxiliar_CulturaVirtual {
    
    public $FTP = array(
        'ip' => '80.34.222.61',
        'port' => '30',
        'user' => 'casadecultura',
        'password' => '(ccg@#).',
        'NodesUrl' => array(1 => array( 1 => '/Actual/WEB/PMP/') )
    );

    public function __construct() {}

    public function loadArxiusNous( $idS, $idNode ) {
        
        $conn = $this->ConnectaFTP();        
        $baseUrl = DOCUMENTSDIR . '/' . $idNode . '/';
        $ftpUrl = $this->FTP['NodesUrl'][$idS][$idNode];
        $NewFiles = $this->getNewFiles( $conn , $baseUrl, $ftpUrl );

        $this->DownloadFiles( $conn, $NewFiles , $baseUrl, $ftpUrl );

        ftp_close($conn);
    }

    function ConnectaFTP() {
        $conn = ftp_connect($this->FTP['ip'], $this->FPT['port']);
        ftp_login($conn, $this->FTP['user'], $this->FTP['password']);
        return $conn;
    }

    function getNewFiles( $conn , $baseUrl, $ftpUrl ) {        
        $files = ftp_nlist($conn, $ftpUrl);
        $NewFiles = array();
        foreach( $files as $file ) {
            $timeFtp = ftp_mdtm($conn, $ftpUrl . $file);
            $timeLocal = filemtime( $baseUrl . $file );
            if($timeLocal < $timeFtp ) $NewFiles[] = $file;
        }
    }    

    function DownloadFiles( $conn, $NewFiles, $baseUrl, $ftpUrl ) {
        foreach( $NewFiles as $file ) {
            $result = ftp_get($conn, $baseUrl . $file, $ftpUrl . $file, FTP_BINARY);
            if($result === FALSE) $this->guardoLogError();
        }
    }

    function guardoLogError() {

    }

}

?>