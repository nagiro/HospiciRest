<?php 
require_once DATABASEDIR . 'Tables/ActivitatsModel.php';


class Auxiliar_CulturaVirtual {

    public $Categoria = array(
        56 => array(3),         // Música
        46 => array(41),        // Exposició
        64 => array(54),        // Activitat en Línia
        66 => array(0)         // No cal penjar-la
    );

    public $TipusActivitat = array(
        98  => array(55),
        102 => array(55),
        103 => array(3),
        105 => array(55),
        106 => array(41),
        108 => array(4, 60),
        109 => array(55, 62),
        113 => array(39, 62),
        114 => array(4, 60),
        116 => array(55, 62),
        118 => array(55),
        119 => array(50, 62),	       
        120 => array(55),
        121 => array(55, 62),	
        122 => array(55),	
        123 => array(55),	
        124 => array(55),	
        126 => array(55),	
        128 => array(41),	
        129 => array(60),	
        130 => array(55, 62),	
        131 => array(55, 62),	
        132 => array(55, 62),	
        133 => array(55, 62),	
        134 => array(55, 62),	
        135 => array(55, 62)        
    );

    public function __construct() {}

/**
 *  [0] => Array
        (
            [ActivitatID] => 25944
            [HorarisID] => 102368
            [tMig] => ZINC - Girona a Cappella
            [dMig] => 
            [Dia] => 2021-05-14,
            [HoraInici] => 10:10:00,
            [Cicles_CicleID] => 594
            [Imatge] => /images/activitats/A-25944-.jpg
            [TipusActivitat_idTipusActivitat] => 103
            [Categories] => 49@56
            [TmpAccio] => 'add, update'
            [DataInicial] => ''
            [DataFinal] => ''
        )

 */



    public function loadActivitatsFutures( $idS ) {
        $WQ = new WebQueries();
        
        // Carrego l'arxiu CSV on hi ah l'històric dels registres carregats ( id, hash, data )
        $FileCSV = $this->ReadFileCSV( $idS );
        
        // Carrego les activitats del Site en qüestió
        $ROWS = $WQ->getActivitatsFuturesPerXML( $idS );                
        //Converteixo el format agrupant per dates i marco si cal fer add o update segons arxiu CSV
        $LlistatActivitats = $this->MarcoAccioAFer( $FileCSV, $this->AgrupoActivitatsPerData( $ROWS ) );

        // Per cada activitat
        foreach($LlistatActivitats as $idActivitat => $Activitat) {                        

            if( $Activitat['TmpAccio'] != 'nothing' ) {
                
                // Poso URL imatge i converteixo a format WP les dades
                $Imatge = $this->getImatgeUrl($Activitat['Imatge']);
                $ActivitatAGuardar = $this->addData($Activitat, $Imatge);                                    
                
                // Carrego l'acció que toqui al WP
                $idWordPress = false;    
                echo implode(' | ', array($Activitat['TmpAccio'], $Activitat['tMig']));
                if( $Activitat['TmpAccio'] == 'add' ) $idWordPress = $this->curlToLoad( $ActivitatAGuardar );            
                elseif($Activitat['TmpAccio'] == 'update') $idWordPress = false;
                else $idWordPress = false;
                                                    
                // Guardo l'activitat que acabo de carregar al CSV amb el nou hash i data si l'acció no és nothing
                if( $idWordPress > 0 ) { 
                    $FileCSV[$idActivitat] = $this->genCSVRow($idWordPress, $Activitat);            
                }
            }
        }

        // Guardem totes les línies noves que hem generat i les actualitzades
        $this->WriteFileCSV($idS, $FileCSV);

    }






    /**
     * Funció que genera una línia CSV
     */
    private function genCSVRow($idWP, $Activitat) {
        unset($Activitat['TmpAccio']);
        return array($Activitat['ActivitatID'], $idWP, hash('md5', json_encode($Activitat)), $Activitat['Dia']);
    }

     /**
     * Marco quina acció cal fer, si update, add o nothing
     * Si el hash és diferent, farem un update, si no existeix un add i si el hash és igual i existeix, no fem res. 
     */
    public function MarcoAccioAFer( $FileCSV, $LlistatActivitats ) {
        foreach($LlistatActivitats as $id => $Activitat) {                        
            // Si ja havíem entrat aquesta activitat
            if( isset( $FileCSV[ $id ] ) ) {
                if( $this->SonDiferents( $FileCSV[ $id ] , $Activitat ) ) $LlistatActivitats[$id]['TmpAccio'] =  'update';
                else $LlistatActivitats[$id]['TmpAccio'] = 'nothing';
            } else {
                $LlistatActivitats[$id]['TmpAccio'] =  'add';
            }            
        }
        return $LlistatActivitats;
    }

    /**
     * Comprar dos hash per veure si són iguals o no
     */
    private function SonDiferents($ActCSV, $Activitat ) {        
        unset($Activitat['TmpAccio']);
        $hash = hash('md5', json_encode($Activitat));
        return $ActCSV[2] != $hash;
    }

    /**
     * Format del fitxer
     * 0 => idActivitat Hospici, 1 => idActivitat WP, 2 => Estat, 3 => Data
     */
    private function ReadFileCSV( $idS ){
        $RET = array();
        $file = __DIR__ . "/{$idS}-activitats.csv";

        if(!is_file($file)){ file_put_contents($file, ""); }

        $gestor = fopen($file, "r+");
        if ($gestor !== FALSE) {
            while (($datos = fgetcsv($gestor, 1000, ",")) !== FALSE) {
                $RET[$datos[0]] = $datos;                                
            }
            fclose($gestor);
        } 
        return $RET;
    }

    /**
     * Format del fitxer
     * 0 => idActivitat, 1 => hash, 2 => DataInici
     */
    private function WriteFileCSV( $idS, $Data ){
        $RET = array();
        $fp = fopen(__DIR__ . "/{$idS}-activitats.csv", 'w');
        if($fp) {
            foreach ($Data as $fields) { fputcsv($fp, $fields); }        
            fclose($fp);
        }
        return $RET;
    }

    /**
     * Converteix les dades de BDD a WP
     */
    public function addData($R, $Imatge) {        
        return array(
            'title' => $R['tMig'],
            'description' =>  $R['dMig'],
            'start_date' =>  $R['DataInicial'],
            'end_date' => $R['DataFinal'],
            'categories' => $this->ConvertCategories($R['Categories'], $R['TipusActivitat_idTipusActivitat']),
            'website' => "https://www.casadecultura.cat/detall/{$R['ActivitatID']}/" . urlencode($R['tMig']),
            'cost' => 0,
            'venue' => 284,
            'image' => $Imatge
        );
    }

    public function getImatgeUrl($Imatge) {
        $ImatgeParts = explode('.', $Imatge);
        $ImatgePartsNumber = sizeof($ImatgeParts);
        $ImatgeParts[$ImatgePartsNumber - 2] .= 'L';
        // $Imatge = 'http://culturavirtual.ddgi.cat' . implode('.', $ImatgeParts);
        // $Imatge = 'http://culturavirtual.ddgi.cat/testapi/testfoto.jpg';
        
        return 'http://www.casadecultura.cat' . implode('.', $ImatgeParts);
    }

    public function AgrupoActivitatsPerData( $ROWS ) {
        $RET = array();
        foreach($ROWS as $R):                                    
            
            if( isset($RET[ $R['ActivitatID'] ])){
                $RET[ $R['ActivitatID'] ]['DataFinal'] = $R['Dia'].' '.$R['HoraInici'];
            }
            else {
                $RET[ $R['ActivitatID'] ] = $R;
                $RET[ $R['ActivitatID'] ]['DataInicial'] = $R['Dia'].' '.$R['HoraInici'];
                $RET[ $R['ActivitatID'] ]['DataFinal'] = $R['Dia'].' '.$R['HoraInici'];
            }
            
        endforeach;
        return $RET;
    }

    public function ConvertCategories($Categories, $TipusActivitat) {
        $Cats = array();
        foreach(explode("@", $Categories) as $C):
            if(isset($this->Categories[$C])){
                foreach($this->Categories[$C] as $Cat) $Cats[] = $Cat;
            }
        endforeach;
        if( isset( $this->TipusActivitat[$TipusActivitat] ) ) {
            foreach($this->TipusActivitat[$TipusActivitat] as $C) $Cats[] = $C;
        }
        return $Cats;
    }

    public function curlToLoad($ActivitatAGuardar) {

        $username = 'test';
        $password = 'n7Qb UMF4 tFHX DBtF KlKm Cuww';

        // end point
        $process = curl_init('http://culturavirtual.ddgi.cat/wp-json/tribe/events/v1/events/');                        
        
        $data_string = json_encode($ActivitatAGuardar);

        curl_setopt($process, CURLOPT_USERPWD, $username . ":" . $password);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_POST, 1);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($process, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($process, CURLOPT_HTTPHEADER, array(                                                                          
            'Content-Type: application/json',                                                                                
            'Content-Length: ' . strlen($data_string))                                                                       
        );                        

        $return = curl_exec($process);
        curl_close($process);
        $result = json_decode($return, true);
        
        if( isset($result['id']) ) {                                    
            return $result['id'];
        } else {                 
            // print_r($result);       
            return false;
        }
    
    }
}

?>