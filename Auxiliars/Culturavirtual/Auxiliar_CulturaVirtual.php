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
        )

 */
    public function loadActivitatsFutures() {
        $WQ = new WebQueries();
        $ROWS = $WQ->getActivitatsFuturesPerXML();
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

        foreach($RET as $R) {
            
            $ImatgeParts = explode('.', $R["Imatge"]);
            $ImatgePartsNumber = sizeof($ImatgeParts);
            $ImatgeParts[$ImatgePartsNumber - 2] .= 'L';
            $Imatge = 'http://www.casadecultura.cat' . implode('.', $ImatgeParts);
            // $Imatge = 'http://culturavirtual.ddgi.cat' . implode('.', $ImatgeParts);
            
            $data = array(
                'title' => $R['tMig'],
                'description' =>  $R['dMig'],
                'start_date' =>  $R['DataInicial'],
                'end_date' => $R['DataFinal'],
                'categories' => $this->ConvertCategories($R['Categories'], $R['TipusActivitat_idTipusActivitat']),
                'image' => $Imatge
            );
            print_r($data);
            $ok = $this->curlToLoad($data);

            echo "Codi: " . $ok;

        }
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

    public function curlToLoad($data) {

        $username = 'test';
        $password = 'n7Qb UMF4 tFHX DBtF KlKm Cuww';

        // end point
        $process = curl_init('http://culturavirtual.ddgi.cat/wp-json/tribe/events/v1/events/');                        
        
        $data_string = json_encode($data);


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
            // print_r($result);
            return $result['id'];
        } else {            
            print_r($result);
            return false;
        }
    
    }
}

?>