<?php 

require_once DATABASEDIR.'Tables/CursosModel.php';
require_once DATABASEDIR.'Tables/MatriculesModel.php';
require_once DATABASEDIR.'Tables/UsuarisSitesModel.php';


class WebApiController
{

    public $WebQueries; 
    public $DataAvui;
    public $DataFi;

    public function __construct() {
        // $this->WebQueries = new WebQueries();
        // $this->setNewDate(date('Y-m-d', time()));        
    }

    public function ExisteixDNI($DNI = '') {
        $U = new UsuarisModel();                
        return $U->ExisteixDNI($DNI);
    }

    public function getUsuariDNI($DNI) {
        $U = new UsuarisModel();
        return $U->getUsuariDNI($DNI);

    }

    public function NovaInscripcioSimple($DNI, $Nom, $Cog1, $Cog2, $Email, $Telefon, $Municipi, $Genere, $AnyNaixement, $QuantesEntrades, $ActivitatId, $CicleId) {                
        
        $OU = array();
        $UM = new UsuarisModel();
        $CM = new CursosModel();

        
        $OU = $UM->getUsuariDNI($DNI);
        // Si no hem trobat el DNI, creem l'usuari
        if(sizeof($OU) == 0) {
            
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';

            $OU = $UM->getEmptyObject(1);            
            $OU[$UM->gnfnwt('Dni')] = $DNI; 
            $OU[$UM->gnfnwt('Nom')] = $Nom; 
            $OU[$UM->gnfnwt('Password')] = substr(str_shuffle($permitted_chars), 0, 10); 
            $OU[$UM->gnfnwt('Cog1')] = $Cog1; 
            $OU[$UM->gnfnwt('Cog2')] = $Cog2; 
            $OU[$UM->gnfnwt('Genere')] = $Genere; 
            $OU[$UM->gnfnwt('PoblacioText')] = $Municipi; 
            $OU[$UM->gnfnwt('DataNaixement')] = $AnyNaixement.'-01-01';
            $OU[$UM->gnfnwt('Mobil')] = $Telefon; 
            $OU[$UM->gnfnwt('Email')] = $Email; 
            $OU[$UM->gnfnwt('Poblacio')] = 1; // Posem 1 perquè és constraint
            $OU[$UM->gnfnwt('Habilitat')] = 1;             
            
            // Inserim l'usuari nou
            $id = $UM->doInsert($OU);            
            if($id > 0) $OU = $UM->getUsuariId($id);
            else throw new Exception("No he pogut crear l'usuari amb DNI {$DNI}");         

        }

        // Vinculem l'usuari amb el site si fa falta... 
        $USM = new UsuarisSitesModel();
        $USM->addUsuariASite( $OU[ $UM->gnfnwt('IdUsuari') ], 1 ); 

        // Carreguem el curs que toca per fer la matrícula
        $OC = array(); 
        if($ActivitatId > 0) { $OC = $CM->getRowActivitatId($ActivitatId); }
        else if($CicleId > 0) { $OC = $CM->getRowCicleId($CicleId); }
        else throw new Exception("No hi ha cap activitat o cicle on registrar-se");
        if(empty($OC)) throw new Exception("No he trobat cap inscripció activa per l'activitat ({$ActivitatId}) ni el cicle ({$CicleId})");

        //Passem a gestionar la matrícula
        $MM = new MatriculesModel();

        //Mirem si l'usuari ja té alguna matrícula en aquest curs
        if($MM->getUsuariHasMatricula( $OC[$CM->gnfnwt('IdCurs')], $OU[$UM->gnfnwt('IdUsuari')] ))
            throw new Exception('Ja hi ha inscripcions per a aquest DNI a aquesta activitat/curs.');

        //Si hem trobat l'activitat, comprovem que quedin prous entrades        
        $QuantesMatricules = $MM->getQuantesMatriculesHiHa( $OC[$CM->gnfnwt('IdCurs')] );
        $Matricules = array();
        if(($QuantesMatricules + $QuantesEntrades) >= $OC[$CM->gnfnwt('Places')]) throw new Exception('No hi ha prou places disponibles.');
        else {
            for($i = 0; $i < $QuantesEntrades; $i++){
                $OM = $MM->getEmptyObject($OU[$UM->gnfnwt('IdUsuari')], $OC[$CM->gnfnwt('IdCurs')], $SiteId);
                $id = $MM->doInsert($OM);
                if( !($id > 0) ) { throw new Exception("Hi ha hagut algun problema guardant la inscripció. Consulti amb la Casa de Cultura al 972.20.20.13"); }
                else { $Matricules[] = $id; }
            }
        }
        
        return $Matricules;
        
    }

 }

 ?>
