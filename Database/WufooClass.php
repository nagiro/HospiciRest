<?php 

require_once BASEDIR."Database/DB.php";

class WufooClass {

    private $ModelObject = array();      //L'objecte que guarda les dades del formulari
    private $FormFields = array();   //Un objecte del tipus FormItemClass

    const FIELD_FORMULARI_AUDITORI = 'Field732';
    const FIELD_FORMULARI_GENERAL = 'Field526';
    const FORMULARI_AUDITORI = 'm1ewz7w703303sr';
    const FORMULARI_GENERAL = 'm1ffwvmw0yeoqc7';
    const WUFOOKEY = '42L1-CPPG-PK6O-GYKB:foostatic';

    /**
    * Afegim l'objecte de la taula on hi haurà les dades
    */
    
    private function doGetFields($QuinFormulari) {
        $curl = curl_init('https://casadecultura.wufoo.com/api/v3/forms/'.$QuinFormulari.'/fields.json');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERPWD, self::WUFOOKEY );
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);                          
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);                           
        curl_setopt($curl, CURLOPT_USERAGENT, 'Casa de Cultura');

        $response = curl_exec($curl);
        $resultStatus = curl_getinfo($curl);

        if($resultStatus['http_code'] == 200) {
            $json = json_decode($response);
            return json_encode($json, JSON_PRETTY_PRINT);
        } else {
            return array();
        }
    }

    public function getFieldLabels($QuinFormulari) {
        $Fields = json_decode($this->doGetFields($QuinFormulari), true);
        $Labels = array();
        foreach($Fields['Fields'] as $Field) {
            $Labels[$Field['ID']] = $Field['Title'];
        }
        return $Labels;
    }
    public function saveEntry( $Entry ) {
                
        $idActivitat = 0;

        if(isset($Entry[self::FIELD_FORMULARI_AUDITORI])) { $QuinFormulari = WufooClass::FORMULARI_AUDITORI; $idActivitat = $Entry[self::FIELD_FORMULARI_AUDITORI]; }
        if(isset($Entry[self::FIELD_FORMULARI_GENERAL]))  { $QuinFormulari = WufooClass::FORMULARI_GENERAL;  $idActivitat = $Entry[self::FIELD_FORMULARI_GENERAL]; }

        //Carrego els labels i ho converteixo tot a una estructura amb labels correctes
        $Labels = $this->getFieldLabels($QuinFormulari);
        $FormWithLabels = array();
        foreach($Entry as $Key => $Field) {
            if(array_key_exists($Key, $Labels)) {
                $FormWithLabels[ $Labels[$Key] ] = $Field;
            }            
        }

        // Guardo el formulari amb el codi d'activitat
        if($idActivitat > 0) file_put_contents(WEBFILESDIR . 'WufooFormEntries/'.$idActivitat.'.json', print_r($FormWithLabels, true) );

        //Hauria de guardar a la base de dades que ja he carregat el formulari detall
        $AM = new ActivitatsModel();
        $OA = $AM->getActivitatByIdObject($idActivitat);
        if(!empty($OA)) $AM->setWufooStatus(ActivitatsModel::WUFOO_CONTESTAT, $OA);

        return true;

    }    

}

?>