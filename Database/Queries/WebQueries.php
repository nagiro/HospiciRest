<?php 

require_once BASEDIR . "Database/DB.php";
require_once BASEDIR . "Database/Tables/NodesModel.php";
require_once BASEDIR . "Database/Tables/ActivitatsModel.php";
require_once BASEDIR . "Database/Tables/PromocionsModel.php";

class WebQueries extends BDD {

    private $Menu;

    public function __construct() {
        parent::__construct("","",array(),array());                        
    }

    public function getMenu() {
        // Utilitzem el model, perquè aquí, utilitzem tot el model de la base de dades
        $N = new NodesModel();
        $SQL = "SELECT {$N->getSelectFieldsNames()} 
                FROM {$N->getTableName()} 
               WHERE {$N->getOldFieldNameWithTable('Actiu')} = 1
                 AND {$N->getOldFieldNameWithTable('idSite')} = 1
                 AND {$N->getOldFieldNameWithTable('isActiva')} = 1                 
                 AND {$N->getOldFieldNameWithTable('Nivell')} < 5
        ORDER BY {$N->getOldFieldNameWithTable('Ordre')} asc";        
        return $this->runQuery( $SQL , array());
    }    

    public function getNodesCerca($ParaulesCerca) {
        $N = new NodesModel();
        return $N->getNodesCerca($ParaulesCerca);
    }

    public function getHorarisActivitatDetall( $idA ) {
        return $this->runQuery( $this->getSQLHorarisActivitatDetall( $idA ) , array());
    }

    public function getCursDetall($idC) {
        $CursosModel = new CursosModel();
        return $CursosModel->getCursById($idC);
    }

    public function getActivitatsDetall( $idA ) {        
        return $this->runQuery( $this->getSQLActivitatsDetall( $idA ) , array());
    }

                                        
    public function getActivitatsHome($Cat, $Dia, $DiaF, $Site, $Activa = true, $ArrayCicles = array(), $TagsVinculatsArray = array(), $ParaulesCerca = array() ) { 
        return $this->runQuery( $this->getSQLActivitatsHome( $Cat , $Dia, $DiaF, $Site, $Activa, $ArrayCicles, $TagsVinculatsArray, $ParaulesCerca) , array());
    }

    public function getCiclesHome($idC = 0, $DataInicial = '', $DataFinal = '') {        
        return $this->runQuery( $this->getSQLCiclesHome($idC, $DataInicial, $DataFinal) , array());
    }

    public function getNoticiesHome($idS = 1, $DataInicial = '') {
        return $this->runQuery( $this->getSQLNoticies($idS, $DataInicial) , array());
    }

    public function getPromocions($empty = false, $title = '', $subtitle = '', $TipusImatge = 'A', $idImatge = '', $Link = '') {
        if(!$empty) {
            $RET = $this->runQuery( $this->getSQLPromocions() , array());                
            foreach($RET as $index => $Row) {
                $RET[$index]['PROMOCIONS_IMATGE_S'] = IMATGES_URL_PROMOCIONS . $Row['PROMOCIONS_IMATGE_S'];
                $RET[$index]['PROMOCIONS_IMATGE_M'] = IMATGES_URL_PROMOCIONS . $Row['PROMOCIONS_IMATGE_M'];
                $RET[$index]['PROMOCIONS_IMATGE_L'] = IMATGES_URL_PROMOCIONS . $Row['PROMOCIONS_IMATGE_L'];
            }
        } else {
            $P = new PromocionsModel();
            $RET[0] = $P->getEmptyArray();
            if($TipusImatge == 'A'):            
                $RET[0]['PROMOCIONS_IMATGE_S'] = IMATGES_URL_ACTIVITATS . 'A-' . $idImatge . '-M.jpg';
                $RET[0]['PROMOCIONS_IMATGE_M'] = IMATGES_URL_ACTIVITATS . 'A-' . $idImatge . '-L.jpg';
                $RET[0]['PROMOCIONS_IMATGE_L'] = IMATGES_URL_ACTIVITATS . 'A-' . $idImatge . '-XL.jpg';
            elseif($TipusImatge == 'C'):
                $RET[0]['PROMOCIONS_IMATGE_S'] = IMATGES_URL_CICLES . 'C-' . $idImatge . '-M.jpg';
                $RET[0]['PROMOCIONS_IMATGE_M'] = IMATGES_URL_CICLES . 'C-' . $idImatge . '-L.jpg';
                $RET[0]['PROMOCIONS_IMATGE_L'] = IMATGES_URL_CICLES . 'C-' . $idImatge . '-XL.jpg';
            endif;
            $RET[0]['PROMOCIONS_TITOL'] = $title;
            $RET[0]['PROMOCIONS_SUBTITOL'] = $subtitle;
            $RET[0]['PROMOCIONS_URL'] = $Link;                        
        }
            
        return $RET;
    }

    public function getTipusActivitats() {
        return $this->runQuery( $this->getSQLGetTipusActivitat() , array() );
    }

    private function getSQLGetTipusActivitat() {
        return "Select * from tipus where tipusNom = 'class_activitat' AND actiu = 1 AND site_id = 1";
    }


    public function getTagsActivitats() {
        return $this->runQuery( $this->getSQLGetTagsActivitat() , array() );
    }

    private function getSQLGetTagsActivitat() {
        return "Select * from tipusactivitat where actiu = 1 AND site_id = 1 order by Nom";
    }
    
    

    private function getSQLPromocions($idS = 1) {
        
        $PromocionsTable = new PromocionsModel();

        $SQL = "SELECT {$PromocionsTable->getSelectFieldsNames()} 
                  FROM {$PromocionsTable->getTableName()} 
                 WHERE {$PromocionsTable->getOldFieldNameWithTable('ACTIU')} = 1
                   AND {$PromocionsTable->getOldFieldNameWithTable('IS_ACTIVA')} = 1
                   AND {$PromocionsTable->getOldFieldNameWithTable('SITE_ID')} = 1
                   ORDER BY {$PromocionsTable->getOldFieldNameWithTable('ORDRE')} asc;            
            ";
        
        return $SQL;
    }

    private function getSQLNoticies($idS = 1, $Datai = '') {
        
        $SQL = " Select n.TitolNoticia as Titol, n.TextNoticia as Text, n.Imatge as Imatge, n.Adjunt as Adjunt
                FROM noticies n 
                WHERE n.actiu = 1
                AND n.site_id = {$idS}";

        if( $Datai != '' )
            $SQL .= " AND n.DataPublicacio <= '{$Datai}' AND n.DataDesaparicio >= '{$Datai}' ";
        
        $SQL .= " ORDER BY n.Ordre LIMIT 10;";
        
        return $SQL;
    }

    private function getSQLHorarisActivitatDetall( $idA ) {
        $SQL = "
        SELECT h.Dia as DIA, h.HoraInici as HORA, e.Nom as ESPAI
          FROM horaris h LEFT JOIN horarisespais he ON (h.HorarisID = he.Horaris_HorarisID)
          LEFT JOIN espais e ON (he.Espais_EspaiID = e.EspaiID)
         WHERE h.actiu = 1
           AND h.Activitats_ActivitatID = {$idA}
           AND h.site_id = 1
           ORDER BY e.Nom asc, h.Dia asc, h.HoraInici asc
          ";

      return $SQL;
    }

    private function getSQLActivitatsDetall( $idA ) {
        
        // Utilitzem el model, perquè aquí, utilitzem tot el model de la base de dades
        $ActivitatTable = new ActivitatsModel();

        $SQL = "SELECT {$ActivitatTable->getSelectFieldsNames()} 
                FROM {$ActivitatTable->getTableName()} 
                WHERE {$ActivitatTable->getOldFieldNameWithTable('ActivitatId')} = {$idA}";
        
        return $SQL;

    }

    /**
     * $Categories = array de categories ( 40, 52, etc... )
     * $dia = Dia inicial des d'on buscar
     * $site = Lloc on buscar
     * $actiu = Actiu o no
     * $CiclesArray = array de cicles a buscar ( 544, 542 )
     */
    private function getSQLActivitatsHome(
        $Categories = array(), 
        $dia = '', 
        $diaf = '',
        $site = 0, 
        $actiu = 1, 
        $CiclesArray = array(),
        $TagsVinculatsArray = array(),
        $ParaulesCerca = array()
        ){
        
        $SQL = " SELECT a.ActivitatID as idActivitat,
                a.Cicles_CicleID as idCicle,
                a.tMig as NomActivitat,
                a.Nom as NomActivitatIntern,
                a.Categories as Categories,
                (Select min(h.Dia) as Dia from horaris h where h.Activitats_ActivitatID = a.ActivitatID and h.actiu = 1) as Dia,
                (Select min(h.HoraInici) as HoraInici from horaris h where h.Activitats_ActivitatID = a.ActivitatID and h.actiu = 1) as HoraInici,
                h.HoraFi as HoraFi,
                e.Nom as NomEspai,
                (Select max(h.Dia) as DiaMax from horaris h where h.Activitats_ActivitatID = a.ActivitatID and h.actiu = 1) as DiaMax, 
                ta.CategoriaVinculada as CategoriaVinculada ";
        $SQL .= "
                FROM activitats a LEFT JOIN horaris h ON (a.ActivitatID = h.Activitats_ActivitatID)
                LEFT JOIN horarisespais he ON (he.Horaris_HorarisID = h.HorarisID)
                LEFT JOIN espais e ON (e.EspaiID = he.Espais_EspaiID)
                LEFT JOIN tipusactivitat ta ON (ta.idTipusActivitat = a.TipusActivitat_idTipusActivitat)
                WHERE a.actiu = 1 AND a.site_id = 1 AND a.PublicaWEB = 1 ";

        if(sizeof($Categories) > 0) {
            $SQL .= "AND ( 1 <> 1 ";
            foreach($Categories as $C){
                $SQL .= " OR Categories like '%".$C."%' ";
            }
            $SQL .= ") ";
        }

        if(sizeof($CiclesArray) > 0) {
            $SQL .= " AND a.Cicles_CicleID in (" . implode(",", $CiclesArray).")";                        
        }   

        if(sizeof($TagsVinculatsArray) > 0) {
            $SQL .= " AND ta.CategoriaVinculada in (" . implode(",", $TagsVinculatsArray).")";
        }   
        
        if(sizeof($ParaulesCerca) > 0) {            
            $SQL .= " AND a.tMig like '%" . implode("%", $ParaulesCerca) . "%' ";
        }   

        if(!empty($dia) && !empty($diaf)) { $SQL .= " and h.Dia >= '".$dia."' AND h.Dia <= '".$diaf."'"; }
        if(!empty($site)) { $SQL .= " and a.site_id = ".$site." "; }        
        
        $SQL .= " GROUP BY a.ActivitatID ";                                        
        $SQL .= " ORDER BY Dia";                        
                
        return $SQL;

    }

    public function getSQLCiclesHome($idC = 0, $DataInicial = '', $DataFinal = '', $extingit = 0, $site = 1, $actiu = 1){
                
        $WIDC = ($idC > 0) ? " AND c.CicleID = {$idC}" : ""; 
        if((!empty($DataInicial) && !empty($DataFinal))) { 
            $WIDC .= " AND h.Dia > '".$DataInicial."' AND h.Dia < '".$DataFinal."'"; }
        
        
        $SQL = "SELECT  '0' as idActivitat,
                        a.Cicles_CicleID as idCicle, 
                        c.tMig as NomActivitat, 
                        c.Nom as NomActivitatIntern,
                        '0' as Categories,                        
                        (Select min(h.Dia) as Dia from cicles c LEFT JOIN activitats a ON (c.CicleID = a.Cicles_CicleID) LEFT JOIN horaris h ON (a.ActivitatID = h.Activitats_ActivitatID) where c.CicleID = idCicle AND h.actiu = 1) as Dia,
                        (Select min(h.HoraInici) as HoraInici from cicles c LEFT JOIN activitats a ON (c.CicleID = a.Cicles_CicleID) LEFT JOIN horaris h ON (a.ActivitatID = h.Activitats_ActivitatID) where c.CicleID = idCicle AND h.actiu = 1) as HoraInici,
                        h.HoraFi as HoraFi, 
                        e.Nom as NomEspai, 
                        (Select max(h.Dia) as DiaMax from cicles c LEFT JOIN activitats a ON (c.CicleID = a.Cicles_CicleID) LEFT JOIN horaris h ON (a.ActivitatID = h.Activitats_ActivitatID) where c.CicleID = idCicle AND h.actiu = 1) as DiaMax,
                        c.dMig as Descripcio,
                        '0' as CategoriaVinculada
                FROM cicles c 
                LEFT JOIN activitats a on (c.CicleID = a.Cicles_CicleID)
                LEFT JOIN horaris h ON (a.ActivitatID = h.Activitats_ActivitatID)
                LEFT JOIN horarisespais he ON (he.Horaris_HorarisID = h.HorarisID)
                LEFT JOIN espais e ON (e.EspaiID = he.Espais_EspaiID)
                WHERE 1 = 1 ";
                if( $idC == 0 ) {
                    $SQL .= " AND c.actiu = 1 
                    AND c.Visibleweb = 1 
                    AND c.extingit = {$extingit}                     
                    AND c.site_id = {$site}";                    
                }
                $SQL .= $WIDC;
                $SQL .= " GROUP BY a.Cicles_CicleID ORDER BY Dia asc; ";
                
                // var_dump($SQL);
                // echo $SQL;
                return $SQL;
                
    }

}

?>