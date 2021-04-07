<?php


const PDOString = "mysql:host=127.0.0.1;dbname=intranet;charset=utf8";
const Username = 'root';
const Password = '';
const BASEDIR = "C:\Users\Usuario\Documents\Code\HospiciRest/";
# const BASEDIR = "C:\Users\Usuario\Documents\Code\HospiciRest\\";
//const OLD_BASEDIR_IMG_ACT = "/var/www/hospici_cultural/web/images/activitats/";
const OLD_BASEDIR_IMG_ACT = "C:\Users\Usuario\Documents\Code\HospiciRest\WebFiles\Imatges\Activitats/";
const OLD_BASEDIR_IMG_CUR = "C:\Users\Usuario\Documents\Code\HospiciRest\WebFiles\Imatges\Cursos/";


const APIDIR = BASEDIR . 'Api/';
const APIDIRADMIN = APIDIR . 'admin/'; 
const APIDIRWEB = APIDIR . 'web/'; 
const AUXDIR = BASEDIR . 'Auxiliars/';
const TEATRES = AUXDIR . 'Teatres/';

const CONTROLLERSDIR = BASEDIR . 'Controllers/';
const DATABASEDIR = BASEDIR . 'Database/';
const VIEWDIR = BASEDIR . 'View/';
const VIEWDIRMOD = VIEWDIR . 'Modules/';

const WEBFILESDIR = BASEDIR . 'WebFiles/';
const WEBFILESURL = '/WebFiles/';

const IMATGESDIR = WEBFILESDIR . 'Imatges/';
const IMATGESURL = WEBFILESURL . 'Imatges/';
const IMATGES_DIR_PROMOCIONS = IMATGESDIR . 'Promocions/';
const IMATGES_URL_PROMOCIONS = IMATGESURL . 'Promocions/';
const IMATGES_DIR_ACTIVITATS_NW = IMATGESDIR . 'Activitats/';
const IMATGES_URL_ACTIVITATS_NW = IMATGESURL . 'Activitats/';
const IMATGES_DIR_NODES = IMATGESDIR . 'Nodes/';
const IMATGES_URL_NODES = IMATGESURL . 'Nodes/';
const IMATGES_DIR_CURSOS = IMATGESDIR . 'Cursos/';
const IMATGES_URL_CURSOS = IMATGESURL . 'Cursos/';

// const IMATGES_URL_BASE = 'http://www.casadecultura.cat'; 
const IMATGES_URL_BASE = 'http://localhost:8087'; 
const IMATGES_URL_ACTIVITATS = '/images/activitats/';
const IMATGES_URL_CICLES = '/images/cicles/';
const IMATGES_URL_INSCRIPCIONS = '/WebFiles/Inscripcions/';

const IMATGES_URL_ESPAIS = '/WebFiles/Imatges/Espais/';
const IMATGES_DIR_ESPAIS = BASEDIR . '/WebFiles/Imatges/Espais/';

const DOCUMENTSDIR = WEBFILESDIR . 'Documents/';
const DOCUMENTSURL = WEBFILESURL . 'Documents/';

const SESSION_TIME = 900;

const EXCEPTION_CODE_NO_ROW_FIND = 1;

/*
const PDOString = "mysql:host=127.0.0.1;dbname=cryjzmpucv;charset=utf8";
const Username = 'cryjzmpucv';
const Password = 'QdNNe4tT6J';
const LOCAL_URL = '/home/master/applications/cryjzmpucv/public_html/assets/docs';
const LOCAL_WEB_URL = "/assets/docs/";
*/
?>
