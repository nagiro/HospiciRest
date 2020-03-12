<?php 

// require_once BASEDIR.'/Database/Tables/UsuarisModel.php';

class DateController {

    public function __construct() {}
    
    static public function getDataLlarga($MySQLDate) {        
        list($any, $mes, $dia) = explode('-', $MySQLDate);
        $NomDia = date('w', mktime(0,0,0,$mes, $dia, $any));
        return self::getNomDiaLlarg($NomDia).', '.$dia.' '.self::getNomMesLlarg($mes).' de '.$any;
    }

    static public function getNomDiaLlarg($diaSetmana) {
        switch($diaSetmana) {
            case 0: return 'Diumenge'; break;
            case 1: return 'Dilluns'; break;
            case 2: return 'Dimarts'; break;
            case 3: return 'Dimecres'; break;
            case 4: return 'Dijous'; break;
            case 5: return 'Divendres'; break;
            case 6: return 'Dissabte'; break;
        }
    }

    static public function getNomMesLlarg($Mes) {
        switch($Mes) {
            case 1: return 'de gener'; break;
            case 2: return 'de febrer'; break;
            case 3: return 'de març'; break;
            case 4: return 'd\'abril'; break;
            case 5: return 'de maig'; break;
            case 6: return 'de juny'; break;
            case 7: return 'de juliol'; break;
            case 8: return 'd\'agost'; break;
            case 9: return 'de setembre'; break;
            case 10: return 'd\'octubre'; break;
            case 11: return 'de novembre'; break;
            case 12: return 'de desembre'; break;
        }        
    }

    
}


?>