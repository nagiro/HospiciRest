<?php 

require_once BASEDIR."Database/DB.php";

class AvuiQueries extends BDD {

    public $E;
    public $Camps;

    public function __construct() {

        parent::__construct("","",array(),array());
                
    }

    public function MissatgesRows($idU, $idS) {

        $SQL = "
                SELECT  missatges.MissatgeID as MISSATGES_MISSATGEID, 
                        missatges.Titol as MISSATGES_TITOL, 
                        missatges.Text as MISSATGES_TEXT,
                        DATE_FORMAT(missatges.Date, '%d-%m-%Y') as MISSATGES_DATA,
                        missatges.isGlobal as MISSATGES_ISGLOBAL,
                        (Select concat(usuaris.Cog1,' ',usuaris.Cog2,', ',usuaris.Nom) from usuaris where usuaris.UsuariID = missatges.Usuaris_UsuariID) as MISSATGES_USUARI_NOM,
                        respostes.Data as RESPOSTES_DATA,
                        respostes.Text as RESPOSTES_TEXT,
                        (Select concat(usuaris.Cog1,' ',usuaris.Cog2,', ',usuaris.Nom) from usuaris where respostes.IdUsuari = usuaris.UsuariID) as RESPOSTES_USUARI_NOM
                FROM `missatges` LEFT JOIN respostes ON (missatges.MissatgeID = respostes.idPare )
                WHERE missatges.site_id = :idSite
                  AND missatges.actiu = 1
                  AND respostes.actiu = 1
                  AND respostes.idSite = :idSite2
                  AND (missatges.Date = :dataMissatges OR respostes.Data > :dataRespostes)
        ";        
        
        //$data = '2019-05-16';
        //$dataMissatges = '2019-05-13';
        $idSite = 1;        
        $data = date('Y-m-d', strtotime("now"));
        $dataMissatges = date('Y-m-d', strtotime("-3 days"));

        return $this->runQuery($SQL, 
                        array(
                                'idSite'=> $idSite, 
                                'idSite2'=> $idSite, 
                                'dataMissatges' => $data,
                                'dataRespostes' => $dataMissatges
                            ));

    }

    
    public function IncidenciesRows($idU, $idS) {

        $SQL = "
            SELECT 
                    incidencies.idIncidencia as INCIDENCIES_IDINCIDENCIA,
                    (Select concat(usuaris.Cog1,' ',usuaris.Cog2,', ',usuaris.Nom) from usuaris where usuaris.UsuariID = incidencies.quiinforma) as INCIDENCIES_QUIINFORMA,
                    incidencies.titol as INCIDENCIES_TITOL,
                    DATE_FORMAT(incidencies.dataalta, '%d-%m-%Y') as INCIDENCIES_DATAALTA
            FROM    incidencies 
            WHERE   incidencies.actiu = 1
            AND     incidencies.quiresol = :idU
            AND     incidencies.site_id = :idS
            AND     incidencies.estat < 30
        ";        
        
        $data = '2019-05-16';
        $dataMissatges = '2019-05-13';
        $idSite = 1;        
        $idUsuari = 1;
        //$data = date('Y-m-d', strtotime("now"));
        //$dataMissatges = date('Y-m-d', strtotime("-3 days"))

        return $this->runQuery($SQL, 
                        array(
                                'idU'=> $idUsuari, 
                                'idS'=> $idSite
                            ));

    }

    public function FeinesRows($idU, $idS) {

        $SQL = "
            Select  personal.tipus as PERSONAL_TIPUS, 
                    personal.text as PERSONAL_TEXT,
                    DATE_FORMAT(personal.data_alta, '%d-%m-%Y') as PERSONAL_DATA_ALTA
            from    personal
            where   personal.actiu = 1
              AND   personal.idUsuari = :idU
              AND   personal.site_id = :idS
              AND (
                    (personal.tipus = 1 AND personal.idData = :data1)
                OR (personal.tipus = 3 AND personal.idData = :data2)
                OR (personal.tipus = 4 AND personal.data_finalitzada is null)
              )
            ORDER BY personal.idData DESC
        ";        
        
        $data = '2017-01-04';        
        $idSite = 1;        
        $idUsuari = 1;
        //$data = date('Y-m-d', strtotime("now"));
        //$dataMissatges = date('Y-m-d', strtotime("-3 days"))

        return $this->runQuery($SQL, 
                        array(
                                'idU'=> $idUsuari, 
                                'idS'=> $idSite,
                                'data1' => $data,
                                'data2' => $data
                            ));

    }    

    public function ActivitatsRows($idU, $idS) {

        $SQL = "
            Select  activitats.ActivitatID as ACTIVITATS_ACTIVITATID,
                    activitats.Nom as ACTIVITATS_NOM,
                    DATE_FORMAT(horaris.dia, '%d-%m-%Y') as HORARIS_DIA,
                    TIME_FORMAT(horaris.HoraInici, '%H:%i') as HORARIS_HORAINICI,
                    TIME_FORMAT(horaris.HoraFi, '%H:%i') as HORARIS_HORAFI,
                    espais.Nom as ESPAIS_NOM,
                    horaris.Avis as HORARIS_AVIS
            FROM    activitats left join horaris on (activitats.ActivitatID = horaris.Activitats_ActivitatID)
            LEFT JOIN horarisespais ON (horaris.HorarisID = horarisespais.Horaris_HorarisID)
            LEFT JOIN espais ON (horarisespais.Espais_EspaiID = espais.EspaiID)
            WHERE   activitats.actiu = 1
            AND     horaris.actiu = 1
            AND     horarisespais.actiu = 1
            AND     espais.actiu = 1
            AND     activitats.site_id = :idS
            AND     horaris.Dia = :data
            ORDER BY horaris.HoraInici
        ";        
        
        //$data = '2017-01-04';        
        $idSite = $idS;                
        $data = date('Y-m-d', strtotime("now"));        

        return $this->runQuery($SQL, 
                        array(                                
                                'idS'=> $idSite,
                                'data' => $data                                
                            ));

    }    

}

?>