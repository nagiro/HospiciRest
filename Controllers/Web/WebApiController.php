<?php 

require_once DATABASEDIR.'Tables/CursosModel.php';
require_once DATABASEDIR.'Tables/MatriculesModel.php';
require_once DATABASEDIR.'Tables/UsuarisSitesModel.php';
require_once DATABASEDIR.'Tables/OptionsModel.php';
require_once BASEDIR . 'vendor/autoload.php';
require_once AUXDIR . 'Redsys/apiRedsys.php';

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

    /**
     * Funció que retorna un resguard en HTML
     * $idMatricula: Identificador de la matrícula que entra.     
     * $Tipus : array() (C) Té codi de barres, (I) Inscripció, (E) Entrada numerada (W) = Web
     * @return Resguard HTML
     * 
    **/
    public function generaResguard( $InscripcioCodificada, $UrlDesti = 'https://www.casadecultura.cat', $Preu = 0 ) {
        
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Carrego els models 
        $OptionsModel = new OptionsModel();
        $MatriculesModel = new MatriculesModel();
        $CursosModel = new CursosModel();
        $UsuarisModel = new UsuarisModel();

        /* Carrego els objectes a utilitzar... sempre serà activitat només d'una activitat, agafo la primera matrícula i avall */
        $idMatricula = $this->Decrypt( $InscripcioCodificada );
        $MatriculesVinculades = $MatriculesModel->getMatriculesVinculades($idMatricula, true);                
        
        $OMatricula = $MatriculesModel->getMatriculaById( $idMatricula );        
        $OCurs = $CursosModel->getCursById( $OMatricula['MATRICULES_CursId'] );
        $OUsuari = $UsuarisModel->getUsuariId( $OMatricula['MATRICULES_UsuariId'] );        

        /* Carrego l'import general o bé només el de la primera matrícula */
        $Import = ($Preu == 0 ) ? $OMatricula[$MatriculesModel->gnfnwt('Pagat')] : $Preu;
        $idS = $OMatricula[$MatriculesModel->gnfnwt('SiteId')];

        $HTML = '';


        /*********************** HEADER *********************************/

        $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Mail/MailHeader'.$OMatricula['MATRICULES_SiteId'].'.html' );        
        
        $HTML = str_replace('@@LOGO@@', $OptionsModel->getOption('LOGO_URL', 1), $HTML);
        
        //Si la matrícula és d'una activitat, carrego la imatge. 
        $ImatgeMatricula = "";        
        if($OCurs['CURSOS_ActivitatId'] > 0):
            $ImatgeMatricula = IMATGES_URL_BASE . IMATGES_URL_ACTIVITATS . 'A-' . $OCurs['CURSOS_ActivitatId'] . '-L.jpg';            
        elseif($OCurs['CURSOS_CicleId'] > 0):
            $ImatgeMatricula = IMATGES_URL_BASE . IMATGES_URL_CICLES . 'C-' . $OCurs['CURSOS_CicleId'] . '-L.jpg';            
        else:
            $ImatgeMatricula = 'http://www.casadecultura.cat/WebFiles/Web/img/NoImage.jpg';            
        endif;        
        
        $HTML = str_replace('@@IMATGE@@', $ImatgeMatricula, $HTML);        

        // Si hi ha pagament amb codi de barres i estem agafant el header, el calculem i l'inserim.
        if( MatriculesModel::PAGAMENT_CODI_DE_BARRES == $OMatricula[$MatriculesModel->gnfnwt('TipusPagament')] ) {
            $CB = $this->generaCodiBarres( $idMatricula, $Import, $idS );
            $HTML = str_replace('@@DISPLAY_CODI_BARRES@@', 'display: block;', $HTML);
            $HTML = str_replace('@@URL_CODI_BARRES@@', $CB['URL'], $HTML);
            $HTML = str_replace('@@CODI_BARRES@@',  $CB['CODI'], $HTML);
        } else {
            $HTML = str_replace('@@DISPLAY_CODI_BARRES@@', 'display: none;', $HTML);
        }        
        
        /*********************** BODY *********************************/

        foreach($MatriculesVinculades as $InscripcioCodificada) {

            $idMatricula = $this->Decrypt($InscripcioCodificada);                                    
                        
            $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Mail/MailBody'.$OMatricula['MATRICULES_SiteId'].'.html' );
                                                
            // Genero el QR de la matrícula
            \PHPQRCode\QRcode::png($InscripcioCodificada, BASEDIR . "/WebFiles/Inscripcions/" . $InscripcioCodificada .'.png', 'L', 4, 2);
            
            $HTML = str_replace('@@ACTIVITAT@@', $OCurs['CURSOS_TitolCurs'], $HTML);
            $HTML = str_replace('@@HORARI@@', $OCurs['CURSOS_DataInici'], $HTML);
            $HTML = str_replace('@@LLOC@@', 'Casa de Cultura de Girona', $HTML);
            $HTML = str_replace('@@LOCALITAT@@', '----', $HTML);
            $HTML = str_replace('@@USUARI@@', $OUsuari['USUARIS_Dni'] . ' - ' . $OUsuari['USUARIS_Nom'].' '.$OUsuari['USUARIS_Cog1'].' '.$OUsuari['USUARIS_Cog2'], $HTML);            
            $HTML = str_replace('@@ESTAT@@', $MatriculesModel->getEstatString($OMatricula), $HTML);
            $HTML = str_replace('@@QR_IMATGE@@', IMATGES_URL_BASE . IMATGES_URL_INSCRIPCIONS . $InscripcioCodificada . '.png', $HTML);
            $HTML = str_replace('@@QR_TEXT@@', $InscripcioCodificada, $HTML);            
                  
        }
        
        // Carregp el footer
        $HTML .= file_get_contents( AUXDIR . 'Inscripcions/Mail/MailFooter'.$OMatricula['MATRICULES_SiteId'].'.html' );
        $HTML = str_replace('@@URL_DESTI@@', $UrlDesti, $HTML);
        
        return $HTML;
        
    }

    /**
     * Funció que genera la crida del TPV
     * $idMatriculaGrup: Number indicador del grup de matrícules
     * $Import: Valor a pagar
     * $Ids: Site d'on és el pagament
     * $origen: On s'ha de tornar amb aquesta petició
     */
    public function generaPeticioTPV( $idMatriculaGrup, $import = 0, $idS = 1, $UrlDesti = 'https://www.casadecultura.cat') {        	
 
        // Se crea Objeto
        $miObj = new RedsysAPI;
        $OM = new OptionsModel();        
            
        // Valores de entrada
        $fuc = $OM->getOption("TPV_C_Ds_Merchant_MerchantCode", $idS);        
        $terminal = "1";
        $moneda = "978";
        $trans = "0";
        $url= $OM->getOption("TPV_C_ENT_URL", $idS);                
        
        // Carreguem les URL de retorn
        $urlOKKO = $OM->getOption("TPV_C_WEB_Merchant_MerchantURL", $idS);        
        
        $id = $idMatriculaGrup;
        $amount = $import * 100;                
        
        // Se Rellenan los campos
        $miObj->setParameter("DS_MERCHANT_AMOUNT",$amount);
        $miObj->setParameter("DS_MERCHANT_ORDER",strval($id));
        $miObj->setParameter("DS_MERCHANT_MERCHANTCODE",$fuc);
        $miObj->setParameter("DS_MERCHANT_CURRENCY",$moneda);
        $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE",$trans);
        $miObj->setParameter("DS_MERCHANT_TERMINAL",$terminal);
        $miObj->setParameter("DS_MERCHANT_MERCHANTURL",$url);
        $miObj->setParameter("DS_MERCHANT_URLOK",$urlOKKO);		
        $miObj->setParameter("DS_MERCHANT_URLKO",$urlOKKO);
        $miObj->setParameter("DS_MERCHANT_MERCHANTDATA", base64_encode($UrlDesti));
        $miObj->setParameter("DS_MERCHANT_PRODUCTDESCRIPTION", 'Inscripció');    

        //Datos de configuración
        $kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7';//Clave recuperada de CANALES                
        $TPV['url'] = $url;
        $TPV['version'] = "HMAC_SHA256_V1";        
        $TPV['params'] = $miObj->createMerchantParameters();
        $TPV['signature'] = $miObj->createMerchantSignature($kc);        

        return $TPV;
    }

    /**
     * Funció que crida el TPV virtualment per pagar la factura i que si hi entres posteriorment, et redirigeix cap a l'activitat en qüestió
     */
    public function getTpv($Request, $AnemAOKUrl) {
        
        $miObj = new RedsysAPI;
        $version = $Request["Ds_SignatureVersion"];
        $datos = $Request["Ds_MerchantParameters"];
        $signatureRecibida = $Request["Ds_Signature"];
            
        $decodec = $miObj->decodeMerchantParameters($datos);	            
        $D = json_decode($decodec, true);                
        $UrlDesti = base64_decode($D['Ds_MerchantData']);
        
        $kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'; //Clave recuperada de CANALES
        $firma = $miObj->createMerchantSignatureNotif($kc,$datos);	        
        
        // Tenim resposta i és correcta
        if($D['Ds_Response'] === '0000' && $firma === $signatureRecibida) {
            
            // Crida directament del TPV per validar el pagament
            if ($AnemAOKUrl === false){            

                // Tot correcte. Marquem les matrícules com a pagades. Agafem el grup de matrícules
                $MM = new MatriculesModel();
                $Array_ObjectesMatricula = $MM->getMatriculesVinculades($D["Ds_Order"], false);
                foreach($Array_ObjectesMatricula as $OM):
                    
                    $OM[$MM->gnfnwt('Estat')] = MatriculesModel::ESTAT_ACCEPTAT_PAGAT;
                    $OM[$MM->gnfnwt('TpvOperacio')] = $D["Ds_AuthorisationCode"];
                    $OM[$MM->gnfnwt('TpvOrder')] = $D["Ds_Order"];            
                    $MM->updateMatricula($OM);     

                endforeach;
                
                // Si tot ha anat bé, enviem un correu amb la inscripció                                
                $this->EnviaEmailInscripcio( $this->Encrypt($D["Ds_Order"]), $MM->getUserEmail($OM), array(self::TIPUS_RESGUARD_MAIL), $UrlDesti );                 

            } else {
                
                // isOKUrl == true -> Mostrem les inscripcions i a les inscripcions i posem un enllaç cap a la pàgina d'origen $_SESSION["TPV_UrlDesti"] = $UrlDesti;                
                return $this->generaResguard( $this->Encrypt( $D['Ds_Order'] ), $UrlDesti, array(self::TIPUS_RESGUARD_MAIL) );

            }
        } else {

            throw new Exception('Hi ha hagut algun error fent el pagament. Si us plau, contacti amb la seva entitat.');

        }        

    }

    /**
     * Funció que genera el pagament amb codi de barres
     * $idMatriculaGrup: Number indicador del grup de matrícules
     * $Import: Valor a pagar
     * $Ids: Site d'on és el pagament
     * @return ARRAY('CODI', 'URL')
     */
    public function generaCodiBarres( $idMatriculaGrup, $preu = 0, $idS = 1) {        	
 
        // Se crea Objeto
        $miObj = new RedsysAPI;
        $OptionsModel = new OptionsModel();
        $MatriculesModel = new MatriculesModel(); 
        $MatriculaObject = $MatriculesModel->getMatriculaById($idMatriculaGrup);

        $Rebut = $MatriculaObject[$MatriculesModel->gnfnwt("Rebut")];
        

        $inici = $OptionsModel->getOption( 'PAG_CAIXER_CODI_OP' , $idS );                          
        $entitat = $OptionsModel->getOption( 'PAG_CAIXER_CODI_ENTITAT' , $idS );
        $codi = 0; $referencia = 0;        
                    
        //Càlcul valor de check per 502
        if( $idS == 5 ):            
            
            $referencia = str_pad( strval( $Rebut ),10,'0',STR_PAD_LEFT);
            $tribut = $OptionsModel->getOption('PAG_CAIXER_CODI_TRIBUT', $idS );
            $import = str_pad(strval( $preu * 100 ),8,'0',STR_PAD_LEFT);
    
            $e1 = $entitat; $e2 = $referencia; $e3 = strval($tribut) + strval( $import );        
            $i = ( $e1 * 76 + $referencia * 9 + (( $e3 - 1 ) * 55) ) / 97; //Dóna decimals i hem d'agafar els dos primers        
            
            $decimals = $i - floor($i); // .25        
            $check =  99 - floor( $decimals * 100 );
            $check = str_pad( $check,2,'0', STR_PAD_LEFT);
                            
            $codi = $inici.$entitat.$referencia.$check.$tribut.$import;        
    
        else:
    
            $referencia = str_pad(strval($idMatriculaGrup),11,'0',STR_PAD_LEFT);                                
    
            //Càlcul de valor de check general
            $ponderacions = array( 10=>2 , 9=>3 , 8=>4 , 7=>5 , 6=>6 , 5=>7 , 4=>8 , 3=>9 , 2=>2 , 1=>3 , 0=>4 );
            $tot = 0;
            for($i = 10; $i >= 0; $i--):                                    
                $tot += $referencia[$i]*$ponderacions[$i];
            endfor;                                
            $cc = ($tot % 11); 
            if($cc == 10) $cc = 0;
            
            //Afegim el valor de check a la referència i seguim.
            $referencia .= $cc;
    
            $import = str_pad(strval( $preu * 100 ),10,'0',STR_PAD_LEFT);
            $codi = $inici.$entitat.$referencia.$import;    
    
        endif;
            
        
        $name = IMATGES_URL_INSCRIPCIONS . $idMatriculaGrup . '-barcode.png';
        $barcode_name =  BASEDIR . $name;
        $generator = new Picqer\Barcode\BarcodeGeneratorPNG();
        file_put_contents($barcode_name, $generator->getBarcode($codi, $generator::TYPE_CODE_128));                

        return array('URL' => IMATGES_URL_BASE . $name, 'CODI' => $codi);

    }    

    /**
     * $Origen: web, hospici
     */
    public function NovaInscripcioSimple($DNI, $Nom, $Cog1, $Cog2, $Email, $Telefon, $Municipi, $Genere, $AnyNaixement, $QuantesEntrades, $ActivitatId, $CicleId, $TipusPagament, $UrlDesti) {                
        
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
            
            // Inserim l'usuari nou i el recarreguem de la base de dades per garantir que s'ha creat.
            $id = $UM->doInsert($OU);            
            if($id > 0) $OU = $UM->getUsuariId($id);
            else throw new Exception("No he pogut crear l'usuari amb DNI {$DNI}");         

        }
        
        // Vinculem l'usuari amb el site si fa falta... 
        $USM = new UsuarisSitesModel();
        $USM->addUsuariASite( $OU[ $UM->gnfnwt('IdUsuari') ], 1 ); 

        // Validem si el TipusPagament és correcte
        if($TipusPagament == MatriculesModel::PAGAMENT_CAP) throw new Exception("No heu escollit cap tipus de pagament.");

        // Carreguem el curs que toca per fer la matrícula.
        // Informem que no existeix si no està creat.        
        $OC = array();                 
        if($ActivitatId > 0) { $OC = $CM->getRowActivitatId($ActivitatId); }
        else if($CicleId > 0) { $OC = $CM->getRowCicleId($CicleId); }
        else throw new Exception("No hi ha cap activitat o cicle on registrar-se");        
        if(empty($OC)) throw new Exception("No he trobat cap inscripció activa per l'activitat ({$ActivitatId}) ni el cicle ({$CicleId})");        
                        
        //Passem a gestionar la matrícula
        $MM = new MatriculesModel();

        //Mirem si l'usuari ja té alguna matrícula en aquest curs
//        if($MM->getUsuariHasMatricula( $OC[$CM->gnfnwt('IdCurs')], $OU[$UM->gnfnwt('IdUsuari')] ))
//            throw new Exception('Ja hi ha inscripcions per a aquest DNI a aquesta activitat/curs.');
        
        //Si hem trobat l'activitat, comprovem que quedin prous entrades        
        $QuantesMatricules = $MM->getQuantesMatriculesHiHa( $OC[$CM->gnfnwt('IdCurs')] );
        if(($QuantesMatricules + $QuantesEntrades) >= $OC[$CM->gnfnwt('Places')]) 
            throw new Exception('No hi ha prou places disponibles.');

        $Matricules = array(); $MatriculesId = array(); $idMatriculaGrup = 0;
        $Import = 0;
                
        if($QuantesEntrades > 0) {

            for($i = 0; $i < $QuantesEntrades; $i++){
            
                // Per cada inscripció, creo un objecte matrícula i marco com a reservat
                $OM = $MM->getEmptyObject($OU[$UM->gnfnwt('IdUsuari')], $OC[$CM->gnfnwt('IdCurs')], $OC[$CM->gnfnwt('SiteId')]);
                
                //Marquem l'estat de la matrícula. Si és pagament amb targeta, posem en procès. Els altres, reservat
                $OM = $MM->setEstatFromPagament($OM, $TipusPagament);

                // Posem el preu amb descompte ( quan estigui )
                $OM = $MM->setPreuMatricula($OM, $OC[$CM->gnfnwt('Preu')]);
                $Import += $OC[$CM->gnfnwt('Preu')];
                
                //Guardem la matrícula
                $id = $MM->doInsert($OM);
                $OM[ $MM->gnfnwt('IdMatricula') ] = $id;                                
                if( !($id > 0) ) { throw new Exception("Hi ha hagut algun problema guardant la inscripció. Consulti amb la Casa de Cultura al 972.20.20.13"); }                
                else { 

                    //Si hem guardat la matrícula correctament, indiquem a quin grup pertany
                    if($i == 0) { $idMatriculaGrup = $id; }                    

                    // Encripto el codi de la matrícula per poder-lo buscar després
                    $MatriculesId[] = $id;
                    $Matricules[] = $this->Encrypt($id);
                    
                    //Fem update de la matrícula amb el grup que toqui. 
                    $MM->updateMatricula( $MM->setGrupMatricula($OM, $idMatriculaGrup) );     
                }
            }

            $RET['MATRICULES'] = $Matricules;
            if($TipusPagament == MatriculesModel::PAGAMENT_TARGETA) {                
                $RET['TPV'] = $this->generaPeticioTPV($idMatriculaGrup, $Import, $OC[$CM->gnfnwt('SiteId')], $UrlDesti);
            } else {                
                if(sizeof($Matricules) > 0) { $this->EnviaEmailInscripcio($Matricules[0], $OU[$UM->gnfnwt('Email')], array(self::TIPUS_RESGUARD_MAIL), $UrlDesti); }
            }
                    
            return $RET;
        
        } else {
            throw new Exception("Has d'escollir com a mínim una inscripció");
        }
    }


    /**
     * Funció que genera un resguard amb una o més matrícules dins a partir d'una matrícula del grup
     * $MatriculesArray = array(EncryptedIdMatricula1, EncryptedIdMatricula2)
     * $Tipus = TipusDeResguard (self::TIPUS___)
     * */
/*    public function generaInscripcio($Encrypted_IdMatricula, $UrlDesti, $ArrayTipusInscripcio = array(self::TIPUS_RESGUARD_MAIL)) {
        
        $MM = new MatriculesModel();
        $idMatricula = $this->Decrypt($Encrypted_IdMatricula);
        $MatriculesArray = $MM->getMatriculesVinculades($idMatricula, true);        
                
        // Id, WithHeader, WithFooter            
        $HTML .= $this->generaResguard( $this->Encrypt( $MatriculaId ) , 
                                        $ArrayTipusInscripcio, 
                                        ($K == 0), 
                                        ($K == (sizeof($MatriculesArray)-1)),
                                        $UrlDesti 
                                    );                    
        
        return $HTML;

    }
*/
    /**
     * $Encrypted_IdMatricula: Enviem el codi d'una de les matrícules del grup. 
     * $Email: El correu on s'enviarà.
     * $Tipus: Tipus d'inscripció que apareix
     */
    public function EnviaEmailInscripcio( $Encrypted_IdMatricula, $email, $Tipus = array( self::TIPUS_RESGUARD_MAIL ), $UrlDesti ) {
                        
        $HTML = $this->generaResguard( $Encrypted_IdMatricula, $UrlDesti, $Tipus);
        
        if(!empty($email) > 0) $this->SendEmail($email, 'informatica@casadecultura.cat', "Nova inscripció", $HTML);        
        
        return $HTML;
        
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

    private function Encrypt($id) { return base64_encode(openssl_encrypt($id, 'aes128', '(ccg@#).', 0, '45gh354645gh3546' )); }
    private function Decrypt($id) { return openssl_decrypt(base64_decode($id), 'aes128', '(ccg@#).', 0, '45gh354645gh3546'); }
    
}



 ?>
