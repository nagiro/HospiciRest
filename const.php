<?php


const PDOString = "mysql:host=127.0.0.1;dbname=intranet;charset=utf8";
const Username = 'root';
const Password = '';
const BASEDIR = "C:\Users\Usuario\Documents\Code\HospiciRest/";
const OLD_BASEDIR_IMG_ACT = "/var/www/hospici_cultural/web/images/activitats/";


const APIDIR = BASEDIR . 'Api/';
const APIDIRADMIN = APIDIR . 'admin/'; 
const APIDIRWEB = APIDIR . 'web/'; 
const AUXDIR = BASEDIR . 'Auxiliars/';

const CONTROLLERSDIR = BASEDIR . 'Controllers/';
const DATABASEDIR = BASEDIR . 'Database/';
const VIEWDIR = BASEDIR . 'View/';
const VIEWDIRMOD = VIEWDIR . 'Modules/';

const WEBFILESDIR = BASEDIR . 'WebFiles/';
const WEBFILESURL = 'WebFiles/';

const IMATGESDIR = WEBFILESDIR . 'Imatges/';
const IMATGESURL = WEBFILESURL . 'Imatges/';
const IMATGES_DIR_PROMOCIONS = IMATGESDIR . 'Promocions/';
const IMATGES_URL_PROMOCIONS = IMATGESURL . 'Promocions/';
const IMATGES_DIR_NODES = IMATGESDIR . 'Nodes/';
const IMATGES_URL_NODES = IMATGESURL . 'Nodes/';

// const IMATGES_URL_BASE = 'http://www.casadecultura.cat'; 
const IMATGES_URL_BASE = 'http://localhost:8087'; 
const IMATGES_URL_ACTIVITATS = '/images/activitats/';
const IMATGES_URL_CICLES = '/images/cicles/';
const IMATGES_URL_INSCRIPCIONS = '/WebFiles/Inscripcions/';

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
