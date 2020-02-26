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

?>