<?php 

require_once BASEDIR.'Database/Queries/AvuiQueries.php';

class AvuiController
{
    private $AvuiData = array('Missatges' => array(), "Incidencies" => array(), "Feines" => array(), "Activitats" => array() );
    private $AvuiQuery;

    public function __construct() {
        $this->AvuiQuery = new AvuiQueries();
    }
        
/*
    public function consulta() {}
    public function edita() {}
    public function actualitza() {}
    public function esborra() {}
*/

    public function consulta($idUsuari, $idSite) {        
        $this->AvuiData['Missatges'] = $this->AvuiQuery->MissatgesRows($idUsuari, $idSite);
        $this->AvuiData['Incidencies'] = $this->AvuiQuery->IncidenciesRows($idUsuari, $idSite);        
        $this->AvuiData['Feines'] = $this->AvuiQuery->FeinesRows($idUsuari, $idSite);        
        $this->AvuiData['Activitats'] = $this->AvuiQuery->ActivitatsRows($idUsuari, $idSite);        
        return $this->AvuiData;
    }

 }

 ?>
