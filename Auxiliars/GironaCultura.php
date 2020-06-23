<?php

$cookie_file_path = "./cookie.txt";

/* Comença l'APP */

// Carreguem la fitxa i fem login si cal.
Autentificar($cookie_file_path);
$idRegistre = getFitxa($cookie_file_path);
$idRegistre = 23202;

//Carrego l'xml. 
$xml = simplexml_load_file ( './News.txt');
foreach($xml->caixa as $caixa) {

    tractaCaixa($caixa, $idRegistre);
    guardaFitxa($cookie_file_path, $A);
    // uploadFileMini($cookie_file_path, $idRegistre, "https://www.casadecultura.cat/images/front/96-L.jpg");
    
    
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
    echo $html;
    curl_close($ch);    
}

function buildMultiPartRequest($ch, $boundary, $fields, $files) {
    $delimiter = '-------------' . $boundary;
    $data = '';

    foreach ($fields as $name => $content) {
        $data .= "--" . $delimiter . "\r\n"
            . 'Content-Disposition: form-data; name="' . $name . "\"\r\n\r\n"
            . $content . "\r\n";
    }
    foreach ($files as $name => $content) {
        $data .= "--" . $delimiter . "\r\n"
            . 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $name . '"' . "\r\n\r\n"
            . $content . "\r\n";
    }

    $data .= "--" . $delimiter . "--\r\n";        

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: multipart/form-data; boundary=' . $delimiter,
            'Content-Length: ' . strlen($data)
        ],
        CURLOPT_POSTFIELDS => $data
    ]);

    return $ch;
}


function uploadFileMini($cookie, $idReg, $file) {
    
    $file = file_get_contents( $file );            
    $filename = "C:/xampp7.3/htdocs/imatge.jpg";        
    $filecurl = curl_file_create($filename);    
    $fields = array(
        'idReg' => $idReg, 
        'taula' => 'agenda', 
    //    'modif_img' => 'mini', 
    //    'mida_w' => '', 
    //    'mida_h' => '', 
    //    'tipus_fitxer' => 'fotomini', 
    //    'fotomini_w' => '231', 
     //   'fotomini_h' => '150', 
    //    'foto_w' => '664', 
    //    'foto_h' => '374', 
    //    'ban_w' => '664', 
    //    'ban_h' => '374', 
     //   'force_size' => '1',
        'image_loaded' => '0',
        'fitxer' => $filecurl
        );
            

    $verbose = fopen('php://temp', 'w+');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields );
    curl_setopt($ch, CURLOPT_STDERR, $verbose);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));    
    curl_setopt($ch, CURLOPT_URL, "https://www.girona.cat/adminwebs/upload.php");                
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_HEADER, 1);    
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);    
    $result=curl_exec ($ch);            
    
    if ($result === FALSE) {
        printf("cUrl error (#%d): %s<br>\n", curl_errno($ch), htmlspecialchars(curl_error($ch)));
    }
    $verboseLog = stream_get_contents($verbose);
    echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

//    echo "<pre>". print_r(curl_getinfo($ch), true) .'</pre>';
    
    curl_close ($ch);    
    
}

function tractaCaixa($caixa, $idRegistre) {
    $D = explode("-", $caixa->data_inicial);    
    $DataInicial = $D[2].'/'.$D[1].'/'.$D[0].' '.$caixa->hora_inici;
    $Tipologia = explode( "@", $caixa->tipologia );
    $TextCurt = ( empty($Text) ) ? $caixa->text_curt : $caixa->text;

/*		
Acte popular
Altres
Audició
Audició comentada
Celebració
Cinefòrum
Circ
Club de lectura
Concert
Concurs
Conferència
Congrès
Conte
Curs
Dansa
Debat
Diàleg
Espectacle
Exposició
Festival
Hora del conte
Inauguració
Instal·lació
Itinerari
Joc
Jornada
Lectura
Mostra
Musical
Nadons
Òpera
Performance
Portes obertes
Presentació
Projecció
Recital
Sardanes
Seminari
Simposi
Sortida
Taller
Taula rodona
Teatre
Tertúlia
Titelles
Trobada
Visita guiada
Visita teatralitzada
Xerrada
*/

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
    $A["tipologia"] = $caixa->tipus_activitat;				// Tipologia    
    $A["butlleti_cat"] = $caixa->text; 					// Text pel butlletí
    $A["club_descompte_cat"] = $TextCurt;
    $A["resum_cultura_cat"] = $caixa->text; 			// Resum cultura
    $A["data_modif"] = date('Y/m/d H:i:s', time());

    return $A;
}