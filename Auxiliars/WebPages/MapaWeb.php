<?php 

require_once DATABASEDIR . 'Queries/WebQueries.php';
require_once DATABASEDIR . 'Tables/NodesModel.php';

$WQ = new WebQueries();
$Nodes = $WQ->getMenu();
$NodesInicials = array();
$NM = new NodesModel();
$Menu = array();

// Carrego els valors dins el menú amb estructura. 
foreach($Nodes as $N) {
    $idNode = $NM->getIdNodes($N);
    $idPare = $NM->getIdPare($N);
    
    $Menu[$idNode] = array('Titol'=> $NM->getTitolMenu($N), 'Fills' => array());
    if(isset($Menu[ $idPare ])) {
        $Menu[$idPare]['Fills'][] = $idNode;
    } 

    if( !($idPare > 0) ) $NodesInicials[] = $idNode;

}

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
error_reporting(0);

echo '<h1>Mapa del web</h1>';

function MostraMenu($Menu, $idNode) {    
    echo '<li><a href="/pagina/'.$idNode.'/'.urlencode($Menu[$idNode]['Titol']).'">'.$Menu[$idNode]['Titol'].'</a>';
    foreach($Menu[ $idNode ]['Fills'] as $N) {
        echo '<ul>';
        MostraMenu($Menu, $N);        
        echo '</ul>';
    }
    echo '</li>';
}

foreach($NodesInicials as $N) {
    echo '<ul>';
    MostraMenu($Menu, $N);
    echo '</ul>';
}

?>