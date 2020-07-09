<?php 


class BDD extends PDO {

    public $db;   
    public $SelectFields; //Camp estructura que s'usa a tots els models
    private $OldTableName;
    private $NewTableName;
    private $OldFieldsNameArray;
    public $NewFieldsNameArray;
    public $NewFieldsWithTableArray;
    // abstract function getEmptyObject();

    public function __construct($OldTableName, $NewTableName, $OldFieldsNameArray, $NewFieldsNameArray) {
                
        $this->db = new PDO( PDOString, Username, Password );                    
        $this->db->setAttribute( PDO::ATTR_EMULATE_PREPARES, false );
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if(strlen($OldTableName) > 0) $this->ConstructStructure($OldTableName, $NewTableName, $OldFieldsNameArray, $NewFieldsNameArray);        

    }            

    public function getDefaultObject() {
        $O = array();        
        $select = $this->db->query("select * from information_schema.columns where table_name = '{$this->OldTableName}' and table_schema = 'intranet'");
        $select->execute();
        while( $F = $select->fetch() ){                        
            $valor = $F['COLUMN_DEFAULT'];
            
            if($F['DATA_TYPE'] == 'tinyint' || $F['DATA_TYPE'] == 'int' || $F['DATA_TYPE'] == 'bigint') $valor = intval($valor);
            if($F['EXTRA'] == 'auto_increment') $valor = '';

            $O[ $this->getFromOltFieldNameToNewFieldNameWithTable($F['COLUMN_NAME']) ] = $valor;                        
        }
        return $O;        
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
    public function gofnwt($Field) { return $this->getOldFieldNameWithTable($Field); }
    public function getOldFieldNameWithTable($Field) {                
        $index = array_search($Field, $this->NewFieldsNameArray, true);                
        if( $index > -1 ) {
            return $this->OldTableName.'.'.$this->OldFieldsNameArray[$index];
        } else { throw new Exception("getOldFieldNameWithTable: Camp ".$Field." no trobat..."); }
    }

    public function getAllNewFieldsNameWithTable() {
        return $this->NewFieldsWithTableArray;
    }

    public function gnfnwt($Field, $isTableField = true) {
        return $this->getNewFieldNameWithTable($Field, $isTableField);
    }

    public function getNewFieldNameWithTable($Field, $isTableField = true) {
        $index = array_search($Field, $this->NewFieldsNameArray, true);                
        if( $index > -1 || !$isTableField ) { return $this->NewTableName.'_'.$Field; } 
        else { throw new Exception("getNewFieldNameWithTable: Camp ".$Field." no trobat..."); }
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

    private function getFromOltFieldNameToNewFieldNameWithTable($OldField) {
        
        $index = array_search($OldField, $this->OldFieldsNameArray, true);        
        if($index > -1) return $this->NewFieldsWithTableArray[$index];
        else throw new Exception("getFromOltFieldNameToNewFieldNameWithTable: Camp {$OldField} no trobat." );
    }

    /**
     * EXCEPTION CODE 1 //No ha trobat cap fila
     * */
    public function _getRowWhere($W, $multiple = false) {        
        $WHERE = array();
        $SQL = "Select {$this->getSelectFieldsNames()} 
        from {$this->getTableName()}
        where 1 = 1 "; 
        foreach($W as $K => $V) {
            $PdoKey = str_replace(".", "_", $K);            
            if(is_array($V)) {
                $SQL .= " AND {$K} in (".implode(',', $V).")";            
            } else {
                $SQL .= " AND {$K} = :{$PdoKey}";            
                $WHERE[ $PdoKey ] = $V;
            }
            
        }
        
        $RET = $this->runQuery($SQL, $WHERE, !$multiple);                
        return $RET;        

    }

    /* Funció per a fer un insert */
    public function _doInsert($NFWTAV) {
        //Carreguem els nous valors a guardar
        $FIELDS = array();
        $VALUES = array();
        $VALUES_VAL = array();
        
        foreach($this->NewFieldsWithTableArray as $NewFieldWithTableName) {                        
            $FIELDS[] = $this->getFromNewFieldTableNameToOldFieldTableName($NewFieldWithTableName);
            $VALUES[] = ':'.$NewFieldWithTableName;
            $VALUES_VAL[':'.$NewFieldWithTableName] = $NFWTAV[$NewFieldWithTableName];            
        }       
        
        $SQL = "INSERT INTO {$this->OldTableName} (" . implode(',', $FIELDS) .") VALUES (". implode(",", $VALUES) .")";
        
        return $this->runQuery($SQL, array_merge($VALUES_VAL), false, false, 'A');
    }

    /**
     * NFWTAV: Objecte[NewFieldNameWithTable] => Value
     * WhereArray: NewFieldName => Value
     */
    public function _doUpdate($NFWTAV, $WhereArray) {
        //Carreguem els nous valors a guardar
        $VALUES = array();
        $VALUES_VAL = array();
        
        foreach($this->NewFieldsWithTableArray as $NewFieldWithTableName) {                        
            $VALUES[] = $this->getFromNewFieldTableNameToOldFieldTableName($NewFieldWithTableName).' = :'.$NewFieldWithTableName;            
            $VALUES_VAL[':'.$NewFieldWithTableName] = $NFWTAV[$NewFieldWithTableName];            
        }

        $W = array();        
        $WV = array();
        foreach($WhereArray as $NewFieldName){            
            $W[]  = $this->getOldFieldNameWithTable($NewFieldName).' = :W_'.$NewFieldName;
            $WV[':W_'.$NewFieldName] = $NFWTAV[$this->getNewFieldNameWithTable($NewFieldName)];
        }                
        
        $SQL = "UPDATE {$this->OldTableName} SET ".implode(", ", $VALUES).' WHERE '.implode(' AND ', $W);
        return $this->runQuery($SQL, array_merge($VALUES_VAL, $WV), false, false, 'U');
    }

    /* Per a mi esborrar és marcar com a "Actiu =  */
    public function _doDelete($NewFieldsWithTableArrayValues, $WhereArray) {        
        //Carreguem els nous valors a guardar
        $VALUES = array();                

        foreach($this->NewFieldsWithTableArray as $NewFieldWithTableName => $NewFieldValue) {
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
                
        $Affected_Rows = $dbs->execute($Params);                                      
        // var_dump($dbs->debugDumpParams());
        // var_dump($Params);       
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