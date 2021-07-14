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

$DIR = DOCUMENTSDIR . "160/";
$URL = DOCUMENTSURL . "160/";

$ArxiusAMostrar = array_diff(scandir($DIR, SCANDIR_SORT_DESCENDING), array('..', '.'));
sort($ArxiusAMostrar);
// El format d'arxiu és PMPYYYYMM
foreach($ArxiusAMostrar as $ArxiuAMostrar) {        
        $year = substr($ArxiuAMostrar,3,4);
        $month = substr($ArxiuAMostrar,7,2);                
        if(!isset($HTML[$year])) $HTML[$year] = array();        
        $HTML[$year][$month] = $ArxiuAMostrar;                
}


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
error_reporting(0);

echo '<h1>Període Mig de Pagament</h1>';

echo '<ul class="nav nav-tabs" id="myTab" role="tablist">';
foreach($HTML as $year => $HtmlMonth):    
    echo '  <li class="nav-item">
                <a class="nav-link" id="A'.$year.'-tab" data-toggle="tab" href="#A'.$year.'" role="tab" aria-controls="A'.$year.'" aria-selected="true">'.$year.'</a>
            </li>';
endforeach;
echo '</ul>';

echo '<div class="tab-content" id="myTabContent">';
foreach($HTML as $year => $HtmlMonth) {    
    
    echo '<div class="tab-pane fade" id="A'.$year.'" role="tabpanel" aria-labelledby="A'.$year.'-tab" style="margin-top: 40px;"><ul>';

    foreach($HtmlMonth as $month => $File){                        
        $dom = new DOMDocument();
        $text = "";
        if( $dom->loadHTML(file_get_contents($DIR.'/'.$File)) ):
            $Tag = $dom->getElementsByTagName("h3");                    
            $Item = $Tag->item(0);                    
            $text = $Item->textContent;
        endif;
        
        echo "<li> <a target=\"_NEW\" href=\"{$URL}{$File}\">". Mesos($month). "</a> </li>";
    }    
    echo '</ul></div>';
}
echo '</div>';

// error_reporting(E_ALL);

?>