<?php 

require_once DATABASEDIR.'Tables/CursosModel.php';
require_once DATABASEDIR.'Tables/MatriculesModel.php';
require_once DATABASEDIR.'Tables/UsuarisSitesModel.php';
require_once DATABASEDIR.'Tables/OptionsModel.php';
require_once BASEDIR . 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class WebApiController
{

    public $WebQueries; 
    public $DataAvui;
    public $DataFi;

    const TIPUS_RESGUARD_CODI_BARRES = 'C';        
    const TIPUS_EMAIL_ENTRADA_NUMERADA = 'E';
    const TIPUS_RESGUARD_GENERAL_INSCRIPCIO = 'I';
    const TIPUS_RESGUARD_MAIL = 'MAIL';
    const TIPUS_RESGUARD_WEB = 'WEB';


    public function __construct() {
        // $this->WebQueries = new WebQueries();
        // $this->setNewDate(date('Y-m-d', time()));        
    }

    public function ExisteixDNI($DNI = '') {
        $U = new UsuarisModel();                
        return $U->ExisteixDNI($DNI);
    }

    public function getUsuariDNI($DNI) {
        $U = new UsuarisModel();
        return $U->getUsuariDNI($DNI);

    }

    /*
    * $idMatricula: Identificador de la matrícula que entra. 
    * $Tipus : array() (C) Té codi de barres, (I) Inscripció, (E) Entrada numerada (W) = Web
    **/
    public function generaResguard( $InscripcioCodificada, $Tipus = array(SELF::TIPUS_RESGUARD_MAIL), $withHeader = true, $withFooter = true ) {
              
        $idMatricula = $this->Decrypt($InscripcioCodificada);                        

        // Carrego els models 

        $OptionsModel = new OptionsModel();
        $MatriculesModel = new MatriculesModel();
        $CursosModel = new CursosModel();
        $UsuarisModel = new UsuarisModel();

        // Carrego els objectes per utilitzar

        $OMatricula = $MatriculesModel->getMatriculaById( $idMatricula );                
        $OCurs = $CursosModel->getCursById( $OMatricula['MATRICULES_CursId'] );
        $OUsuari = $UsuarisModel->getUsuariId( $OMatricula['MATRICULES_UsuariId'] );

        // $HTML = $OptionsModel->getOption('BODY_DOC_MATR_CAIXER', 1);        
        $Logo = $OptionsModel->getOption('LOGO_URL', 1);        
        
        $HTML = '';        
        if( in_array(self::TIPUS_RESGUARD_MAIL, $Tipus) ){

            if($withHeader) $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Mail/MailHeader'.$OMatricula['MATRICULES_SiteId'].'.html' );        
            $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Mail/MailBody'.$OMatricula['MATRICULES_SiteId'].'.html' );        
            if($withFooter) $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Mail/MailFooter'.$OMatricula['MATRICULES_SiteId'].'.html' );

        } elseif( in_array(self::TIPUS_RESGUARD_WEB, $Tipus) ) {

            if($withHeader) $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Web/headerInscripcio'.$OMatricula['MATRICULES_SiteId'].'.html' );        
            $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Web/resguardInscripcio'.$OMatricula['MATRICULES_SiteId'].'.html' );        
            if($withFooter) $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Web/footerInscripcio'.$OMatricula['MATRICULES_SiteId'].'.html' );

        }                        
        
        //Si la matrícula és d'una activitat, carrego la imatge. 
        $ImatgeMatricula = "";
        $ImatgeMatricula_XL = "";
        if($OCurs['CURSOS_ActivitatId'] > 0):
            $ImatgeMatricula = IMATGES_URL_BASE . IMATGES_URL_ACTIVITATS . 'A-' . $OCurs['CURSOS_ActivitatId'] . '-L.jpg';
            $ImatgeMatricula_XL = IMATGES_URL_BASE . IMATGES_URL_ACTIVITATS . 'A-' . $OCurs['CURSOS_ActivitatId'] . '-XL.jpg';
        elseif($OCurs['CURSOS_CicleId'] > 0):
            $ImatgeMatricula = IMATGES_URL_BASE . IMATGES_URL_CICLES . 'C-' . $OCurs['CURSOS_CicleId'] . '-L.jpg';
            $ImatgeMatricula_XL = IMATGES_URL_BASE . IMATGES_URL_CICLES . 'C-' . $OCurs['CURSOS_CicleId'] . '-XL.jpg';
        else:
            $ImatgeMatricula = 'http://www.casadecultura.cat/WebFiles/Web/img/NoImage.jpg';
            $ImatgeMatricula_XL = 'http://www.casadecultura.cat/WebFiles/Web/img/NoImage.jpg';            
        endif;        
        
        /* Falta generar el codi de barres de pagament i el QR de la matrícula */
        \PHPQRCode\QRcode::png($InscripcioCodificada, BASEDIR . "/WebFiles/Inscripcions/" . $InscripcioCodificada .'.png', 'L', 4, 2);        
        
        $HTML = str_replace('@@IMATGE@@', $ImatgeMatricula, $HTML);
        $HTML = str_replace('@@ACTIVITAT@@', $OCurs['CURSOS_TitolCurs'], $HTML);
        $HTML = str_replace('@@HORARI@@', $OCurs['CURSOS_DataInici'], $HTML);
        $HTML = str_replace('@@LLOC@@', 'Casa de Cultura de Girona', $HTML);
        $HTML = str_replace('@@LOCALITAT@@', '----', $HTML);
        $HTML = str_replace('@@USUARI@@', $OUsuari['USUARIS_Dni'] . ' - ' . $OUsuari['USUARIS_Nom'].' '.$OUsuari['USUARIS_Cog1'].' '.$OUsuari['USUARIS_Cog2'], $HTML);
        $HTML = str_replace('@@CODI_BARRES_IMATGE@@', '' , $HTML);
        $HTML = str_replace('@@CODI_BARRES_TEXT@@', '', $HTML);
        $HTML = str_replace('@@QR_IMATGE@@', IMATGES_URL_BASE . IMATGES_URL_INSCRIPCIONS . $InscripcioCodificada . '.png', $HTML);
        $HTML = str_replace('@@QR_TEXT@@', $InscripcioCodificada, $HTML);
        $HTML = str_replace('@@IMATGE_XL@@', $ImatgeMatricula, $HTML);
                        
        // $this->SendEmail('albert.johe@gmail.com', 'informatica@casadecultura.cat', 'Prova', $HTML);        
        
        return $HTML;
        
    }

    public function NovaInscripcioSimple($DNI, $Nom, $Cog1, $Cog2, $Email, $Telefon, $Municipi, $Genere, $AnyNaixement, $QuantesEntrades, $ActivitatId, $CicleId) {                
        
        $OU = array();
        $UM = new UsuarisModel();
        $CM = new CursosModel();
        
        $OU = $UM->getUsuariDNI($DNI);
        // Si no hem trobat el DNI, creem l'usuari
        if(sizeof($OU) == 0) {
            
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

            $OU = $UM->getEmptyObject(1);            
            $OU[$UM->gnfnwt('Dni')] = $DNI; 
            $OU[$UM->gnfnwt('Nom')] = $Nom; 
            $OU[$UM->gnfnwt('Password')] = substr(str_shuffle($permitted_chars), 0, 10); 
            $OU[$UM->gnfnwt('Cog1')] = $Cog1; 
            $OU[$UM->gnfnwt('Cog2')] = $Cog2; 
            $OU[$UM->gnfnwt('Genere')] = $Genere; 
            $OU[$UM->gnfnwt('PoblacioText')] = $Municipi; 
            $OU[$UM->gnfnwt('DataNaixement')] = $AnyNaixement.'-01-01';
            $OU[$UM->gnfnwt('Mobil')] = $Telefon; 
            $OU[$UM->gnfnwt('Email')] = $Email; 
            $OU[$UM->gnfnwt('Poblacio')] = 1; // Posem 1 perquè és constraint
            $OU[$UM->gnfnwt('Habilitat')] = 1;             
            
            // Inserim l'usuari nou
            $id = $UM->doInsert($OU);            
            if($id > 0) $OU = $UM->getUsuariId($id);
            else throw new Exception("No he pogut crear l'usuari amb DNI {$DNI}");         

        }
        
        // Vinculem l'usuari amb el site si fa falta... 
        $USM = new UsuarisSitesModel();
        $USM->addUsuariASite( $OU[ $UM->gnfnwt('IdUsuari') ], 1 ); 

        // Carreguem el curs que toca per fer la matrícula.
        // Si no hi ha el curs que toca o l'activitat el creem a partir del cicle
        $OC = array();                 
        if($ActivitatId > 0) { $OC = $CM->getRowActivitatId($ActivitatId); }
        else if($CicleId > 0) { $OC = $CM->getRowCicleId($CicleId); }
        else throw new Exception("No hi ha cap activitat o cicle on registrar-se");        
        if(empty($OC)) throw new Exception("No he trobat cap inscripció activa per l'activitat ({$ActivitatId}) ni el cicle ({$CicleId})");        
        
        //Passem a gestionar la matrícula
        $MM = new MatriculesModel();

        //Mirem si l'usuari ja té alguna matrícula en aquest curs
        if($MM->getUsuariHasMatricula( $OC[$CM->gnfnwt('IdCurs')], $OU[$UM->gnfnwt('IdUsuari')] ))
            throw new Exception('Ja hi ha inscripcions per a aquest DNI a aquesta activitat/curs.');

        //Si hem trobat l'activitat, comprovem que quedin prous entrades        
        $QuantesMatricules = $MM->getQuantesMatriculesHiHa( $OC[$CM->gnfnwt('IdCurs')] );
        $Matricules = array();
        
        if(($QuantesMatricules + $QuantesEntrades) >= $OC[$CM->gnfnwt('Places')]) throw new Exception('No hi ha prou places disponibles.');
        else {
            for($i = 0; $i < $QuantesEntrades; $i++){
                $OM = $MM->getEmptyObject($OU[$UM->gnfnwt('IdUsuari')], $OC[$CM->gnfnwt('IdCurs')], $OC[$CM->gnfnwt('SiteId')]);
                $OM[$MM->gnfnwt("Estat")] = MatriculesModel::ESTAT_RESERVAT;                
                $id = $MM->doInsert($OM);
                
                if( !($id > 0) ) { throw new Exception("Hi ha hagut algun problema guardant la inscripció. Consulti amb la Casa de Cultura al 972.20.20.13"); }
                else { 
                    $MatriculaId = $this->Encrypt($id);
                    $Matricules[] = $MatriculaId;                                                
                }
            }

            if(sizeof($Matricules) > 0) { $this->EnviaEmailInscripcio($Matricules, $OU[$UM->gnfnwt('Email')], array(self::TIPUS_RESGUARD_MAIL)); }
        
            return $Matricules;
        
        }
    }

    public function EnviaEmailInscripcio( $MatriculesArray, $email, $Tipus ) {
        
        $HTML = "";        
        foreach($MatriculesArray as $K => $MatriculaId){
            // Id, WithHeader, WithFooter            
            $HTML .= $this->generaResguard($MatriculaId, $Tipus, ($K == 0), ($K == (sizeof($MatriculesArray)-1)) );            
        }
                
        if(!empty($email) > 0) $this->SendEmail($email, 'informatica@casadecultura.cat', "Nova inscripció", $HTML);        
        
    }

    public function SendEmail($to, $from, $subject, $HTML) {
        $url = 'https://api.elasticemail.com/v2/email/send';

        try{
                $post = array('from' => $from,
                'fromName' => 'Casa de Cultura de Girona',
                'apikey' => '6c7b2fdd-c15d-4e8d-b61b-d7fbb6d45f46',
                'subject' => $subject,
                'to' => $to,
                'bodyHtml' => $HTML,                
                'isTransactional' => false);
                
                $ch = curl_init();
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $post,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => false,
                    CURLOPT_SSL_VERIFYPEER => false
                ));
                
                $result=curl_exec ($ch);
                $RES = json_decode($result, true);
                curl_close ($ch);
                
                if( $RES['success'] === false ){
                    throw new Exception($result);
                }                
                
        }
        catch(Exception $ex){
            return false;
        }        
    }

    public function SendEmailPhpMailer($to, $from, $subject, $HTML) {
        
        $mail = new PHPMailer();
        
    
        $mail->IsSMTP();
        $mail->CharSet = 'UTF-8';
        $mail->Timeout  =   10;            
        // $mail->Host       = "email-smtp.eu-central-1.amazonaws.com"; // SMTP server example
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
        $mail->SMTPSecure = 'ssl';
        $mail->SMTPAuth   = true;                  // enable SMTP authentication
        $mail->Port       = 465;                    // set the SMTP port for the GMAIL server                
        // $mail->Username   = "AKIAS2ROCN6SN7CJF354"; // SMTP account username example
        $mail->Username = 'albert.johe@gmail.com';
        // $mail->Password   = "BEHMBj1kvHrPeJAmYroQ5BAikHQs7i4OpE007aMRxL1W";        // SMTP account password example
        $mail->Password = 'gmail1981.';
        
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->addAddress($to);
        $mail->From = $from;
        $mail->FromName = "Casa de Cultura de Girona";            
        $mail->addReplyTo($from);            
        $mail->setFrom($from);
        $mail->setAddress = $to;
        $mail->setFrom = $from;
        $mail->Subject = $subject;
        $mail->Body    = $HTML;        

        if(!$mail->send()) {
            var_dump("No s'ha enviat el correu: " . $mail->ErrorInfo);
        }         

    }

    private function Encrypt($id) { return base64_encode(openssl_encrypt($id, 'aes128', '(ccg@#).' )); }
    private function Decrypt($id) { return openssl_decrypt(base64_decode($id), 'aes128', '(ccg@#).' ); }
    
}



 ?>