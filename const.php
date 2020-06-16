<?php


const PDOString = "mysql:host=127.0.0.1;dbname=intranet;charset=utf8";
const Username = 'root';
const Password = '';
const BASEDIR = "C:\Users\USER\Documents\Code\HospiciRest/";

const APIDIR = BASEDIR . 'Api/';
const APIDIRADMIN = APIDIR . 'admin/'; 
const APIDIRWEB = APIDIR . 'web/'; 

const CONTROLLERSDIR = BASEDIR . 'Controllers/';
const DATABASEDIR = BASEDIR . 'Database/';
const VIEWDIR = BASEDIR . 'View/';
const VIEWDIRMOD = VIEWDIR . 'Modules/';

const WEBFILESDIR = BASEDIR . 'WebFiles/';
const WEBFILESURL = 'WebFiles/';

const IMATGESDIR = WEBFILESDIR . 'Imatges/';
const IMATGESURL = WEBFILESURL . 'Imatges/';
const IMATGES_DIR_PROMOCIONS = IMATGESDIR . 'promocions/';
const IMATGES_URL_PROMOCIONS = IMATGESURL . 'promocions/';

const IMATGES_URL_ACTIVITATS = 'http://www.casadecultura.cat/images/activitats/';
const IMATGES_URL_CICLES = 'http://www.casadecultura.cat/images/cicles/';

const DOCUMENTSDIR = WEBFILESDIR . 'Documents/';
const DOCUMENTSURL = WEBFILESURL . 'Documents/';

const LOCAL_URL = "../assets/docs/";
const LOCAL_EMPRESES_FILES = LOCAL_URL . 'empreses/';
const LOCAL_USERS_FILES = LOCAL_URL . 'usuaris/';
const LOCAL_WEB_URL = "/assets/docs/";
const LOCAL_WEB_EMPRESES_URL = LOCAL_WEB_URL . "empreses/";
const LOCAL_WEB_USUARIS_URL = LOCAL_WEB_URL . "usuaris/";
// const SESSION_PATH = "C:\Users\Usuario\Documents\Code\src\BorsaLaboral\php";
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
