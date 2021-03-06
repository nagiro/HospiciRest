<?php 

function Mesos($mes) {

    switch($mes) {
        case '01': return 'Gener';
        case '02': return 'Febrer';
        case '03': return 'Març';
        case '04': return 'Abril';
        case '05': return 'Maig';
        case '06': return 'Juny';
        case '07': return 'Juliol';
        case '08': return 'Agost';
        case '09': return 'Setembre';
        case '10': return 'Octubre';    
        case '11': return 'Novembre';
        case '12': return 'Desembre';
    }
    
}

$HTML = array();

$URL = '/var/www/downloads/premsa';
$files1 = scandir($URL, SCANDIR_SORT_DESCENDING);
foreach($files1 as $f2) {
    
    if( stripos($f2, '.html') !== false && stripos($f2, 'template') === false ) {
        $year = substr($f2,0,4);
        $month = substr($f2,4,2);
        $dia = substr($f2, 6, 2);
        $hora = substr($f2, 8, 2);
        if(!isset($HTML[$year])) $HTML[$year] = array();
        if(!isset($HTML[$year][$month])) $HTML[$year][$month] = array();
        if(!isset($HTML[$year][$month])) $HTML[$year][$month] = array();
        if(!isset($HTML[$year][$month][$dia])) $HTML[$year][$month][$dia] = array();
        if(!isset($HTML[$year][$month][$dia][$hora])) $HTML[$year][$month][$dia][$hora] =  $f2;
        
    }

}


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
error_reporting(0);

echo '<ul class="nav nav-tabs" id="myTab" role="tablist">';
foreach($HTML as $year => $HtmlMonth):    
    echo '  <li class="nav-item">
                <a class="nav-link" id="A'.$year.'-tab" data-toggle="tab" href="#A'.$year.'" role="tab" aria-controls="A'.$year.'" aria-selected="true">'.$year.'</a>
            </li>';
endforeach;
echo '</ul>';

echo '<div class="tab-content" id="myTabContent">';
foreach($HTML as $year => $HtmlMonth) {    
    
    echo '<div class="tab-pane fade" id="A'.$year.'" role="tabpanel" aria-labelledby="A'.$year.'-tab">';

    foreach($HtmlMonth as $month => $HtmlDay){

        echo '<h2>'.Mesos($month).' '.$year.'</h2><ul>';

        foreach($HtmlDay as $day => $HtmlHour) {
            foreach($HtmlHour as $hour => $File) {
                $dom = new DOMDocument();
                $text = "";
                if( $dom->loadHTML(file_get_contents($URL.'/'.$File)) ):
                    $Tag = $dom->getElementsByTagName("h3");                    
                    $Item = $Tag->item(0);                    
                    $text = $Item->textContent;
                endif;
                
                echo "
                <li>
                    {$day}/{$month}/{$year} a les {$hour}h __ <a target=\"_NEW\" href=\"/downloads/premsa/{$File}\">{$text}</a>
                </li>";
            }
        }
        echo '</ul>';
    }    
    echo '</div>';
}
echo '</div>';

error_reporting(E_ALL);

?>