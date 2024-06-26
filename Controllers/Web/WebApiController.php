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
        $CodiMatricula = (is_numeric($QR)) ? $QR : $this->Decrypt($QR);            
        return $MM->validaQR($CodiMatricula);                
    }

    public function __construct() {
        // $this->WebQueries = new WebQueries();
        // $this->setNewDate(date('Y-m-d', time()));        
    }

    public function getOcupacioEspai($idEspai, $Mes, $Any) {        
        require_once DATABASEDIR . 'Tables/HorarisEspaisModel.php';
        $HEM = new HorarisEspaisModel();
        return $HEM->getHorarisEspaisOcupats($idEspai, $Mes, $Any);
    }

    /**
    * Funció que ens diu si un DNI existeix o no. Si existeix retorna l'usuari encriptat
     */
    public function ExisteixDNI($DNI = '') {
        $UM = new UsuarisModel();                
        
        //Limitem el DNI ( per si entra un passaport a 12 caràcters que és el màxim de la intranet )
        $DNI = substr($DNI, 0, 12);
        
        $DNI = strtoupper($DNI);
        $idUsuari = $UM->ExisteixDNI($DNI);
        $RET['ExisteixDNI'] = ($idUsuari > 0);
        $RET['IdUsuariEncrypted'] = self::Encrypt($idUsuari);        
                
        return $RET;
    }

    /**
    * Funció que retorna els permisos d'un usuari en funció d'un curs. 
    */
    public function getPermisosUsuariCursos($DNI = 0, $idUsuariDecrypted = 0, $idCurs = 0, $IsRestringit = 0) {
        if($idCurs > 0) {
            $CM = new CursosModel();
            $MM = new MatriculesModel();
            $RET['PotMatricularCursRestringit'] = $CM->potMatricularSegonsRestriccio($DNI, $idCurs);
            $RET['HasUsuariMatriculaAAquestCurs'] = $MM->getUsuariHasMatricula($idCurs, $idUsuariDecrypted);
        }
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
                \PHPQRCode\QRcode::png($this->Encrypt($NumeroInscripcio), BASEDIR . "/WebFiles/Inscripcions/" . $NumeroInscripcio .'.png', 'L', 4, 2);

                // Busco la localitat si existeix
                $LocalitatText = '-----';
                if($CursosModel->hasEscullLocalitats($OCurs)) { $LocalitatText = $MatriculesModel->getLocalitatString($OMatricula); }
                $DisplayLocalitat = ($LocalitatText == '-----') ? 'none' : 'block';
                
                $HTML = str_replace('@@ACTIVITAT@@', $OCurs['CURSOS_TitolCurs'], $HTML);
                $HTML = str_replace('@@DESCRIPCIO@@', $OCurs['CURSOS_Descripcio'], $HTML);
                $HTML = str_replace('@@DESCRIPCIO_HORARIS@@', $OCurs['CURSOS_Horaris'], $HTML);
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
                $CB = $this->generaCodiBarres( $idMatricula, $Import_total_a_pagar, $idS );
                $HTML = str_replace('@@DISPLAY_CODI_BARRES@@', 'display: block;', $HTML);
                $HTML = str_replace('@@URL_CODI_BARRES@@', $CB['URL'], $HTML);
                $HTML = str_replace('@@CODI_BARRES@@',  $CB['CODI'], $HTML);
                $HTML = str_replace('@@IMPORT_TOTAL@@',  $Import_total_a_pagar, $HTML);
            } else {
                $HTML = str_replace('@@DISPLAY_CODI_BARRES@@', 'display: none;', $HTML);
                $HTML = str_replace('@@IMPORT_TOTAL@@',  $Import_total_a_pagar, $HTML);
            }        


            /************************ PAGAMENT AMB TARGETA AL HEADER *********************************/

            if(     MatriculesModel::PAGAMENT_TARGETA == $OMatricula[$MatriculesModel->gnfnwt('TipusPagament')] 
                ||  MatriculesModel::PAGAMENT_DATAFON == $OMatricula[$MatriculesModel->gnfnwt('TipusPagament')]
            ) { 
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
        $HTML = str_replace('@@URL_DOWNLOAD@@', 'https://sites.hospici.cat/apiweb/GeneraResguard?i='.$InscripcioCodificada.'&g=&d=', $HTML);
        $HTML = str_replace('@@URL_PRINT@@', 'javascript:window.print()', $HTML);
                
        return array("html" => $HTML, "SiteId" => $idS);
        
    }

    private function ConvertImageBase64Url($url) {
        // Guardo la imatge en format Base64        
        $type = pathinfo($url, PATHINFO_EXTENSION);
        $data = file_get_contents($url);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * Llistat que treu els seients ocupats i lliures d'un teatre
     */
    public function getLlistatTeatre($idActivitatCurs) {
        $CM = new CursosModel();
        $OC = $CM->getCursById($idActivitatCurs);        
        $Teatre = $CM->getTeatre($OC);
        $SeientsIOcupacions = array();
        foreach($Teatre['Seients'] as $Fila):
            foreach($Fila as $Seient):
                if($Seient['tipus'] == 'loc'):                                        
                    $SeientsIOcupacions["F: ".$Seient['fila'].' | S: '.$Seient['seient']] = 
                        array('Fila' => $Seient['fila'], 'Seient' => $Seient['seient'], 'Ocupat' => false, 'Dades' => '---');
                endif;            
            endforeach;
        endforeach;
        
        //Miro els matriculats d'aquest curs i els vinculo amb els llocs escollits
        $LlistatMatricules = $CM->getMatriculesByCursAndUserData($idActivitatCurs);        
        foreach($LlistatMatricules as $OM):
            $index = "F: ".$OM['Fila'].' | S: '.$OM['Seient'];
            if(isset($SeientsIOcupacions[$index])):                
                 $SeientsIOcupacions[$index]['Ocupat'] = true;
                 $SeientsIOcupacions[$index]['Dades'] = $OM['Cog1'] . ' ' . $OM['Cog2'].', '.$OM['Nom'] . ' - '.$OM['Email'].' - '.$OM['Telefon'];                 
                 if(isset($OM['Comentari'])) $SeientsIOcupacions[$index]['Dades'] .= ' | ' . $OM['Comentari'];
            endif;
        endforeach;

        foreach($Teatre['Seients'] as $Fila):
            foreach($Fila as $S):
                if($S['tipus'] == 'fila') echo $S['text'].' ';
                if($S['tipus'] == 'bloc') echo ' _ ';
                if($S['tipus'] == 'loc') echo $S['seient'];
            endforeach;
            echo "\n";
        endforeach;
        echo "\n\n";

        foreach($SeientsIOcupacions as $K => $E):
            echo $K . ' => '. $E['Dades']. "\n";
        endforeach;
        
    }

    /**
     * Funció que guarda el codi de la operació que s'ha fet amb un TPV
     * $CodiOperacio = El codi que dóna el datàfon
     * $Matricules = Llistat de les matrícules associades a aquest número codificades
     * @return true si ha anat bé
     */
    public function setCodiOperacio($CodiOperacio, $Matricules, $PagatCorrectament) {
        
        $MM = new MatriculesModel();
        
        foreach($Matricules as $CodiMatricula):
            $idMatricula = $this->Decrypt($CodiMatricula);
            $OM = $MM->getMatriculaById($idMatricula);
            if($PagatCorrectament == '1') $OM[$MM->gnfnwt("Estat")] = $MM::ESTAT_ACCEPTAT_PAGAT;
            $OM[$MM->gnfnwt('TpvOperacio')] = $CodiOperacio;
            $MM->updateMatricula($OM);            
        endforeach;                
        
    }

    /**
     * Funció que genera la crida del TPV
     * $idMatriculaGrup: Number indicador del grup de matrícules
     * $Import: Valor a pagar
     * $Ids: Site d'on és el pagament
     * $origen: On s'ha de tornar amb aquesta petició
     */
    public function generaPeticioTPV( $idMatriculaGrup, $import = 0, $idS = 1, $UrlDesti = 'https://www.casadecultura.cat', $Tipus = 0) {        	
 
        // Se crea Objeto
        $miObj = new RedsysAPI;
        $OM = new OptionsModel();                
        $TPV = json_decode($OM->getOption("TPV_C_PARAMS", $idS), true);                                                                 

        //Si entrem amb preautorització ho indiquem al paràmetre corresponent
        $miObj->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $Tipus);
        
        // Calculem l'ID i l'import 
        $id = $idMatriculaGrup;
        $amount = $import * 100;                
        
        // Se Rellenan los campos
        $miObj->setParameter("DS_MERCHANT_AMOUNT",          $amount);
        $miObj->setParameter("DS_MERCHANT_ORDER",           strval($id));
        $miObj->setParameter("DS_MERCHANT_MERCHANTCODE",    $TPV['DS_MERCHANT_MERCHANTCODE']);
        $miObj->setParameter("DS_MERCHANT_CURRENCY",        $TPV['DS_MERCHANT_CURRENCY']);        
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
                $Resposta = $this->generaResguard( $this->Encrypt( $D['Ds_Order'] ), $UrlDesti, 0 );
                return $Resposta['html'];

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
    * Baixa inscripció, possiblement haurem de fer que entrin el correu electrònic o alguna altra dada per validar-ho.    
    **/
    public function BaixaInscripcioWeb($idUsuari, $idCurs) {
                
        $MM = new MatriculesModel();    
        $ArrayMatricules = $MM->getUsuariHasMatricula($idCurs, $idUsuari, true);
        foreach($ArrayMatricules as $OM) {
            $MM->doBaixaWeb($OM);
        }        

        return sizeof($ArrayMatricules);

    }

    /**
    * Funció que dóna d'alta un nou usuari i retorna el seu IdUsuari
     */
    public function NouUsuari($DNI, $Nom, $Cog1, $Cog2, $Email, $Telefon, $Municipi, $Genere, $AnyNaixement) {
        
        $UM = new UsuarisModel();

        //Limitem el DNI ( per si entra un passaport a 12 caràcters que és el màxim de la intranet )
        $DNI = substr($DNI, 0, 12);
        
        $OU = array();        
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
                        
            return $id; 
            
        } else {
            return $UM->getId($OU);
        }

    }

    /**
     * $Origen: web, hospici
     */
    public function NovaInscripcioSimple($IdUsuari, $QuantesEntrades, $ActivitatId, $CicleId, $CursId, $TipusPagament, $UrlDesti, $DescompteAplicat, $Localitats, $Token, $DadesExtres) {                
        
        $UM = new UsuarisModel();
        $CM = new CursosModel();

        //Carreguem l'usuari en qüestió
        $OU = $UM->getUsuariId($IdUsuari);
                
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
        $USM->addUsuariASite( $IdUsuari, $idSite ); 

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
            $UsuariHasMatricules = $MM->getUsuariHasMatricula( $OC[$CM->gnfnwt('IdCurs')], $IdUsuari );
            if($UsuariHasMatricules) throw new Exception('Ja hi ha inscripcions per a aquest DNI a aquesta activitat/curs.');
        }
        
        // Marquem les entrades escollides comptant les localitats
        if( sizeof($Localitats) > 0 ) {
            $QuantesEntrades = sizeof($Localitats);
        } 

        //Si hem trobat l'activitat, comprovem que quedin prous entrades        
        $QuantesMatricules = $MM->getQuantesMatriculesHiHa( $idCurs );
        $TotalEntrades = $QuantesMatricules + $QuantesEntrades;
        if( $TotalEntrades > $OC[$CM->gnfnwt('Places')] && $TipusPagament != $MM::PAGAMENT_LLISTA_ESPERA )
            throw new Exception('No hi ha prou places disponibles.');

        $Matricules = array(); $MatriculesId = array(); $idMatriculaGrup = 0;
        $Import = 0;
                
        if($QuantesEntrades > 0) {

            // Si són localitats, mirem que no estiguin ocupades per algú actualment
            if( ! $MM->hasSeientsSonLliures($Localitats, $CM->getCursId($OC) ) ) throw new Exception('Hi ha hagut algun conflicte guardant les localitats. Torna-ho a provar.');

            // Guardem les inscripcions
            for($i = 0; $i < $QuantesEntrades; $i++){
            
                // Per cada inscripció, creo un objecte matrícula i marco com a reservat
                $OM = $MM->getEmptyObject($IdUsuari, $idCurs, $idSite);
                                
                // Si hi ha descompte, l'apliquem. 
                $PreuPagat = $OC[$CM->gnfnwt('Preu')];
                if($TipusPagament == $MM::PAGAMENT_INVITACIO) $PreuPagat = 0;
                if($DescompteAplicat > -1) {                                        
                    $PreuPagat = $CM->getPreuAplicantDescompte($OC, $DescompteAplicat);
                    $OM[$MM->gnfnwt('TipusReduccio')] = $DescompteAplicat;
                }
                 
                // Guardem l'import a la matrícula i el tipus de pagament
                $OM = $MM->setPreuMatricula($OM, $PreuPagat);
                $Import += $PreuPagat;
                $OM[$MM->gnfnwt('TipusPagament')] = $TipusPagament;

                //Marquem el nou estat de la matrícula segons el pagament que s'hagi fet
                $OM = $MM->setEstatByTipusPagament($OM);

                // Si és amb localitats, guardo les localitats
                if ( $CM->hasEscullLocalitats($OC) ) {
                    $OM = $MM->setLocalitat($OM, $Localitats[$i] );                    
                }

                // Si hi ha dades extres, les guardo
                if(isset($OC[$CM->gnfnwt('DadesExtres')]) && strlen($OC[$CM->gnfnwt('DadesExtres')]) > 0) $OM[$MM->gnfnwt('Comentari')] = $DadesExtres;
                                                            
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
            $RET['TIPUS_PAGAMENT'] = $TipusPagament;
            if( $TipusPagament == MatriculesModel::PAGAMENT_TARGETA ) {                
                $RET['TPV'] = $this->generaPeticioTPV($idMatriculaGrup, $Import, $idSite, $UrlDesti);
            } elseif ( $TipusPagament == MatriculesModel::PAGAMENT_PREAUTORITZACIO ){
                $RET['TPV'] = $this->generaPeticioTPV($idMatriculaGrup, $Import, $idSite, $UrlDesti);
            } elseif (  sizeof($Matricules) > 0 
                        && $TipusPagament != MatriculesModel::PAGAMENT_LLISTA_ESPERA 
                        && $TipusPagament != MatriculesModel::PAGAMENT_DATAFON ) {
                $this->EnviaEmailInscripcio($Matricules[0], $UM->getEmail($OU), array(self::TIPUS_RESGUARD_MAIL), $UrlDesti);                    
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
                        
        $Resposta = $this->generaResguard( $Encrypted_IdMatricula, $UrlDesti, 0);        
        $HTML = $Resposta["html"];
        $HTML_EMAIL = $HTML;
        
        if(!empty($email) > 0) { 
            
            $HTML_EMAIL = str_replace('@@EMAIL_SEND@@',  'Correu enviat correctament a: '.$email, $HTML_EMAIL);
            if($this->SendEmail($email, $Resposta['SiteId'], "Nova inscripció", $HTML_EMAIL)){
                $HTML = str_replace('@@DISPLAY_MAIL_COLOR@@',  '#EEEEEE', $HTML);
                $HTML = str_replace('@@DISPLAY_MAIL@@',  'none', $HTML);
                $HTML = str_replace('@@EMAIL_SEND@@',  'Correu enviat correctament a: '.$email, $HTML);
            } else {
                $HTML = str_replace('@@DISPLAY_MAIL_COLOR@@',  '#FFD0D0', $HTML);
                $HTML = str_replace('@@DISPLAY_MAIL@@',  'block', $HTML);
                $HTML = str_replace('@@EMAIL_SEND@@',  'Hi ha hagut algun error enviant el correu a: '.$email, $HTML);
            }
        }

        return $HTML;
        
    }

    /**
    * Reenviem el correu a la persona, un cop havent canviat el seu email 
    **/
    public function ReenviaEmailInscripcio( $Encrypted_IdMatricula, $UrlDesti ) {
                        
        $IdMatricula = $this->Decrypt($Encrypted_IdMatricula);
        $MM = new MatriculesModel();
        $OM = $MM->getMatriculaById($IdMatricula);
        $Email = $MM->getUserEmail($OM);
        $HTML = $this->EnviaEmailInscripcio( $Encrypted_IdMatricula, $Email, $Tipus = array( self::TIPUS_RESGUARD_MAIL ), $UrlDesti ); 
        
        // Si encara hi ha el display... l'ensenyem perquè es vegi que s'ha enviat el correu.
        $HTML = str_replace('@@DISPLAY_MAIL@@',  'block', $HTML);                                 
        return $HTML;
        
    }

    /**
    * Reenviem el correu a la persona, un cop havent canviat el seu email 
    **/
    public function ReenviaEmailInscripcioWeb( $IdUsuari, $idCurs ) {
        
        $MM = new MatriculesModel();
        $MatriculesUsuariCurs = $MM->getUsuariHasMatricula($idCurs, $IdUsuari, true);
        $GrupsMatricules = array();
        foreach($MatriculesUsuariCurs as $OM):
            $GM = $OM[$MM->gnfnwt('GrupMatricules')];
            $GrupsMatricules[$GM] = $GM;
        endforeach;        
        foreach($GrupsMatricules as $idMatricula):
            $MatriculaEncriptada = $this->Encrypt($idMatricula);
            $this->ReenviaEmailInscripcio($MatriculaEncriptada, '');
        endforeach;     

        return sizeof($MatriculesUsuariCurs);                                           
        
    }

    public function SendEmail($to, $idSite, $subject, $HTML) {
        $url = 'https://api.elasticemail.com/v2/email/send';

        // From, i From Name canviarà segons opció escollida
        $OO = new OptionsModel();
        $FromEmail = $OO->getOption("MAIL_FROM", $idSite);
        $FromName = $OO->getOption("MAIL_NAME", $idSite);        
        
        try{
                $post = array('from' => $FromEmail,
                'fromName' => $FromName,
                'apikey' => '882D1E9420DA8EFC9A20F712B96703AC6D9D06099C059D20325B91A467DB449A558C4DAD46C13DC2712D8132F35847D3',
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
                    throw new Exception($RES['error']);
                }                
                
                return true;
                
        }
        catch(Exception $ex){
            return false;            
        }        
    }

    /**
    * Funció que es crida quan es fa una nova reserva d'espai a través de l'Hospici
    */
    public function setReservaEspai($FormulariReservaEspai, $isNew){
        require_once DATABASEDIR . 'Tables/ReservaEspaisModel.php';

        // Treballem el que rebem de reserva espai, per adaptar-ho a la nostra base de dades. Els camps múltiples els convertim a @ i els arxius els guardem. 
        $REM = new ReservaEspaisModel();        
        
        $PrimerEspaiEscollit = $FormulariReservaEspai[$REM->gnfnwt(ReservaEspaisModel::EspaisSolicitats)][0];

        $FormulariReservaEspai = $REM->adaptFromFormFields($FormulariReservaEspai, $isNew);
        
        $ORE = $REM->insert($FormulariReservaEspai);        
        HelperForm_FileRenameFromTempToId(DOCUMENTS_RESERVAESPAIS_DIR, $REM->getReservaEspaiId($ORE, $REM->getReservaEspaiId($ORE) ) );

        //Envio un email a administració perquè puguin estar al cas que s'ha fet una reserva        
        $EM = new EspaisModel();        
        $OE = $EM->getEspaiDetall($PrimerEspaiEscollit);
        $idS = $EM->getSiteId($OE);
        
        $OM = new OptionsModel();
        $Email = $OM->getOption('MAIL_SECRETARIA', $idS);
        
        $Titol = $FormulariReservaEspai[$REM->gnfnwt(ReservaEspaisModel::Nom)];        
        $Codi = $REM->getCodi($ORE);        
        $Organitzadors = $FormulariReservaEspai[$REM->gnfnwt(ReservaEspaisModel::Organitzadors)];
        $HTML = "S'ha registrat una nova reserva d'espai amb el codi <b>{$Codi}</b> organitzada per <b>{$Organitzadors}</b> amb el títol <b>{$Titol}</b>  ";

        $this->SendEmail($Email, $idS, "Nova reserva d'espai", $HTML);

        return $ORE;

    }

    public function getEspaisDisponibles($idSite) {
        require_once DATABASEDIR . 'Tables/EspaisModel.php';
        $EM = new EspaisModel(); 
        return $EM->getEspaisDisponiblesSite($idSite);
    }

    public function Encrypt($id) { return HelperForm_Encrypt($id); }
    public function Decrypt($id) { return HelperForm_Decrypt($id); }
    
}

 ?>
