<?php

$cookie_file_path = "./cookie.txt";

/* Comença l'APP */

// Carreguem la fitxa i fem login si cal.
Autentificar($cookie_file_path);
$idRegistre = getFitxa($cookie_file_path);
// $idRegistre = 23213;

//Carrego l'xml. 
$xml = simplexml_load_file ( './News.txt');
foreach($xml->caixa as $caixa) {

    $Fitxa = tractaCaixa($caixa, $idRegistre);
    guardaFitxa($cookie_file_path, $Fitxa);
    uploadFile($cookie_file_path, $idRegistre, $caixa->url_img_l, 231, 150, 'mini', 'fotomini');
    uploadFile($cookie_file_path, $idRegistre, $caixa->url_img_l, 664, 374, 'foto', 'foto');    
    uploadFile($cookie_file_path, $idRegistre, $caixa->url_img_m, 200, 200, 'logo', 'logo');    

}


function Autentificar($cookie) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'user=casacultura&pwd=Hospital%242&Enviar=+Entrar+');
    curl_setopt($ch, CURLOPT_URL, "https://www.girona.cat/adminwebs/index.php");
    $html = curl_exec($ch);
    if( stripos($html, "incorrectes") > 0 ){ echo "\n\r[FAIL] Hi ha hagut un error autentificant."; die; }
    else echo "\n\r[OK] Login...";    
    curl_close($ch);        
}

function getFitxa($cookie) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_URL, "https://www.girona.cat/adminwebs/llistar_agenda.php?act=2");
    $html = curl_exec($ch);
    $curl_info = curl_getinfo($ch);
    curl_close($ch);
    
    if( stripos($html, 'Agenda: informaci') >= 0 ) {        
        $idRegistre = explode("idReg=", $curl_info['url'])[1];
        echo "\n\r[OK] Nova fitxa amb id: ". $idRegistre;        
        return $idRegistre;    
    } else {
        die("\n\r[FAIL] Carregant fitxa...");
    }
    

}

function guardaFitxa($cookie, $A) {
    
    foreach($A as $K => $A2) $A[$K] = $K . '=' . urlencode(utf8_decode($A2));
    $RET = implode("&", $A);    

    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $RET);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_URL, "https://www.girona.cat/adminwebs/guardar_dades.php");            
    $html = curl_exec($ch);

    if( stripos($html, 'Ägenda: informaci') >= 0 ) {
        echo "\n\r[OK] Fitxa guardada...";
    } else {
        echo "\n\r[FAIL] Error guardant fitxa...";
    }
    
    curl_close($ch);    
}



/**
* mini / fotomini 
* 
*
**/
function uploadFile($cookie, $idReg, $file, $width, $height, $tag1, $tag2) {
        
    // Baixem imatge d'internet i la guardem a un arxiu    
    $filename = "./imatge.jpg";            
    file_put_contents($filename, file_get_contents( $file ));    
    
    //Obrim l'ariu i fem un crop    
    list($w, $h) = getimagesize($filename);    
    $image = imagecreatefromjpeg($filename);    
    $ratio = max($width/$w, $height/$h);
    $h = $height / $ratio;
    $x = ($w - $width / $ratio) / 2;
    $w = $width / $ratio;
    $newImage = imagecreatetruecolor($width, $height);    
    imagecopyresampled($newImage, $image, 0, 0, $x, 0, $width, $height, $w, $h);
    imagejpeg($newImage, $filename);
        
    $CF = new CURLFile($filename, 'image/jpeg', 'imatge.jpg' );        
    $base64 = 'data:image/jpeg;base64,' . base64_encode( file_get_contents($filename) );    
    $fields = array(
        'idReg' => $idReg, 
        'taula' => 'agenda', 
        'modif_img' => $tag1, 
        'mida_w' => '', 
        'mida_h' => '', 
        'tipus_fitxer' => $tag2, 
        'fotomini_w' => $width, 
        'fotomini_h' => $height, 
        'foto_w' => '664', 
        'foto_h' => '374', 
        'ban_w' => '664', 
        'ban_h' => '374', 
        'force_size' => '1',
        'image_loaded' => '1',
        'slim[]' => '{ "server": null,
            "meta": {},
            "input": { "name": "imatge.jpg","type": "image/jpeg","size": 11118,"width": '.$width.',"height": '.$height.',"field": null},
            "output": {"name": "imatge.jpg","type": "image/jpeg","width": '.$width.',"height": '.$height.',"image": "'.$base64.'"},
            "actions": {"rotation": null,"crop": {"x": 0,"y": 0,"width": '.$width.',"height": '.$height.',"type": "auto"},"size": {"width": '.$width.'\n,"height": '.$height.'\n,}}
          }',
        'fitxer' => $CF
        );                

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields );    
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));    
    curl_setopt($ch, CURLOPT_URL, "https://www.girona.cat/adminwebs/upload.php");                
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_HEADER, 1);    
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);    
    $result=curl_exec ($ch);               
    
    curl_close ($ch);    
    
}

function TipusActivitat($TipusActivitat){
    switch($TipusActivitat) {
        case 'Acte intern': return 'Altres'; break;
        case 'Activitat artística': return 'Concert'; break;
        case 'Activitat científica': return 'Conferència'; break;
        case 'Activitat familiar': return 'Taller'; break;
        case 'Activitat humanitats': return 'Conferència'; break;
        case 'Activitat literària': return 'Conferència'; break;
        case 'Activitat tecnològica': return 'Conferència'; break;
        case 'Altres': return 'Altres'; break;
        case 'Assaig': return 'Altres'; break;
        case 'Col·loqui, taula rodona': return 'Taula rodona'; break;
        case 'Concert': return 'Concert'; break;
        case 'Conferència, xerrada': return 'Conferència'; break;
        case 'Curs': return 'Curs'; break;
        case 'Exposició': return 'Exposició'; break;
        case 'Gravació': return 'Altres'; break;
        case 'Hora del conte': return 'Hora del conte'; break;
        case 'Inauguració exposició': return 'Inauguració'; break;
        case 'Jornada': return 'Jornada'; break;
        case 'Presentació de llibre': return 'Presentació'; break;
        case 'Representació teatral': return 'Espectacle'; break;
        case 'Reunió': return 'Altres'; break;
        case 'Roda de premsa': return 'Altres'; break;
        case 'Taller familiar': return 'Taller'; break;
        case 'Taller tecnològic': return 'Taller'; break;
        case 'Visita escolar': return 'Visita guiada'; break;
        case 'Visita guiada': return 'Visita guiada'; break;        
        default: return 'Altres';
    }

}

function tractaCaixa($caixa, $idRegistre) {    
    $D = explode("-", $caixa->data_inicial);    
    $DataInicial = $D[2].'/'.$D[1].'/'.$D[0].' '.$caixa->hora_inici;
    $Tipologia = explode( "@", $caixa->tipologia );
    $TextCurt = ( empty($Text) ) ? $caixa->text_curt : $caixa->text;

    $A = array();
    $A["seccio"] = "agenda";
    $A["visible"] = "1";
    $A["tornar_valor"] = "0";								// 0
    $A["dates_agenda"] = $DataInicial;		            //Aquí hi va el valor de l'agenda 18/04/2020 11:00~#~15/04/2020
    $A["cerca_text"] = "";
    $A["idreg"] = $idRegistre;								//IdRegistre
    $A["estat"] = "0";									    // Vigent 0, Suspes 1, Cancel·lat 3, Ajornat 2
    $A["estat_text_cat"] = "";
    $A["titol_cat"] = $caixa->titol;								//El títol
    $A["intro_cat"] = $TextCurt;									//Introduccio
    $A["cos_cat"] = $caixa->text; 									//El cos del missatge
    $A["preu_cat"] = ( $caixa->preu != 'Gratuït' ) ? $caixa->preu : ''; // Gratuit
    $A["credits_cat"] = ""; 								//Text
    $A["select_cicle"] = "";
    $A["cicle_cat"] = ""; 									// Caldrà posar-lo manualment
    $A["venda_cat"] = ""; 									// Web Venta
    $A["url_cat"] = $caixa->url; 									// URL WEB
    $A["puntual"] = "1";									//1
    $A["selectDia"] = "(dia)";
    $A["selectMes"] = "(mes)";
    $A["selectAny"] = "2020";
    $A["selectHora"] = "(hora)";
    $A["selectMinut"] = "(minut)";
    $A["data"] = "";
    $A["data2"] = "";
    $A["dataviz_cat"] = "";
    $A["select_lloc"] = "";
    $A["lloc"] = $caixa->espais; 										// Un String Casa+de+Cultura
    $A["lat"] = "41.981478";
    $A["lon"] = "2.821027";
    $A["youtube_cat"] = ""; 								// Hi ha vídeo?
    $A["excloure_agendagi"] = "0";                          // Excloure d'agenda de Girona ( 1, 0)
    $A["exposicio"] = ( array_search( '46', $Tipologia) >= 0 ) ? '1' : '0';                                  // És exposició
    $A["gratuit"] = ( $caixa->preu == 'Gratuït') ? '1' : '0';									// Check gratuït
    $A["formacio"] = ( array_search( '45', $Tipologia ) >= 0 ) ? '1' : '0';									// Es formació
    $A["ciencia"] = "0";									// Es ciència
    $A["pect"] = "0";										// És del pect
    $A["virtual"] = "0";									// És virtual
    $A["tipus1"] = ( array_search( '56', $Tipologia ) >= 0 ) ? '1' : '0';	// Música
    $A["tipus2"] = ( array_search( '56', $Tipologia ) >= 0 ) ? '1' : '0';	// Música - Activitats
    $A["tipus3"] = "0";									// Arts escèniques
    $A["tipus4"] = "0";									// Arts visuals
    $A["tipus5"] = "0";									// Audiovisuals
    $A["tipus6"] = ( array_search( '55', $Tipologia ) >= 0 ) ? '1' : '0';	// Lectura, ciència i humanitats
    $A["tipus7"] = "0";									// Patrimoni i museus
    $A["tipus8"] = ( array_search( '59', $Tipologia ) >= 0 ) ? '1' : '0';	// Infantils i familiars
    $A["tipologia"] = TipusActivitat($caixa->tipus_activitat);				// Tipologia    
    $A["butlleti_cat"] = $caixa->text; 					// Text pel butlletí
    $A["club_descompte_cat"] = $TextCurt;
    $A["resum_cultura_cat"] = $caixa->text; 			// Resum cultura
    $A["data_modif"] = date('Y/m/d H:i:s', time());

    return $A;
}