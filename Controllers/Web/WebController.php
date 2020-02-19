<?php 

require_once BASEDIR.'Database/Queries/WebQueries.php';
require_once CONTROLLERSDIR.'FileController.php';

class WebController
{

    public $WebQueries; 
    public $DataAvui;
    public $DataFi;

    public function __construct() {
        $this->WebQueries = new WebQueries();
        $this->setNewDate(date('Y-m-d', time()));        
    }

    public function setNewDate($DataAvui) {
        $this->DataAvui = $DataAvui;
        $this->DataFi = date('Y-m-d', strtotime($this->DataAvui." +4 month"));                
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
        $E["ProperesActivitats"] =  $this->WebQueries->getActivitatsHome(array(), $this->DataAvui, $this->DataFi, 1);
        $E["Noticies"] =            $this->WebQueries->getNoticiesHome(1, $this->DataAvui);
        $E["Promocions"] =          $this->WebQueries->getPromocions();   
        $E["Breadcumb"] =           array(array('Titol'=>'Inici', "Link"=> array('/home'))); 
        return $E;
    }

    public function viewCicles($idC) {
        //Si passo un cicle > 1, mostro les activitats d'aquest cicle. 
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
        foreach($EXTRES["Cicles"] as $Row) { $C[$Row["idCicle"]] = $Row["idCicle"]; }                                                                    
        $EXTRES["Activitats"] = $this->WebQueries->getActivitatsHome(array(), '', '', 1, 1, $C);         
                            
        $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> array('/home'))); 
        if( $EsUnCicle ){ 
            $EXTRES["Breadcumb"][] =     array('Titol' => 'Tots els cicles', "Link" => array('/cicles', 0, urlencode('Tots els cicles')));    
            $EXTRES["Breadcumb"][] =     array('Titol' => $EXTRES['Cicles'][0]['NomActivitat'], "Link" => array('/cicles', $idC, urlencode($EXTRES['Cicles'][0]['NomActivitat'])));
        } else {
            $EXTRES["Breadcumb"][] =     array('Titol' => 'Tots els cicles', "Link" => array('/cicles', 0, urlencode('Tots els cicles')));
        }

        return $EXTRES;

    }

    /**
     * $idT : Tipus d'activitat
     */
    public function viewActivitats( $idT = 0, $Filtres ) {

        $TotsElsTipus = true;
        $CategoriesArray = array();
        $TagsVinculatsArray = array();
        if($idT > 0) { 
            $TotsElsTipus = false;
            $CategoriesArray[] = $idT;
        }

        foreach( $Filtres as $F ):
            if ($F['type'] == 'DATA_INICIAL') if($this->validateDate($F['key'])) { $this->setNewDate($F['key']); $this->DataFi = $this->DataAvui; }
            if ($F['type'] == 'TAG_VINCULAT') $TagsVinculatsArray[] = $F['key'];
        endforeach;                
                                                                             
        $EXTRES['Activitats'] = $this->WebQueries->getActivitatsHome( array(), $this->DataAvui, $this->DataFi, 1, true, array(), $TagsVinculatsArray );
        $EXTRES["TipusActivitats"]["Tipus"] = $this->WebQueries->getTipusActivitats();
        $EXTRES["TagsActivitats"]["Tags"] = $this->WebQueries->getTagsActivitats();
        
        // Si és un tipus concret, busquem el nom únic del tipus per facilitar el mostrar-lo
        if(!$TotsElsTipus){
            foreach($EXTRES["TipusActivitats"]["Tipus"] as $K => $Tipus) {                            
                if( $Tipus['idTipus'] == $CategoriesArray[0] ) $EXTRES["TipusActivitatUnic"] = $Tipus;
            }
        }
        
        $Text = (isset($EXTRES['TipusActivitatUnic']['tipusDesc']))?$EXTRES['TipusActivitatUnic']['tipusDesc']:'';
        
        $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> array('/home'))); 
        if($TotsElsTipus) {
            $EXTRES["Breadcumb"][] =     array('Titol' => 'Totes les activitats', "Link" => array('/activitats', 0, urlencode('Totes les activitats'))); 
        } else {
           $EXTRES["Breadcumb"][] =     array('Titol' => $Text, "Link" => array('/activitats', $idT, urlencode($Text)));
        }
                
        return $EXTRES;
    }

    public function viewDetall( $idA ) {

        $EXTRES["Activitat"] = $this->WebQueries->getActivitatsDetall( $idA );                    
        $EXTRES['Horaris'] = $this->WebQueries->getHorarisActivitatDetall( $idA );                      

        $Nom = $EXTRES['Activitat'][0]['Activitats_TitolMig'];
        
        $idC = $EXTRES["Activitat"][0]["Activitats_CiclesCicleId"];
        $ArrayCicles = array($idC);
        if( ! ($idC > 0) ) $ArrayCicles = array();
        
        // Si idC == 1, és tots els cicles... no hi ha activitats vinculades

        if(sizeof($EXTRES["Activitat"]) > 0 && $idC > 1)
                $EXTRES["ActivitatsRelacionades"] = $this->WebQueries->getActivitatsHome( array(),'', '', 1, 1, $ArrayCicles);
        else    $EXTRES["ActivitatsRelacionades"] = array();
        
                
        $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> array('/home'))); 
        
        if($idC > 1){                        

            $Cicles = $this->WebQueries->getCiclesHome( $idC );   
            $NOM_CICLE = (empty($Cicles[0]['NomActivitat']))?$Cicles[0]['NomActivitatIntern'] : $Cicles[0]['NomActivitat'];                        

            $EXTRES["Breadcumb"][] =     array('Titol' => 'Tots els cicles', "Link" => array('/cicles', 0, urlencode('Tots els cicles'))); 
            $EXTRES["Breadcumb"][] =     array('Titol' => $NOM_CICLE, "Link" => array('/cicles', $idC, urlencode($NOM_CICLE))); 
            
        } else {

            $EXTRES["Breadcumb"][] =     array('Titol' => 'Totes les activitats', "Link" => array('/activitats', 0, urlencode('Totes les activitats'))); 
            
        }

        $EXTRES["Breadcumb"][] =     array('Titol' => $Nom, "Link" => array('/activitats', $idA, urlencode($Nom))); 
                
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
        $EXTRES["Pagina"] = $R[$NodeIndex];
        $EXTRES["Fills"] = $NodeSons;                
        
        $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> array('/home'))); 

        //Si hem trobat el node, mirem per cada nivell quin és el pare i entrem el breadcumb.
        $Pares = array($NodeIndex);
        
        if( $NodeIndex > -1 ) {
            for($i = $R[$NodeIndex]['Nodes_Nivell']; $i >= 0; $i--) {
                foreach($R as $id => $NodeObject) {
                    $IndexNivellActual = end($Pares);                    
                    if( $NodeObject['Nodes_idNodes'] == $R[$IndexNivellActual]['Nodes_idPare'] ):
                        $Pares[] = $id;
                    endif;
                }                
            }
        }    
        
        $Pares = array_reverse($Pares);

        foreach($Pares as $IndexNode) {
            $EXTRES["Breadcumb"][] =     array('Titol' => $R[$IndexNode]['Nodes_TitolMenu'], "Link" => array('/pagina', $R[$IndexNode]['Nodes_idNodes'], urlencode($R[$IndexNode]['Nodes_TitolMenu']))); 
        }

        return $EXTRES;
    }

    public function viewCalendari( ) {
        
        $R = $this->getMenu();                                        
        $EXTRES["Calendari"] = array();                
        
        $EXTRES["Breadcumb"] =       array(array('Titol'=>'Inici', "Link"=> array('/home'))); 
        $EXTRES["Breadcumb"][] =       array('Titol'=>'Calendari', "Link"=> array('/calendari')); 

        return $EXTRES;
    }    


 }

 ?>
