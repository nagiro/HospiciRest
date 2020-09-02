<?php 

require_once DATABASEDIR.'Tables/CursosModel.php';
require_once DATABASEDIR.'Tables/MatriculesModel.php';
require_once DATABASEDIR.'Tables/UsuarisSitesModel.php';
require_once DATABASEDIR.'Tables/OptionsModel.php';
require_once BASEDIR . 'vendor/autoload.php';
require_once AUXDIR . 'Redsys/apiRedsys.php';

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

    public function ValidaQR($QR) {
        $MM = new MatriculesModel();
        $CodiMatricula = $this->Decrypt($QR);            
        return $MM->validaQR($CodiMatricula);                
    }

    public function __construct() {
        // $this->WebQueries = new WebQueries();
        // $this->setNewDate(date('Y-m-d', time()));        
    }

    public function ExisteixDNI($DNI = '', $idCurs = '', $IsRestringit = 0) {
        $UM = new UsuarisModel();                
        $CM = new CursosModel();
        $DNI = strtoupper($DNI);
        $RET['ExisteixDNI'] = $UM->ExisteixDNI($DNI);
        $RET['PotMatricularCursRestringit'] = $CM->potMatricularSegonsRestriccio($DNI, $idCurs);
        
        return $RET;
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
        
        // ini_set('display_errors', 1);
        // ini_set('display_startup_errors', 1);
        // error_reporting(E_ALL);

        // Carrego els models 
        $OptionsModel = new OptionsModel();
        $MatriculesModel = new MatriculesModel();
        $CursosModel = new CursosModel();
        $UsuarisModel = new UsuarisModel();

        /* Carrego els objectes a utilitzar... sempre serà activitat només d'una activitat, agafo la primera matrícula i avall */        
        $idMatricula = $MatriculesModel->getIdMatriculaGrup( $this->Decrypt( $InscripcioCodificada ) );
        $MatriculesVinculades = $MatriculesModel->getMatriculesVinculades( $idMatricula , false );                
                
        $OMatricula = $MatriculesModel->getMatriculaById( $idMatricula );        
        $OCurs = $CursosModel->getCursById( $OMatricula['MATRICULES_CursId'] );
        $OUsuari = $UsuarisModel->getUsuariId( $OMatricula['MATRICULES_UsuariId'] );        

        /* Carrego l'import general o bé només el de la primera matrícula */
        $Import = ($Preu == 0 ) ? $OMatricula[$MatriculesModel->gnfnwt('Pagat')] : $Preu;
        $idS = $OMatricula[$MatriculesModel->gnfnwt('SiteId')];

        $HTML = '';
        

        /* Si la matrícula està en procés de pagament...no podem imprimir les entrades */
        if( ! $MatriculesModel->IsMatriculaCorrectaPerImprimirResguard($OMatricula)) {

            $HTML = file_get_contents( AUXDIR . "Inscripcions/Mail/{$idS}/MailError.html" );                    

        } else {

            /*********************** HEADER *********************************/

            $HTML .= file_get_contents( AUXDIR . "Inscripcions/Mail/{$idS}/MailHeader.html" );                    

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

            /*********************** BODY *********************************/

            $Import_total_a_pagar = 0;

            foreach($MatriculesVinculades as $MatriculaVinculadaObjecte) {            
                            
                $OMatricula = $MatriculaVinculadaObjecte;
                $NumeroInscripcio = $OMatricula[$MatriculesModel->gnfnwt('IdMatricula')];

                $HTML .= file_get_contents( AUXDIR . "Inscripcions/Mail/{$idS}/MailBody.html" );
                                                    
                // Genero el QR de la matrícula
                \PHPQRCode\QRcode::png($NumeroInscripcio, BASEDIR . "/WebFiles/Inscripcions/" . $NumeroInscripcio .'.png', 'L', 4, 2);

                // Busco la localitat si existeix
                $LocalitatText = '-----';
                if($CursosModel->hasEscullLocalitats($OCurs)) { $LocalitatText = $MatriculesModel->getLocalitatString($OMatricula); }
                $DisplayLocalitat = ($LocalitatText == '-----') ? 'none' : 'block';
                
                $HTML = str_replace('@@ACTIVITAT@@', $OCurs['CURSOS_TitolCurs'], $HTML);
                $HTML = str_replace('@@HORARI@@', $OCurs['CURSOS_DataInici'], $HTML);
                $HTML = str_replace('@@LLOC@@', 'Casa de Cultura de Girona', $HTML);
                $HTML = str_replace('@@LOCALITAT@@', $LocalitatText, $HTML);
                $HTML = str_replace('@@USUARI@@', $OUsuari['USUARIS_Dni'] . ' - ' . $OUsuari['USUARIS_Nom'].' '.$OUsuari['USUARIS_Cog1'].' '.$OUsuari['USUARIS_Cog2'], $HTML);            
                $HTML = str_replace('@@ESTAT@@', $MatriculesModel->getEstatString($OMatricula), $HTML);
                $HTML = str_replace('@@IMPORT@@', $OMatricula[$MatriculesModel->gnfnwt('Pagat')], $HTML);
                $HTML = str_replace('@@DESCOMPTE@@', $MatriculesModel->getDescompteString($OMatricula), $HTML);
                $HTML = str_replace('@@QR_IMATGE@@', IMATGES_URL_BASE . IMATGES_URL_INSCRIPCIONS . $NumeroInscripcio . '.png', $HTML);
                $HTML = str_replace('@@QR_TEXT@@', $NumeroInscripcio, $HTML);            
                $HTML = str_replace('@@DISPLAY_LOCALITAT@@', $DisplayLocalitat, $HTML);            
                

                $Import_total_a_pagar += $OMatricula[$MatriculesModel->gnfnwt('Pagat')];
                    
            }
                

            /************************ PAGAMENT CODI DE BARRES AL HEADER *********************************/

            if( MatriculesModel::PAGAMENT_CODI_DE_BARRES == $OMatricula[$MatriculesModel->gnfnwt('TipusPagament')] ) {
                $CB = $this->generaCodiBarres( $idMatricula, $Import, $idS );
                $HTML = str_replace('@@DISPLAY_CODI_BARRES@@', 'display: block;', $HTML);
                $HTML = str_replace('@@URL_CODI_BARRES@@', $CB['URL'], $HTML);
                $HTML = str_replace('@@CODI_BARRES@@',  $CB['CODI'], $HTML);
                $HTML = str_replace('@@IMPORT_TOTAL@@',  $Import_total_a_pagar, $HTML);
            } else {
                $HTML = str_replace('@@DISPLAY_CODI_BARRES@@', 'display: none;', $HTML);
                $HTML = str_replace('@@IMPORT_TOTAL@@',  $Import_total_a_pagar, $HTML);
            }        


            /************************ PAGAMENT AMB TARGETA AL HEADER *********************************/

            if( MatriculesModel::PAGAMENT_TARGETA == $OMatricula[$MatriculesModel->gnfnwt('TipusPagament')] ) {            
                $HTML = str_replace('@@DISPLAY_PAGAMENT_TARGETA@@', 'display: block;', $HTML);                        
                $HTML = str_replace('@@IMPORT_TOTAL@@',  $Import_total_a_pagar, $HTML);
            } else {
                $HTML = str_replace('@@DISPLAY_PAGAMENT_TARGETA@@', 'display: none;', $HTML);
                $HTML = str_replace('@@IMPORT_TOTAL@@',  $Import_total_a_pagar, $HTML);
            }        

            /******************************* FOOTER *******************************/

            $HTML .= file_get_contents( AUXDIR . "Inscripcions/Mail/{$idS}/MailFooter.html" );
            
            
        
        }

        $HTML = str_replace('@@LOGO@@', $OptionsModel->getOption('LOGO_URL', $idS), $HTML);
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
        $TPV = json_decode($OM->getOption("TPV_C_PARAMS", $idS), true);                                                                 
        
        // Calculem l'ID i l'import 
        $id = $idMatriculaGrup;
        $amount = $import * 100;                
        
        // Se Rellenan los campos
        $miObj->setParameter("DS_MERCHANT_AMOUNT",          $amount);
        $miObj->setParameter("DS_MERCHANT_ORDER",           strval($id));
        $miObj->setParameter("DS_MERCHANT_MERCHANTCODE",    $TPV['DS_MERCHANT_MERCHANTCODE']);
        $miObj->setParameter("DS_MERCHANT_CURRENCY",        $TPV['DS_MERCHANT_CURRENCY']);
        $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $TPV['DS_MERCHANT_TRANSACTIONTYPE']);
        $miObj->setParameter("DS_MERCHANT_TERMINAL",        $TPV['DS_MERCHANT_TERMINAL']);
        $miObj->setParameter("DS_MERCHANT_MERCHANTURL",     $TPV['DS_MERCHANT_MERCHANTURL']);
        $miObj->setParameter("DS_MERCHANT_URLOK",           $TPV['DS_MERCHANT_URLOK']);		
        $miObj->setParameter("DS_MERCHANT_URLKO",           $TPV['DS_MERCHANT_URLKO']);
        $miObj->setParameter("DS_MERCHANT_NAME",            $TPV['DS_MERCHANT_NAME']);
        $miObj->setParameter("DS_PRODUCT_DESCRIPTION",      $TPV['DS_PRODUCT_DESCRIPTION']);
        $miObj->setParameter("DS_MERCHANT_MERCHANTDATA",    base64_encode($UrlDesti));
        $miObj->setParameter("DS_MERCHANT_PRODUCTDESCRIPTION", 'Inscripció / Entrada');    

        //Datos de configuración
        $kc = $TPV['CLAU_RECUPERACIO_CANALS'];//Clave recuperada de CANALES                
        $TPV['url'] = $TPV['URL'];
        $TPV['version'] = $TPV['VERSIO'];        
        $TPV['params'] = $miObj->createMerchantParameters();
        $TPV['signature'] = $miObj->createMerchantSignature($kc);        

        return $TPV;
    }

    /**
     * Funció que crida el TPV virtualment per pagar la factura i que si hi entres posteriorment, et redirigeix cap a l'activitat en qüestió
     * La Clau de recuperació de canals, sempre ha de ser igual al SITE 1. 
     */
    public function getTpv($Request, $AnemAOKUrl) {
        
        $miObj = new RedsysAPI;
        $OM = new OptionsModel();                        
        
        $version = $Request["Ds_SignatureVersion"];
        $datos = $Request["Ds_MerchantParameters"];
        $signatureRecibida = $Request["Ds_Signature"];
            
        $decodec = $miObj->decodeMerchantParameters($datos);	            
        $D = json_decode($decodec, true);        
        
        // Carreguem la matrícula amb el SITE al que pertany
        $MM = new MatriculesModel();        
        $OMatricula = $MM->getMatriculaById($D['Ds_Order']);
        $TPV = json_decode($OM->getOption("TPV_C_PARAMS", $MM->getSiteValue($OMatricula)), true);        
        $UrlDesti = base64_decode($D['Ds_MerchantData']);
        
        $kc = $TPV['CLAU_RECUPERACIO_CANALS']; //Clave recuperada de CANALES
        $firma = $miObj->createMerchantSignatureNotif($kc,$datos);	        
        
        // Tenim resposta i és correcta
        if($D['Ds_Response'] === '0000' && $firma === $signatureRecibida) {
            
            // Crida directament del TPV per validar el pagament
            if ($AnemAOKUrl === false){            

                // Tot correcte. Marquem les matrícules com a pagades. Agafem el grup de matrícules                
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
                return $this->generaResguard( $this->Encrypt( $D['Ds_Order'] ), $UrlDesti, 0 );

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
    public function NovaInscripcioSimple($DNI, $Nom, $Cog1, $Cog2, $Email, $Telefon, $Municipi, $Genere, $AnyNaixement, $QuantesEntrades, $ActivitatId, $CicleId, $CursId, $TipusPagament, $UrlDesti, $DescompteAplicat, $Localitats, $Token) {                
        
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
        
        // Validem si el TipusPagament és correcte
        if($TipusPagament == MatriculesModel::PAGAMENT_CAP) throw new Exception("No heu escollit cap tipus de pagament.");

        // Carreguem el curs que toca per fer la matrícula.
        // Informem que no existeix si no està creat.        
        $OC = array();                 
        if($ActivitatId > 0) { $OC = $CM->getRowActivitatId($ActivitatId); }
        else if($CicleId > 0) { $OC = $CM->getRowCicleId($CicleId); }
        else if($CursId > 0) { $OC = $CM->getCursById($CursId); }
        else throw new Exception("No hi ha cap activitat o cicle on registrar-se");        
        if(empty($OC)) throw new Exception("No he trobat cap inscripció activa per l'activitat ({$ActivitatId}) ni el cicle ({$CicleId}) ni el curs ({$CursId})");        

        $idSite = $OC[$CM->gnfnwt('SiteId')];
        $idCurs = $OC[$CM->gnfnwt('IdCurs')];        

        // Vinculem l'usuari amb el site si fa falta... 
        $USM = new UsuarisSitesModel();
        $USM->addUsuariASite( $OU[ $UM->gnfnwt('IdUsuari') ], $idSite ); 

        // Validem que passi el token... si no el superem, sortim.
        $isAdmin = false;
        if( sizeof($Token) == 2 && strlen($Token[1]) > 0 ) {
            require_once CONTROLLERSDIR.'AuthController.php';
            $Auth = new AuthController();
            $Auth->DecodeToken($Token[1]);
            if( $Auth->getSiteIdIfAdmin() != $idSite || $Auth->getSiteIdIfAdmin() == 0) throw new Exception("Hi ha hagut algun problema autenticant. Torna a provar-ho.");
            $isAdmin = $Auth->isAdmin();
        }
                        
        //Passem a gestionar la matrícula
        $MM = new MatriculesModel();

        // Mirem si l'usuari ja té alguna matrícula en aquest curs (Menys per l'administrador)
        $RestringitNomesUnCop = $CM->getIsRestringit($OC, CursosModel::RESTRINGIT_NOMES_UN_COP);        
        if($RestringitNomesUnCop && !$isAdmin) {
            $UsuariHasMatricules = $MM->getUsuariHasMatricula( $OC[$CM->gnfnwt('IdCurs')], $OU[$UM->gnfnwt('IdUsuari')] );
            if($UsuariHasMatricules) throw new Exception('Ja hi ha inscripcions per a aquest DNI a aquesta activitat/curs.');
        }
        
        // Marquem les entrades escollides comptant les localitats
        if( sizeof($Localitats) > 0 ) {
            $QuantesEntrades = sizeof($Localitats);
        } 

        //Si hem trobat l'activitat, comprovem que quedin prous entrades        
        $QuantesMatricules = $MM->getQuantesMatriculesHiHa( $idCurs );
        if(($QuantesMatricules + $QuantesEntrades) > $OC[$CM->gnfnwt('Places')]) 
            throw new Exception('No hi ha prou places disponibles.');

        $Matricules = array(); $MatriculesId = array(); $idMatriculaGrup = 0;
        $Import = 0;
                
        if($QuantesEntrades > 0) {

            // Si són localitats, mirem que no estiguin ocupades per algú actualment
            if( ! $MM->hasSeientsSonLliures($Localitats, $CM->getCursId($OC) ) ) throw new Exception('Hi ha hagut algun conflicte guardant les localitats. Torna-ho a provar.');

            // Guardem les inscripcions
            for($i = 0; $i < $QuantesEntrades; $i++){
            
                // Per cada inscripció, creo un objecte matrícula i marco com a reservat
                $OM = $MM->getEmptyObject($OU[$UM->gnfnwt('IdUsuari')], $idCurs, $idSite);
                
                //Marquem l'estat de la matrícula. Si és pagament amb targeta, posem en procès. Els altres, reservat
                $OM = $MM->setEstatFromPagament($OM, $TipusPagament);

                // Si hi ha descompte, l'apliquem. 
                $PreuPagat = $OC[$CM->gnfnwt('Preu')];
                if($DescompteAplicat > -1) {                                        
                    $PreuPagat = $CM->getPreuAplicantDescompte($OC, $DescompteAplicat);
                    $OM[$MM->gnfnwt('TipusReduccio')] = $DescompteAplicat;
                }
                 
                // Guardem l'import a la matrícula i el tipus de pagament
                $OM = $MM->setPreuMatricula($OM, $PreuPagat);
                $Import += $PreuPagat;
                $OM[$MM->gnfnwt('TipusPagament')] = $TipusPagament;

                // Si és amb localitats, guardo les localitats
                if ( $CM->hasEscullLocalitats($OC) ) {
                    $OM = $MM->setLocalitat($OM, $Localitats[$i] );                    
                }
                                                            
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
                $RET['TPV'] = $this->generaPeticioTPV($idMatriculaGrup, $Import, $idSite, $UrlDesti);
            } else {                
                if(sizeof($Matricules) > 0) { $this->EnviaEmailInscripcio($Matricules[0], $OU[$UM->gnfnwt('Email')], array(self::TIPUS_RESGUARD_MAIL), $UrlDesti); }
            }
                    
            return $RET;
        
        } else {
            throw new Exception("Has d'escollir com a mínim una inscripció");
        }
    }

    /**
     * $Encrypted_IdMatricula: Enviem el codi d'una de les matrícules del grup. 
     * $Email: El correu on s'enviarà.
     * $Tipus: Tipus d'inscripció que apareix
     */
    public function EnviaEmailInscripcio( $Encrypted_IdMatricula, $email, $Tipus = array( self::TIPUS_RESGUARD_MAIL ), $UrlDesti ) {
                        
        $HTML = $this->generaResguard( $Encrypted_IdMatricula, $UrlDesti, 0);
        
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

    private function Encrypt($id) { return base64_encode(openssl_encrypt($id, 'aes128', '(ccg@#).', 0, '45gh354645gh3546' )); }
    private function Decrypt($id) { return openssl_decrypt(base64_decode($id), 'aes128', '(ccg@#).', 0, '45gh354645gh3546'); }
    
}



 ?>
