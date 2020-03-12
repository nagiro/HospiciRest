<?php 


class BDD extends PDO {

    public $db;   
    public $SelectFields; //Camp estructura que s'usa a tots els models
    private $OldTableName;
    private $NewTableName;
    private $OldFieldsNameArray;
    private $NewFieldsNameArray;
    private $NewFieldsWithTableArray;

    public function __construct($OldTableName, $NewTableName, $OldFieldsNameArray, $NewFieldsNameArray) {
        try {
            $this->db = new PDO( PDOString, Username, Password );            
        } catch (Exception $e) { return array($e->getMessage(), 500); }        
        $this->db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(strlen($OldTableName) > 0) $this->ConstructStructure($OldTableName, $NewTableName, $OldFieldsNameArray, $NewFieldsNameArray);        

    }    

    private function ConstructStructure($OldTableName, $NewTableName, $OldFieldsNameArray, $NewFieldsNameArray) {
        $this->OldTableName = $OldTableName;
        $this->NewTableName = $NewTableName;
        $this->OldFieldsNameArray = $OldFieldsNameArray;
        $this->NewFieldsNameArray = $NewFieldsNameArray;
        foreach($this->NewFieldsNameArray as $K => $F) {
            $this->NewFieldsWithTableArray[$K] = $this->getNewFieldNameWithTable($F);
        }
    }

    public function getSelectFieldsNames() {
        $RET = array();
        foreach($this->OldFieldsNameArray as $K => $OldField) {
            $RET[$K] = $this->OldTableName.'.'.$OldField. ' as '.$this->NewTableName.'_'.$this->NewFieldsNameArray[$K];
        }
        return implode(', ',$RET);
    }

    public function gsfn($Field) { return $this->getSelectFieldName($Field); }
    public function getSelectFieldName($Field) {
        $index = array_search($Field, $this->NewFieldsNameArray, true);
        if( $index > -1 ) {
            return $this->OldTableName.'.'.$this->OldFieldsNameArray[$index]. ' as '.$this->NewTableName.'_'.$this->NewFieldsNameArray[$index];
        } else { throw new Exception("getSelectFieldName: Camp ".$Field." no trobat..."); }        
    }

    /**
     * Retorna el camp NewTableName_NewFieldName
     */
     
    public function getOldFieldNameWithTable($Field) {        
        $index = array_search($Field, $this->NewFieldsNameArray, true);                
        if( $index > -1 ) {
            return $this->OldTableName.'.'.$this->OldFieldsNameArray[$index];
        } else { throw new Exception("getOldFieldNameWithTable: Camp ".$Field." no trobat..."); }
    }

    public function getNewFieldNameWithTable($Field, $isTableField = true) {
        $index = array_search($Field, $this->NewFieldsNameArray, true);        
        if( $index > -1 || !$isTableField ) {
            return $this->NewTableName.'_'.$Field;
        } else { throw new Exception("getNewFieldNameWithTable: Camp ".$Field." no trobat..."); }
    }

    public function getOldTableName() {
        return $this->OldTableName;
    }

    public function getNewTableName() {
        return $this->NewTableName;
    }

    public function getTableName() {
        return $this->OldTableName;
        //    return $this->OldTableName.' as '.$this->NewTableName;
    }

    public function getFromNewFieldTableNameToOldFieldTableName($FIELD) {

        $index = array_search($FIELD, $this->NewFieldsWithTableArray, true);
        if($index > -1) return $this->getOldFieldNameWithTable( $this->NewFieldsNameArray[$index] );
        else throw new Exception("getFromNewFieldTableNameToOldFieldTableName: Camp {$FIELD} no trobat." );

    }

    public function getFromNewFieldTableNameToNewFieldName($FIELD) {
        
        $index = array_search($FIELD, $this->NewFieldsWithTableArray, true);        
        if($index > -1) return $this->NewFieldsNameArray[$index];
        else throw new Exception("getFromNewFieldTableNameToNewFieldName: Camp {$FIELD} no trobat." );
    }

    public function _doUpdate($NewFieldsWithTableArray, $WhereArray) {
        //Carreguem els nous valors a guardar
        $VALUES = array();
        foreach($NewFieldsWithTableArray as $NewFieldWithTableName => $NewFieldValue) {            
            $VALUES[] = $this->getFromNewFieldTableNameToOldFieldTableName($NewFieldWithTableName).' = :'.$NewFieldWithTableName;
        }

        $W = array();        
        $WV = array();
        foreach($WhereArray as $NewFieldName){            
            $W[]  = $this->getOldFieldNameWithTable($NewFieldName).' = :W_'.$NewFieldName;
            $WV[':W_'.$NewFieldName] = $NewFieldsWithTableArray[$this->getNewFieldNameWithTable($NewFieldName)];
        }                
        
        $SQL = "UPDATE {$this->OldTableName} SET ".implode(", ", $VALUES).' WHERE '.implode(' AND ', $W);
        
        return $this->runQuery($SQL, array_merge($NewFieldsWithTableArray, $WV), false, false, 'U');
    }

    /* Per a mi esborrar és marcar com a "Actiu =  */
    public function _doDelete($NewFieldsWithTableArray, $WhereArray) {        
        //Carreguem els nous valors a guardar
        $VALUES = array();        
        foreach($NewFieldsWithTableArray as $NewFieldWithTableName => $NewFieldValue) {            
            $VALUES[] = $this->getFromNewFieldTableNameToOldFieldTableName($NewFieldWithTableName).' = :'.$NewFieldWithTableName;
        }

        $W = array();        
        $WV = array();
        foreach($WhereArray as $NewFieldName){            
            $W[]  = $this->getOldFieldNameWithTable($NewFieldName).' = :W_'.$NewFieldName;
            $WV[':W_'.$NewFieldName] = $NewFieldsWithTableArray[$this->getNewFieldNameWithTable($NewFieldName)];
        }                
        
        $SQL = "UPDATE {$this->OldTableName} SET ".implode(", ", $VALUES).' WHERE '.implode(' AND ', $W);
        
        return $this->runQuery($SQL, array_merge($NewFieldsWithTableArray, $WV), false, false, 'U');
    }


    public function runQuery($Select, $Params, $getOne = false, $consulta = true, $tipus = '') {
               
       if(is_null($this->db)) throw new Exception("No he pogut connectar-me a la base de dades");
              
       $dbs = $this->db->prepare($Select);
       // var_dump($dbs->debugDumpParams());
              
       $Affected_Rows = $dbs->execute($Params);                                      
       if($consulta && $dbs->rowCount() > 0){
           $RET = $dbs->fetchAll(PDO::FETCH_ASSOC);
           if($getOne && isset($RET[0])) return $RET[0];
           else return $RET;
       } elseif(!$consulta) {
           if ($Affected_Rows > 0 && $tipus == 'A') return $this->db->lastInsertId();
           elseif($Affected_Rows == 0 && $tipus == 'A') throw new PDOException("No he pogut inserir la nova fila");
           elseif($tipus != 'A') return $Affected_Rows;
       } else {
           return array();
       }              

    }

    public function fields() {
        return implode(", ", $this->SelectFields);
    }

}


?>