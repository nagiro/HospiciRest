<?php 

require_once BASEDIR."Database/DB.php";

class HorarisModel extends BDD {

    public $E;

    public function __construct() {
        $this->$E = array(
            "h.actiu as H_ACTIU",
            "h.Activitats_ActivitatID as H_ACTIVITATS_ACTIVITATID",
            "h.Avis as H_AVIS",
            "h.Dia as H_DIA",
            "h.Espectadors as H_ESPECTADORS",
            "h.Estat as H_ESTAT",
            "h.HoraFi as H_HORAFI",
            "h.HoraInici as H_HORAINICI",
            "h.HoraPost as H_HORAPOST",
            "h.HoraPre as H_HORAPRE",
            "h.HorarisID as H_HORARISID",
            "h.isEntrada as H_ISENTRADA",
            "h.Places as H_PLACES",
            "h.Preu as H_PREU",
            "h.PreuR as H_PREUR",
            "h.Responsable as H_RESPONSABLE",
            "h.site_id as H_SITE_ID",
            "h.Titol as H_TITOL"
        );


    }
}

?>