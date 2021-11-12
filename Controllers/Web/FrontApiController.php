<?php 

class FrontApiController
{

    public function __construct() {
        // $this->WebQueries = new WebQueries();
        // $this->setNewDate(date('Y-m-d', time()));        
    }

    /**
     * array(estat, urlArxiu | TextError )
     */
    public function doUploadPaginaFile($Pagina, $NomArxiu, $FileTmpName) {
        
        $ArxiuDesti = DOCUMENTSDIR . $Pagina . '/' . $NomArxiu;
        $UrlDesti = DOCUMENTSURL . $Pagina . '/' . $NomArxiu;

        // Si el directori no exiteix, el creem.
        if(!file_exists(DOCUMENTSDIR . $Pagina)) mkdir(DOCUMENTSDIR . $Pagina);

        // Si hi ha el directori, seguim amb la còpia de l'arxiu
        if(move_uploaded_file($FileTmpName , $ArxiuDesti )) return array(true, $UrlDesti);
        else return array(false, "No he pogut carregar l'arxiu correctament.");
        
    }
    
    private function convertFileNameToDate($Filename, $toDate = false, $toSession = false) {
        $path = pathinfo($Filename);
        return date("YmdHis") . '.' . $path['extension'];
    }
    
    public function doUploadFrontEnds($SiteId, $Tipus, $Dades, $Arxius) {
                
        $DIR_DOWNLOADS = "/var/www/downloads/";
        // $DIR_DOWNLOADS = "C:\Users\Usuario\Downloads/";

        $DIR_DOWNLOADS_SITE = $DIR_DOWNLOADS . $SiteId . "/";
        $DIR_DOWNLOADS_TEMP = $DIR_DOWNLOADS_SITE . "tmp/".session_id().'/';

        $URL_DOWNLOADS = "http://www.casadecultura.cat/downloads/";
        // $URL_DOWNLOADS = "http://localhost:8080/downloads/";
        $URL_DOWNLOADS_SITE = $URL_DOWNLOADS . $SiteId . "/";
        $URL_DOWNLOADS_TEMP = $URL_DOWNLOADS_SITE . "tmp/".session_id().'/';
        
        
        if($Tipus == 'NotaPremsaGenera' || $Tipus == 'NotaPremsaPublica') { 
                                
            $TipusPublica = ($Tipus == 'NotaPremsaPublica');

            // Creo el directori /tmp o bé esborro la sessió en qüestió...             
            if(!file_exists($DIR_DOWNLOADS_TEMP)) mkdir($DIR_DOWNLOADS_TEMP, 0777, true);                        

            $RET['UrlImatge'] = $this->convertFileNameToDate($Arxius[0]['name'] , $TipusPublica, !$TipusPublica );
            $RET['UrlNotaPremsa'] = $this->convertFileNameToDate($Arxius[1]['name'] , $TipusPublica, !$TipusPublica );
            $RET['UrlWeb'] = $this->convertFileNameToDate('NotaDePremsa.html' , $TipusPublica, !$TipusPublica );
                                        
            if($Tipus == 'NotaPremsaGenera') {
                                    
                //Esborro els arxius que hi pugui haver al temp
                array_map( 'unlink', array_filter((array) glob($DIR_DOWNLOADS_TEMP."/*") ) );

                move_uploaded_file( $Arxius[0]['tmp_name'], $DIR_DOWNLOADS_TEMP . 'ImatgeNotaPremsa.html' );
                move_uploaded_file( $Arxius[1]['tmp_name'], $DIR_DOWNLOADS_TEMP . 'ArxiuNotaPremsa.html' );                
                
                $Html = str_replace( '@@URL_NOTA_WEB@@' , $URL_DOWNLOADS_TEMP . 'NotaDePremsa.html', $Dades[0] );         
                $Html = str_replace( '@@DOWNLOAD_NOTA@@' , $URL_DOWNLOADS_TEMP . 'ArxiuNotaPremsa.html', $Html );         
                $Html = str_replace( '@@URL_IMATGE@@' , $URL_DOWNLOADS_TEMP . 'ImatgeNotaPremsa.html', $Html );         
                $Html = str_replace( '&lt;' , "<", $Html );         
                $Html = str_replace( '&gt;' , ">", $Html );                                                                         
                file_put_contents( $DIR_DOWNLOADS_TEMP . 'NotaDePremsa.html' , $Html );                                

                return array(200, array('Html' => $Html, 'Url' => $DIR_DOWNLOADS_TEMP . $RET['UrlWeb']));

            }
            if($Tipus == 'NotaPremsaPublica') {
                                
                rename($DIR_DOWNLOADS_TEMP . 'ArxiuNotaPremsa.html', $DIR_DOWNLOADS_SITE . $RET['UrlNotaPremsa']);
                rename($DIR_DOWNLOADS_TEMP . 'ImatgeNotaPremsa.html', $DIR_DOWNLOADS_SITE . $RET['UrlImatge']);                

                $Html = str_replace( '@@URL_NOTA_WEB@@' , $URL_DOWNLOADS_SITE . $RET['Publica']['UrlWeb'], $Dades[0] );         
                $Html = str_replace( '@@DOWNLOAD_NOTA@@' , $URL_DOWNLOADS_SITE . $RET['Publica']['UrlNotaPremsa'], $Html );         
                $Html = str_replace( '@@URL_IMATGE@@' , $URL_DOWNLOADS_SITE . $RET['Publica']['UrlImatge'], $Html );         
                $Html = str_replace( '&lt;' , "<", $Html );         
                $Html = str_replace( '&gt;' , ">", $Html );                                                                         
                file_put_contents( $DIR_DOWNLOADS_SITE . $RET['UrlWeb'] , $Html );                  

                //Esborro el directori de la sessió
                rmdir($DIR_DOWNLOADS_TEMP);                

                return array(200, array('Html' => $Html, 'Url' => $URL_DOWNLOADS_SITE . $RET['UrlWeb']));

            }
                                
        }        

    }

    /**
     * Funció que gestiona el control horari. 
     * Cada treballador té un arxiu per any on es guarden tots els clicks al botó amb el DATABASE\DbFiles\ControlHorari\nom 1-mmaaaa.json
     * JSonStructure [{Data: aaaa-mm-dd, HoraInici: ii:ii:ii, HoraFi: ii:ii:ii, TotalHores: 0}]
     * Quan guardi, he de tenir en compte que si els dies són diferents... guardar fins les 12. 
     *
     * @param [type] $idS
     * @param [type] $idU
     * @param [type] $accio
     * @return void
     */
    public function doControlHorari( $idS, $idU, $accio, $MesAny = '', $DadesForm = array() ) {
        
        $MonthYear = date('mY');
        if( ! empty($MesAny) ) $MonthYear = $MesAny;

        $ModeIdle = ($accio == 'idle');
        $ModeSave = ($accio == 'save');   
        $ModePdf = ($accio == 'pdf');     
        
        $UrlArxiu = DATABASEDIR . 'DbFiles/ControlHorari/' . $idU . '-' . $MonthYear.'.json';        
        $Return = array('Dia' => 0, 'Setmana' => 0, 'Mes' => 0, 'Error' => '', 'EstatBoto' => '', 'DetallHores' => array(), 'TotalDies' => 0);
        
        // Si estic guardant, primer guardo i després carrego la informació
        if($ModeSave && sizeof($DadesForm) > 0) {
            
            // Calculem els totals i ho guardem
            foreach($DadesForm as $Key => $Row) {
                if($Row['HoraFi'] != '') {
                    $DadesForm[$Key]['Total'] = $this->DiferenciaEntreHores( $Row['Data'] . ' ' . $Row['HoraInici'] , $Row['Data'] . ' ' . $Row['HoraFi'] );
                } else {
                    $DadesForm[$Key]['Total'] = 0;
                }
            }
            if(file_put_contents($UrlArxiu, json_encode($DadesForm)) === FALSE) throw new Exception('Problema guardant');
        }

        $File = array(); $ArxiuInexistent = false;        
        if( ! file_exists($UrlArxiu) ){ $ArxiuInexistent = true; } 
        else $File = json_decode(file_get_contents($UrlArxiu), true);
        
        $UltimaEntrada = end($File);
        $IndexUE = array_key_last($File);
        $isHoraFinalBuida = (isset($UltimaEntrada['HoraFi']) && $UltimaEntrada['HoraFi'] == '');
        $isDataIgualAvui = ( $UltimaEntrada['Data'] == date('Y-m-d') );
        $UltimaPrimeraDataiHora = $UltimaEntrada['Data'] . ' ' . $UltimaEntrada['HoraInici'];
        $DataiHoraAra = date('Y-m-d H:i');
        
        if( ! $ModeIdle && ! $ModeSave && ! $ModePdf ) {
                        
            // Hem de crear una nova línia: Hora final ja està plena, arxiu no existeix o data diferent i hora final plena.
            if( ! $isHoraFinalBuida || $ArxiuInexistent ) { 

                // Si hora final no és buida, creem una entrada nova línia nova
                $File[] = array('Data'=>date('Y-m-d'), 'HoraInici' => date('H:i'), 'HoraFi' => '', 'Total' => 0); 
                $UltimaEntrada = end($File);
                $IndexUE = array_key_last($File);
                $UltimaPrimeraDataiHora = $UltimaEntrada['Data'] . ' ' . $UltimaEntrada['HoraInici'];

                // Hora final buida i data és la d'avui, tanquem la jornada
            } else if( $isHoraFinalBuida && $isDataIgualAvui ) { 
                               
                $File[$IndexUE]['HoraFi'] = date('H:i');
                $File[$IndexUE]['Total'] = $this->DiferenciaEntreHores( $UltimaPrimeraDataiHora , $DataiHoraAra );
                $isHoraFinalBuida = false;

                // La data canvia però la hora final és buida... llavors marquem data màxima del dia i ho deixem per iniciar
            } else if( $isHoraFinalBuida && ! $isDataIgualAvui ) { $File[$IndexUE]['HoraFi'] = '23:59'; }

            if( file_put_contents($UrlArxiu, json_encode($File)) === FALSE) throw new Exception("Problema guardant.");

        }        
                    
        //Ara calculo quantes hores ha fet cada empleat per Dia, Setmana, Mes i comprovo que els totals són correctes. 
        $T = array();
        foreach($File as $Row) {                
            
            $RowTime = strtotime($Row['Data']);
            $Setmana = date('W', $RowTime);            
            $Dia = date('d', $RowTime);            
            $T[$Dia] = $Dia;

            if($Dia == date('d')) $Return['Dia'] += $Row['Total'];            
            if($Setmana == date('W')) $Return['Setmana'] += $Row['Total'];            
            
            // No miro el mes, perquè els arxius ja van per mensualitats
            $Return['Mes'] += $Row['Total'];            
            
        }                     
                
        $is_Existeix_HoraFi_i_es_buida = ( isset($File[$IndexUE]['HoraFi']) && $File[$IndexUE]['HoraFi'] == '' );
        $Return['EstatBoto'] = ( $is_Existeix_HoraFi_i_es_buida ) ? 'off' : 'on';            
        $Return['TempsActualTreballat'] = ($is_Existeix_HoraFi_i_es_buida) ? $this->DiferenciaEntreHores( $UltimaPrimeraDataiHora , $DataiHoraAra ) : 0;
        $File[$IndexUE]['Total'] += $Return['TempsActualTreballat'];
        $Return['Dia'] += $Return['TempsActualTreballat'];
        $Return['Mes'] += $Return['TempsActualTreballat'];
        $Return['Setmana'] += $Return['TempsActualTreballat'];
        $Return['DetallHores'] = $File;
        $Return['PdfUrl'] = '';
        $Return['TotalDies'] = sizeof($T);

        if( $ModePdf ){

            $mpdf = new \Mpdf\Mpdf();
            $Files = '';
            $UM = new UsuarisModel();
            $OU = $UM->getUsuariId($idU);
            $NomUsuari = $UM->getNomComplet($OU);

            foreach($Return['DetallHores'] as $DH):
                $TotalHoresTreballades = round(intval($DH['Total'])/60, 1);
                $T = explode("-", $DH['Data']); 
                $DataFormatEntenedor = $T[2].'/'.$T[1].'/'.$T[0];
                $Files .= '<tr>
                                <td style="width: 25%;">'.$DataFormatEntenedor.'</td>
                                <td style="width: 25%;">'.$DH['HoraInici'].'</td>
                                <td style="width: 25%;">'.$DH['HoraFi'].'</td>
                                <td style="width: 25%;">'.$TotalHoresTreballades.'</td>
                            </tr>';
            endforeach;

            $TotalHoresMes = round( intval( $Return[ 'Mes' ] ) / 60 , 1 );
            $MitjanaHoresMes = round( ( intval( $Return[ 'Mes' ] ) / 60 ) / $Return['TotalDies'] , 1 );
            $HTML = '
            <img width="200px" src="http://www.casadecultura.cat/WebFiles/Web/img/LogoCCG.jpg" />
            <br /><br /><br /><br />
            <table style="width:100%">
                <tr>
                    <td><strong>Treballador</strong></td><td>'.$NomUsuari.'</td>
                    <td><strong>Total hores mes</strong></td>
                    <td>'. $TotalHoresMes .'h - '.$MitjanaHoresMes.'h/dia</td>                                        
                </tr>
            </table>            
            <br /><br />
            <table style="border-collapse: collapse; width: 100%;" border="1">
            <tbody>
                <tr>
                    <th style="width: 25%;">Dia</th>
                    <th style="width: 25%;">Hora inici</th>
                    <th style="width: 25%;">Hora finalitzaci&oacute;</th>
                    <th style="width: 25%;">Total</th>
                </tr>
                '.$Files.'            
            </tbody>
            </table>
            <br /><br /><br /><br />
            <table style="width:100%"><tr><td><strong>Firma treballador</strong></td><td><strong>Firma supervisor</strong></td></tr></table>            
            ';            
            
            $mpdf->WriteHTML($HTML);
            $Return['PdfUrl'] = DOCUMENTSURL . '/ControlHorari/' . $idU . '.pdf';
            $mpdf->Output( DOCUMENTSDIR . '/ControlHorari/' . $idU . '.pdf' , \Mpdf\Output\Destination::FILE );

        }

        return $Return;
    }

    private function DiferenciaEntreHores($H1, $H2) {
        $datetimeObj1 = new DateTime( $H1 );
        $datetimeObj2 = new DateTime( $H2 );
        $interval = $datetimeObj1->diff($datetimeObj2);
        return ($interval->format('%a')*24*60) + ($interval->format('%h')*60) + $interval->format('%i');
    }

    public function getXMLActivitats($DataInicial, $DataFinal, $SiteId) {
        
        $AM = new ActivitatsModel();
        
        $Document = $AM->genXML($DataInicial, $DataFinal, $SiteId);
        $UrlWebFile = $this->saveButlleti("NewFile", $SiteId);         
        
        return array('document' => $Document, 'UrlWeb' => $UrlWebFile);

    }

    public function saveButlleti( $HTML, $SiteId ) {

        $DirWebFile = DOCUMENTSDIR . "Butlletins/{$SiteId}/";
        $UrlWebFile = IMATGES_URL_BASE . DOCUMENTSURL . "Butlletins/{$SiteId}/";

        if(!file_exists($DirWebFile)) mkdir($DirWebFile, 0777, true);
        
        $DataNameTemp = date('dmY'); 
        $DirWebFile .= "Butlleti".$DataNameTemp.".html";
        $UrlWebFile .= "Butlleti".$DataNameTemp.".html";

        if(file_put_contents( $DirWebFile, $HTML ) === false) throw new Exception("No he pogut crear l'arxiu butlletí consultable al web");
        else return $UrlWebFile;        
        
    }
    
}

 ?>
