<?php 

require_once DATABASEDIR . 'Queries/WebQueries.php';
require_once DATABASEDIR . 'Tables/CursosModel.php';
require_once CONTROLLERSDIR.'FileController.php';

class WebController
{

    public $WebQueries; 
    public $DataAvui;
    public $DataFi;
    public $DataFiAct;

    public function __construct() {
        $this->WebQueries = new WebQueries();
        $this->setNewDate(date('Y-m-d', time()));        
    }

    public function setNewDate($DataAvui) {
        $this->DataAvui = $DataAvui;
        $this->DataFi = date('Y-m-d', strtotime($this->DataAvui." +6 month"));                
        $this->DataFiAct = date('Y-m-d', strtotime($this->DataAvui." +2 month"));                
    }

    public function validateDate($Date) {
        $Parts = explode("-", $Date);                
        return checkdate($Parts[1], $Parts[2], $Parts[0]); 
    }
        
    public function getMenu() { return $this->WebQueries->getMenu(); }

    public function viewHome() {                
        $E["Exposicions"] =         $this->WebQueries->getActivitatsHome(array(46), $this->DataAvui, $this->DataFi, 1);
        $E["Musica"] =              $this->WebQueries->getActivitatsHome(array(56), $this->DataAvui, $this->DataFi, 1);
        $E["Petita"] =              $this->WebQueries->getActivitatsHome(array(59), $this->DataAvui, $this->DataFi, 1);
        $E["Cicles"] =              $this->WebQueries->getCiclesHome(0, $this->DataAvui, $this->DataFi);
        $E["ProperesActivitats"] =  $this->WebQueries->getActivitatsHome(array(), $this->DataAvui, $this->DataFiAct, 1);
        $E["Noticies"] =            $this->WebQueries->getNoticiesHome(1, $this->DataAvui);
        $E["Promocions"] =          $this->WebQueries->getPromocions();   
        $E["Breadcumb"] =           array(array('Titol'=>'Inici', "Link"=> '/')); 
        $E["Menu"]      =           $this->getMenu();        

        // Trec les activitats de properes activitats que tenen més d'un dia o que són cicles. 
        foreach($E["ProperesActivitats"] as $id => $EL) {
            if($E["ProperesActivitats"][$id]["Dia"] != $E["ProperesActivitats"][$id]["DiaMax"]) {
                unset($E["ProperesActivitats"][$id]);
            }
        }

        return $E;
    }

    public function viewCicles($idC) {
        // Si passo un cicle > 1, mostro les activitats d'aquest cicle. 
        //  Altrament, mostro només els cicles actius         
        $C = array();
        $EsUnCicle = false;
        $Datai = '';
        $Dataf = '';
                
        if( $idC > 1 ) {             
            $C[] = $idC;            
            $EsUnCicle = true;
            $Datai = '';
            $Dataf = '';
        } else {
            $idC = 0;   //Marquem 0 perquè volem veure tots els cicles disponibles
            //$C es carrega a partir dels cicles que hi ha disponibles
            $EsUnCicle = false;
            $Datai = $this->DataAvui;
            $Dataf = $this->DataFi;
        }
        
        $EXTRES["Cicles"] = $this->WebQueries->getCiclesHome($idC, $Datai, $Dataf);
        if(sizeof($EXTRES['Cicles']) > 0) {
            foreach($EXTRES["Cicles"] as $K => $Row) { 
                $C[$Row["idCicle"]] = $Row["idCicle"];                                
                // Miro si hi ha un pdf i si hi és el carrego
                $PdfCicle = 'C-'.$Row["idCicle"].'-PDF.pdf';
                $EXTRES['Cicles'][$K]['tmp_PDF'] = ( is_file( OLD_BASEDIR_IMG_CICLES . $PdfCicle ) ) ? IMATGES_URL_CICLES . $PdfCicle : '';                
            }
            $EXTRES["Activitats"] = $this->WebQueries->getActivitatsHome(array(), '', '', 1, 1, $C);         
                                
            $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> '/')); 
            if( $EsUnCicle ){ 
                $EXTRES["Breadcumb"][] =     array('Titol' => 'Tots els cicles', "Link" => '/cicles/0/' . $this->aUrl('Tots els cicles'));    
                $EXTRES["Breadcumb"][] =     array('Titol' => $EXTRES['Cicles'][0]['NomActivitat'], "Link" => '/cicles/' . $idC . '/' . $this->aUrl($EXTRES['Cicles'][0]['NomActivitat']));
            } else {
                $EXTRES["Breadcumb"][] =     array('Titol' => 'Tots els cicles', "Link" => array('/cicles/0/' . $this->aUrl('Tots els cicles')));
            }

            if($idC == 0) {
                $EXTRES['Promocions'] = $this->WebQueries->getPromocions(true, 'CICLES', 'Tots els nostres cicles', 'C', 0 );                
            } else {            
                $C = $EXTRES['Cicles'][0];
                $EXTRES['Promocions'] = $this->WebQueries->getPromocions(true, 'CICLES', $C['NomActivitat'], 'C', $idC );
            }
        } else {

            $EXTRES['Promocions'] = $this->WebQueries->getPromocions(true, 'CICLES', 'Tots els nostres cicles', 'C', 0 );                
            // $EXTRES['Errors'] = array('La pàgina on accedeixes no existeix o no és visible. <br />Si vols pots tornar a l\'inici clicant <a href="/">aquí</a>');

        }

        $EXTRES['Menu'] = $this->getMenu();

        return $EXTRES;

    }


    /**
     *   url: UrlArrayParts
    */
    public function getUrlToFilters( $URL ) {
        //Url[0] = 'activitats'
        //Url[1] = Tipus enviament ( Pot ser FILTRE o CATEGORIA o TIPUS o DATA )
        //Url[2] = Dada ( Si és CATEGORIA => IdCategoria || Si és Tipus => idTipus || Si és Data => Data || Filtre => JSON(_BASE64) ) 
        
        $FILTRES = array();

        if(isset($URL[0]) && $URL[0] == 'activitats'){
            if(     isset($URL[1]) 
                &&  in_array($URL[1], array('filtre', 'categoria', 'tipus', 'data', 'text')) 
                &&  isset($URL[2]) 
                &&  strlen($URL[2]) > 0 ) {

                switch( $URL[1] ) {
                    case 'filtre': 
                        $DadesPerFiltrar = json_decode($URL[2], true);
                        foreach($DadesPerFiltrar as $K => $V):
                            $FILTRES[] = $V;
                        endforeach;
                    break;
                    case 'categoria':                
                        $FILTRES[] = array('type' => 'CATEGORIA', 'key' => $URL[2]);
                    break;
                    case 'tipus':
                        $FILTRES[] = array('type' => 'TAG_VINCULAT', 'key' => $URL[2]);
                    break;
                    case 'data':
                        $FILTRES[] = array('type' => 'DATA_INICIAL', 'key' => $URL[2]);
                    break;
                    case 'text':
                        $FILTRES[] = array('type' => 'TEXT', 'key' => $URL[2]);
                    break;

                }
            } else if( !isset($URL[1]) && !isset($URL[2]) ) {
                $FILTRES[] = array('type' => 'DATA_INICIAL', 'key' => Date('Y-m-d', time()));
            }
        }

        return $FILTRES;

    }

    private function hCercaArray($agulla, $palla, $NomCamp) {
        foreach($palla as $V){
            if($V[$NomCamp] == $agulla) return $V;
        }
        return null;
    }

    function aURL($cadena) {
        $eliminar=array("!","¡","?","¿","‘","\"","$","(",")",".",":",";","_","/","\\","\$","%","@","#",",", "«", "»");
        $buscados=array(" ","á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","à","è","ì","ò","ù","À","È","Ì","Ò","Ù");
        $sustitut=array("-","a","e","i","o","u","a","e","i","o","u","n","n","u","a","e","i","o","u","A","E","I","O","U");
        $final=strtolower(str_replace($buscados,$sustitut,str_replace($eliminar,"",$cadena)));
        $final=str_replace("–","-",$final);
        $final=str_replace("–","-",$final);
        return (strlen($final)>50) ? substr($final,0,strrpos(substr($final,0,50),"-")):$final;
    }    

    /**
     * 
     * $Filtres 
     * Url[0] = 'activitats'
     * Url[1] = Tipus enviament ( Pot ser FILTRE o CATEGORIA o TIPUS o DATA o TEXT ) Si vull buscar Text
     * Url[2] = Dada ( Si és CATEGORIA => IdCategoria || Si és Tipus => idTipus || Si és Data => Data || Filtre => JSON(_BASE64) )        
     * 
     */    

     public function viewActivitats( $Filtres ) {
        
        $CategoriesVinculades = array(1 => 'Artística', 2 => 'Curs', 3 => 'Familiar', 4 => 'Científica', 5 => 'Exposició', 6 => 'Humanitats', 7 => 'Música', 8 => 'Conferència', 11 => 'Tecnològica');
        $TotsElsTipus = true;
        $CategoriesArray = array();
        $TagsVinculatsArray = array();
        $ParaulesCerca = array();        

        $EXTRES["TipusActivitats"]["Tipus"] = $this->WebQueries->getTipusActivitats();                      
        $EXTRES["TagsActivitats"]["Tags"] = $this->WebQueries->getTagsActivitats();        
        
        $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> '/')); 

        foreach( $Filtres as $F ):
            
            if ($F['type'] == 'CATEGORIA') { 
                $CategoriesArray[] = $F['key'];

                $F['Text'] = $this->hCercaArray($F['key'], $EXTRES['TipusActivitats']["Tipus"], 'idTipus')['tipusDesc'];                                 
                $EXTRES["Breadcumb"][] = array('Titol' => $F['Text'], "Link" => "/activitats/categoria/{$F['key']}/" . $this->aUrl($F['Text']) );                 
            }

            if ($F['type'] == 'DATA_INICIAL') {
                if($this->validateDate($F['key'])) { 
                    $this->setNewDate($F['key']); 
                    $this->DataFi = $this->DataAvui;                     
                }
                
                $DP = explode("-", $this->DataAvui);
                $F['Text'] = "Activitats el dia " . implode("/", array( $DP[2], $DP[1], $DP[0] ));
                $EXTRES["Breadcumb"][] = array('Titol' => $F['Text'], "Link" => "/activitats/data/{$F['key']}" ); 
            }

            if ($F['type'] == 'TAG_VINCULAT') { 
                $TagsVinculatsArray[] = $F['key'];
                
                $F['Text'] = 'Activitats amb l\'etiqueta "' . (isset($CategoriesVinculades[$F['key']]) ? $CategoriesVinculades[$F['key']] : "" ) . '"';
                $EXTRES["Breadcumb"][] = array('Titol' => $F['Text'], "Link" => "/activitats/tipus/{$F['key']}/" . $this->aUrl($F['Text']) );
                
            }

            // També faltarà afegir-hi la cerca a les pàgines que fem nosaltres
            if ($F['type'] == 'TEXT'){ 
                $ParaulesCerca = explode(' ', $F['key']);
                $F['Text'] = "Activitats amb les paraules \"{$F['key']}\"";
                $EXTRES["Breadcumb"][] = array('Titol' => $F['Text'], "Link" => "/activitats/text/" . urlencode($F['key']) );
            }

            $EXTRES['FiltresAplicats'][] = $F;
        endforeach;                                                              

        if(sizeof($ParaulesCerca) > 0) $EXTRES['Nodes'] = $this->WebQueries->getNodesCerca( $ParaulesCerca );                                            
        $EXTRES['Activitats'] = $this->WebQueries->getActivitatsHome( $CategoriesArray, $this->DataAvui, $this->DataFi, 1, true, array(), $TagsVinculatsArray, $ParaulesCerca );
        
        $Text = $EXTRES['Breadcumb'][sizeof($EXTRES['Breadcumb']) - 1]['Titol'];
        $EXTRES['Promocions'] = $this->WebQueries->getPromocions(true, 'CERCA', $Text, 'A', 0 );                
        
        $EXTRES['Menu'] = $this->getMenu();

        return $EXTRES;
    }

    /**
    * Pot ser el detall d'una activitat o d'un curs
    * SiteIdAdminAuth: És el site passat pel token que ha d'encaixar amb el de l'activitat en qüestió
    */    
    public function viewDetall( $idA , $idCurs, $SiteIdAdminAuth, $Token, $isSiteExtern ) {        

        $EXTRES = array('Activitat' => array(), 'Curs' => array(), 'Token' => array($SiteIdAdminAuth, $Token) );
        $IsCurs = $idCurs > 0;
        $IsAct = $idA > 0;
        $isAdmin = false;

        if( $IsAct ) $EXTRES["Activitat"]     = $this->WebQueries->getActivitatsDetall( $idA );
        elseif( $IsCurs > 0 ) $EXTRES["Curs"] = $this->WebQueries->getCursDetall( $idCurs );                
        
        if( !empty($EXTRES["Curs"]) || !empty($EXTRES['Activitat']) ) {

            if( $IsAct ) $isAdmin = ($EXTRES["Activitat"][0]['ACTIVITATS_SiteId'] == $SiteIdAdminAuth);
            elseif( $IsCurs > 0 ) $isAdmin = ($EXTRES["Curs"]['CURSOS_SiteId'] == $SiteIdAdminAuth);
                        
            $EXTRES['Horaris'] = ($IsAct) ? $this->WebQueries->getHorarisActivitatDetall( $idA ) : array();
        
            $Nom = ( $IsAct ) ? $EXTRES['Activitat'][0]['ACTIVITATS_TitolMig'] : $EXTRES['Curs']['CURSOS_TitolCurs'];
            $idCicle = ( $IsAct ) ? $EXTRES["Activitat"][0]["ACTIVITATS_CiclesCicleId"] : 0;

            // Miro si té un pdf associat
            if( $IsAct ) {
                $PDF = IMATGES_URL_ACTIVITATS . "A-{$EXTRES['Activitat'][0]["ACTIVITATS_ActivitatId"]}-PDF.pdf" ;
                $PDF_EXIST = is_file( OLD_BASEDIR_IMG_ACT . "A-{$EXTRES['Activitat'][0]["ACTIVITATS_ActivitatId"]}-PDF.pdf" );
                if( $PDF_EXIST ) $EXTRES["Activitat"][0]["ACTIVITATS_Pdf"] = $PDF;
                else $EXTRES["Activitat"][0]["ACTIVITATS_Pdf"] = "";
            } else if ( $IsCurs ) {
                $PDF = IMATGES_URL_CURSOS . "C-{$EXTRES['Curs']["CURSOS_IdCurs"]}-PDF.pdf" ;
                $PDF_EXIST = is_file( OLD_BASEDIR_IMG_CUR . "C-{$EXTRES['Curs']["CURSOS_IdCurs"]}-PDF.pdf" );
                if( $PDF_EXIST ) $EXTRES["Curs"]["CURSOS_Pdf"] = $PDF;
                else $EXTRES["Curs"]["CURSOS_Pdf"] = "";
            }

            
            $ArrayCicles = array($idCicle);
            if( ! ($idCicle > 0) ) $ArrayCicles = array();            
            
            /* ACTIVITATS RELACIONADES Si entro a consultar un cicle concret, carrego les activitats relacionades */

            if(sizeof($EXTRES["Activitat"]) > 0 && $idCicle > 1)
                    $EXTRES["ActivitatsRelacionades"] = $this->WebQueries->getActivitatsHome( array(),'', '', 1, 1, $ArrayCicles);
            else    $EXTRES["ActivitatsRelacionades"] = array();
                                
            if( ! $isSiteExtern ) $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> '/')); 
            
            $NOM_CICLE = '';
            
            /* BREADCUMB Si hem escollit un cicle carrego el següent breadcumb */
            
            if($idCicle > 1){                        

                $Cicles = $this->WebQueries->getCiclesHome( $idCicle );   
                $NOM_CICLE = (empty($Cicles[0]['NomActivitat']))?$Cicles[0]['NomActivitatIntern'] : $Cicles[0]['NomActivitat'];                        

                $EXTRES["Breadcumb"][] =     array('Titol' => 'Tots els cicles', "Link" => '/cicles/0/' . $this->aUrl('Tots els cicles')); 
                $EXTRES["Breadcumb"][] =     array('Titol' => $NOM_CICLE, "Link" => '/cicles/' . $idCicle . '/' . $this->aUrl($NOM_CICLE)); 
                
            } else {

                /* BREADCUMB Si volem veure tots els cicles */

                if ( $IsAct ) $EXTRES["Breadcumb"][] =     array('Titol' => 'Totes les activitats', "Link" => '/activitats/0/' .  $this->aUrl('Totes les activitats')); 
                
            }
            $EXTRES["Breadcumb"][] = ( $IsAct ) ? array('Titol' => $Nom, "Link" => '/activitats/' . $idA . '/' . $this->aUrl($Nom)) :  array('Titol' => $Nom, "Link" => '/inscripcio/' . $idCurs . '/' . $this->aUrl($Nom));

            /* ENTRADES Carrego el curs si està habilitat */
            $CM = new CursosModel();                        
            $CursObject = ( $IsAct ) ? $CM->getRowActivitatId( $idA ) : $EXTRES['Curs'];
            if(!empty($CursObject)) { 

                require_once DATABASEDIR . 'Tables/SitesModel.php';
                $SM = new SitesModel();

                $EXTRES['Curs'] = array($CursObject);
                $EXTRES['Descomptes'] = $CM->getDescomptes($CursObject, $isAdmin);
                $EXTRES['Teatre'] = $CM->getTeatre($CursObject);
                $EXTRES['SeientsOcupats'] = $CM->getSeientsOcupats($CursObject);
                $EXTRES['Site'] = $SM->getById( $CursObject[$CM->gnfnwt('SiteId')]);
                
                if($isSiteExtern) {
                    // No cal perquè ja passo sempre el SITE a sobre
                    // require_once DATABASEDIR . 'Tables/SitesModel.php';
                    // $SM = new SitesModel();
                    // $EXTRES['SiteNom'] = $SM->loadNom($CursObject[$CM->gnfnwt('SiteId')]);                    
                }
                
            }
            else $EXTRES['Curs'] = array();                                         
            
            $EXTRES['Promocions'] = ( $IsAct ) ? $this->WebQueries->getPromocions(true, $Nom, $NOM_CICLE, 'A', $idA ) :  $this->WebQueries->getPromocions(true, $Nom, '', 'A', $idCurs );                                

        } else {

            /* La pàgina no és visible perquè no he trobat l'activitat o cicle */

            $EXTRES['Promocions'] = $this->WebQueries->getPromocions(true, '', '', 'A', 0 );                
            $EXTRES['Errors'] = array('La pàgina on accedeixes no existeix o no és visible.');

        }
        
        $EXTRES['Menu'] = $this->getMenu();
                
        return $EXTRES;
    }

    public function viewPagina( $idN ) {
        
        $R = $this->getMenu();                                        
        $EXTRES["Pagina"] = array();
        
        // Busco quin és l'índex del node
        $NodeIndex = -1;
        $NodeSons = array();
        
        foreach($R as $K => $Node) {
            $NodeIndex = ($Node['Nodes_idNodes'] == $idN) ? $K : $NodeIndex; 
            if ( $Node['Nodes_idPare'] == $idN ) { $NodeSons[] = $Node; } 
        }                

        if($NodeIndex >= 0) {

            /* Carrego els fills d'aquesta pàgina per si n'hi ha més... */

            $EXTRES["Pagina"] = $R[$NodeIndex];
            $EXTRES["Fills"] = $NodeSons;                
                        
            /* Genero el breadcrumb */
            
            $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> '/'));             
            $Pares = array($NodeIndex);                                 // Pares serà un node que és l'actual                   
            for($i = $R[$NodeIndex]['Nodes_Nivell']; $i >= 0; $i--) {   // Agafo els nodes des de l'actual fins l'arrel
                foreach($R as $id => $NodeObject) {                     // Per cada node que trobo el guardo a Pares i això serà el breadcumb.
                    $IndexNivellActual = end($Pares);
                    if( $NodeObject['Nodes_idNodes'] == $R[$IndexNivellActual]['Nodes_idPare'] ):
                        $Pares[] = $id;
                    endif;
                }                
            }                        
            $Pares = array_reverse($Pares);
            foreach($Pares as $IndexNode) {
                $EXTRES["Breadcumb"][] =     array('Titol' => $R[$IndexNode]['Nodes_TitolMenu'], "Link" => '/pagina/' . $R[$IndexNode]['Nodes_idNodes'] . '/' . $this->aUrl($R[$IndexNode]['Nodes_TitolMenu'])); 
            }

            /* SI ÉS UN PHP */

            if($EXTRES["Pagina"]['Nodes_isPhp'] == 1) {
                $EXTRES["Pagina"]['Nodes_Html'] = $this->get_include_contents( AUXDIR . $EXTRES["Pagina"]['Nodes_Html']); 
            }
            
        } else {

            $EXTRES['Errors'] = array('La pàgina on accedeixes no existeix o no és visible.<br />Si vols pots tornar a l\'inici clicant <a href="/">aquí</a>');

        }
        
        $EXTRES['Promocions'] = $this->WebQueries->getPromocions(true, '', '', 'A', 0 );                
        $EXTRES['Menu'] = $this->getMenu();

        return $EXTRES;
    }

    /**
     * 
     * $Filtres 
     * Url[0] = 'activitats'
     * Url[1] = Tipus enviament ( Pot ser FILTRE o CATEGORIA o TIPUS o DATA o TEXT ) Si vull buscar Text
     * Url[2] = Dada ( Si és CATEGORIA => IdCategoria || Si és Tipus => idTipus || Si és Data => Data || Filtre => JSON(_BASE64) )        
     * 
     */    

    public function viewCursosSites( $idSite ) {
                
        require_once DATABASEDIR . 'Tables/SitesModel.php';

        $CM = new CursosModel();
        $SM = new SitesModel();
        $EXTRES['LlistatCursos'] = $CM->getLlistatCursosWeb($idSite);
        $EXTRES['Site'] = $SM->getById($idSite);


        return $EXTRES;
    }

    function get_include_contents($filename) {         
        if(is_file($filename)){
            ob_start();                                
            include $filename;                        
            return ob_get_clean();            
        } else return "Hi ha hagut algun error a aquesta pàgina i no la puc carregar.";
    }

    public function viewCalendari( ) {
        
        $R = $this->getMenu();                                        
        $EXTRES["Calendari"] = array();                
        
        $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> '/')); 
        $EXTRES["Breadcumb"][] =       array('Titol'=>'Calendari', "Link"=> '/calendari'); 

        return $EXTRES;
    }    

    public function getActivitatsDiaValidar() {
        $CM = new CursosModel();        
        return $CM->getTodayCursosAndMatricules();
    }



 }

 ?>
