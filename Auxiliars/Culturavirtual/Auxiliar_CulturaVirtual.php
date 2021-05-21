<?php 
require_once DATABASEDIR . 'Tables/ActivitatsModel.php';


class Auxiliar_CulturaVirtual {

    public $Categoria = array(
        56 => array(3),
        46 => array(41)
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
        )

 */



    public function loadActivitatsFutures( $idS ) {
        $WQ = new WebQueries();
        $FileCSV = $this->ReadFileCSV( $idS );
        
        $ROWS = $WQ->getActivitatsFuturesPerXML( $idS );                
        $LlistatActivitats = $this->MarcoAccioAFer( $FileCSV, $this->AgrupoActivitatsPerData( $ROWS ) );

        foreach($LlistatActivitats as $Activitat) {                        

            $Imatge = $this->getImatgeUrl($Activitat['Imatge']);
            $ActivitatAGuardar = $this->addData($Activitat, $Imatge);                                    
            
            $idWordPress = false;              
            if( $Activitat['TmpAccio'] == 'add' ) $idWordPress = $this->curlToLoad( $ActivitatAGuardar );            
            elseif($ActivitatAGuardar['TmpAccio'] == 'update') $idWordPress = false;
            else $idWordPress = false;
                                                
            $FileCSV[$idWordPress] = $this->genCSVRow($idWordPress, $Activitat);            

        }

        // Guardem totes les línies noves que hem generat i les actualitzades
        $this->WriteFileCSV($idS, $FileCSV);

    }






    private function genCSVRow($id, $Activitat) {
        unset($Activitat['TmpAccio']);
        return array($id, hash('md5', json_encode($Activitat)), $Activitat['Dia']);
    }

    public function MarcoAccioAFer( $FileCSV, $LlistatActivitats ) {
        foreach($LlistatActivitats as $id => $Activitat) {                        
            // Si ja havíem entrat aquesta activitat
            if( isset( $FileCSV[$Activitat['ActivitatID'] ] ) ) {
                if( $this->SonIguales( $FileCSV[$Activitat['ActivitatID'] ] , $Activitat ) ) $LlistatActivitats[$id]['TmpAccio'] =  'update';
                else $LlistatActivitats[$id]['TmpAccio'] = 'nothing';
            } else {
                $LlistatActivitats[$id]['TmpAccio'] =  'add';
            }            
        }
        return $LlistatActivitats;
    }

    private function SonIguales($ActCSV, $Activitat ) {        
        unset($Activitat['TmpAccio']);
        return $ActCSV[1] == hash('md5', json_encode($Activitat));
    }

    /**
     * Format del fitxer
     * 0 => idActivitat, 1 => Estat, 2 => Data
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
     * 0 => idActivitat, 1 => hash, 2 => DataFi
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
            print_r($result);
            return $result['id'];
        } else {            
            print_r($result);
            return false;
        }
    
    }
}

?>