<?php 

function SelectHelper($VModelName, $cols, $Titol, $OptionsArrayName, $isTable = false) {
    $RET = "";

    if ($isTable) {

        $RET .= '
        <tr>
            <td> ' . $Titol . ' </td>
            <td> 
            <select class="form-control" v-model="' . $VModelName . '" >
                <option v-for="o in ' . $OptionsArrayName . '" :value="o.id">{{o.nom}}</option>                
            </select>
            </td>            
        </tr> ';


    } else {

        $RET .= '
        <div class="col-'.$cols.'">
            <div class="form-group">
            <label>Tipus</label>
            <select class="form-control" v-model="'.$VModelName.'" >
                <option v-for="o in '.$OptionsArrayName.'" :value="o.id">{{o.nom}}</option>
            ';

        $RET .= '
            </select>
            </div>            
        </div>';

    }

    return $RET;
}

function InputHelper($VModelName, $cols, $Titol, $id, $Placeholder, $isTable = false) {

    $RET = "";

    if($isTable) {

        $RET .= '
        <tr>
            <td> '.$Titol.' </td>
            <td> <input type="text" class="form-control form-control-sm" id="'.$id.'" v-model="'.$VModelName.'"  placeholder="'.$Placeholder.'"> </td>
        </tr>';

    } else {

        $RET .= '
        <div class="col-'.$cols.'">
            <div class="form-group">
                <label for="'.$id.'">'.$Titol.'</label>
                <input  type="text" class="form-control form-control-sm" id="'.$id.'" v-model="'.$VModelName.'"  placeholder="'.$Placeholder.'"> 
            </div>            
        </div>';

    }
    return $RET;

}

function ButtonHelper($ClickAction, $cols, $Titol, $estil){
    $RET = "";
    $RET .= '
        <div class="col-'.$cols.'">
          <div class="form-group">
            <label for="">&nbsp;</label>
            <button v-on:click="'.$ClickAction.'" class="btn '.$estil.' form-control">'.$Titol.'</button>
          </div>            
        </div> ';
    
    return $RET;
}


/**
 * ChangeAction: Funció de Vuejs per quan carrega la imatge
 * Cols: Quantes columnes ocupa
 * Titol: Quina és l'etiqueta a usar
 * id: Identificador del tag html
 * URLAMostrar: Funció o variable de vuejs on hi ha la url que s'ha de mostrar
 */
function ImageHelper($ChangeAction, $cols, $Titol, $id, $URLAMostrar = '') {
    $RET = "";    
    $RET .= '
    <tr>
        <td> '.$Titol.' </td>
        <td> 
            <div class="custom-file">
                <div v-if="'.$URLAMostrar.'.length > 0" style="height: 50px">
                    <img :src="'.$URLAMostrar.'" style="height: 50px">
                    <i @click="EsborraImatge(\''.$id.'\')" class="withHand fas fa-trash-alt"></i>
                </div> 
                <div v-if="'.$URLAMostrar.'.length == 0">
                    <input @change="'.$ChangeAction.'" type="file" class="form-control" id="'.$id.'" >
                    <label class="custom-file-label" for="'.$id.'">Escull arxiu</label>
                </div>        
            </div>
        </td>
    </tr> ';        

    return $RET;

}

function TitleWithAdd($Action, $Titol){
    return "<h5>{$Titol} <i @click=\"{$Action}\" class=\"fas fa-plus-square withHand\"></i></h5>";
}


function HelperForm_FileConvertAndSaveFromPostParameterBase64($DirWhereToSave, $UrlToShow, $FormFile, $id) {
    
    if(strlen($FormFile['hexfile']) == 0) return true; 
    if(empty($id)) $id = "tmp_" . session_id();

    $DirPartsArray = explode('.', $FormFile['name']);
    $Extensio = array_pop($DirPartsArray);    
    
    $index = 0;
    $filename = "F_{$id}_{$index}.{$Extensio}";
        
    while(file_exists($DirWhereToSave . $filename)) {
        $index = $index + 1;
        $filename = "F_{$id}_{$index}.{$Extensio}";
    }
        
    file_put_contents($DirWhereToSave . $filename, base64_decode($FormFile['hexfile'])); 
    $FormFile['dir'] = $filename;
    $FormFile['url'] = $UrlToShow . $filename;
    $FormFile['hexfile'] = '';
    return $FormFile;
}

function HelperForm_FileCleanFromPostParameterBase64($DirWhereToSave, $id) {
                    
    if(empty($id)) $id = "F_tmp_" . session_id();
    foreach (glob("{$DirWhereToSave}F_{$id}*") as $filename) {
        unlink($filename);
    } 

}

// Esborrem tots els arxius d'una id determinada a un directori determinat
function HelperForm_FileRenameFromTempToId($DirWhereToSave, $id) {
                    
    if(empty($id)) throw new Exception('Vols renombrar un arxiu sense un id vàlid');
    else {
        $oldName = "F_tmp_" . session_id();
        foreach (glob("{$DirWhereToSave}{$oldName}*") as $filename) {
            $oldFile = basename($filename);
            $Parts = explode("_", $oldFile);
            $Parts[2] = $id;
            unset($Parts[1]);            
            $newName = implode("_", $Parts);            
            rename("{$DirWhereToSave}{$oldFile}", "{$DirWhereToSave}{$newName}");
        } 
    }
    

}

function HelperForm_DefaultValueForFileUploadForm() {
    return array('url' => '', 'dir' => '', 'hexfile' => '', 'name' => '');
}


function HelperForm_Encrypt($id) { return base64_encode(openssl_encrypt($id, 'aes128', '(ccg@#).', 0, '45gh354645gh3546' )); }
function HelperForm_Decrypt($id) { return openssl_decrypt(base64_decode($id), 'aes128', '(ccg@#).', 0, '45gh354645gh3546'); }

function HelperForm_SendEmail($to, $idSite, $subject, $HTML) {
    $url = 'https://api.elasticemail.com/v2/email/send';

    // From, i From Name canviarà segons opció escollida
    $OO = new OptionsModel();
    $FromEmail = $OO->getOption("MAIL_FROM", $idSite);
    $FromName = $OO->getOption("MAIL_NAME", $idSite);        
    
    try{
            $post = array('from' => $FromEmail,
            'fromName' => $FromName,
            'apikey' => '882D1E9420DA8EFC9A20F712B96703AC6D9D06099C059D20325B91A467DB449A558C4DAD46C13DC2712D8132F35847D3',
            'subject' => $subject,
            'to' => $to,
            'bodyHtml' => $HTML,                
            'isTransactional' => false);
            
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $post,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_SSL_VERIFYPEER => false
            ));
            
            $result=curl_exec ($ch);
            $RES = json_decode($result, true);                                
            curl_close ($ch);
            
            if( $RES['success'] === false ){
                throw new Exception($RES['error']);
            }                
            
            return true;
            
    }
    catch(Exception $ex){
        return false;            
    }        
}


?>