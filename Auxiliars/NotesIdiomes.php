<?php 
require_once '../Database/Tables/CursosModel.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
error_reporting(0);

echo "<h1>Inscripcions als cursos d'idiomes</h1>";

$DNI = '';
if(isset($_GET['DNI'])):
    
    $DNI = trim(strtoupper($_GET['DNI']));
    $CM = new CursosModel();
    $CURSOS = $CM->potMatricularSegonsRestriccio($DNI, 2741);
    
    
    if(sizeof($CURSOS) == 0) echo '<h2>No hem trobat cap constància del seu DNI.</h2>';
    else echo "<h2>Cursos als que pot matricular-se</h2>";
    
    echo "<p><ul>";
    foreach($CURSOS['CursosOk'] as $K => $Curs):
        echo '<li><a href="https://www.casadecultura.cat/inscripcio/'.$Curs['id'].'">' . $Curs['nom'] . '</a></li>';
    endforeach;    
    echo "</ul></p>";


else: 

?>

<form action="/pagina/201/NotesIdiomes" method="GET">
    <p>En aquesta pàgina vostè podrà entrar el seu DNI i li indicarem a quins cursos pot matricular-se.</p>
    <input type="text" placeholder="DNI amb lletra sense guionets ni punts" name="DNI">
    <button>Cerca</button>
</form>


<?php 

endif;

// error_reporting(E_ALL);

?>