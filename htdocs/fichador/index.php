<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
//Establecemos zona horaria por defecto
date_default_timezone_set('Europe/Madrid');

session_start(); 
define('APP_NAME', 'fichador');
$arr = explode('/', $_SERVER['DOCUMENT_ROOT'] ) ;
define('FOLDER_ROOT' , str_replace(array_pop($arr),'',$_SERVER['DOCUMENT_ROOT']) );
define('FOLDER_APP' ,   FOLDER_ROOT     . "app/" . APP_NAME . "/");
define('FOLDER_CORE' ,  FOLDER_APP      . "core/" );
define('FOLDER_VENDOR', FOLDER_ROOT     . 'vendor/');
define('FOLDER_VIEWS',  FOLDER_APP      . 'views/');

//Se inicia session que esta en clase segurity
$url_base = str_replace('htdocs', '', $_SERVER['DOCUMENT_ROOT']);
//configuracion general 
require(FOLDER_VENDOR . 'autoload.php');
$file_env = $_SERVER["HTTP_HOST"] == "localhost" ? '.env.local' : '.env';
$dotenv = Dotenv\Dotenv::createImmutable(FOLDER_ROOT, $file_env);
$env = $dotenv->load();
$conf = parse_ini_file(FOLDER_APP . "config/conf.ini");
$router = new \app\fichador\core\Controller(FOLDER_VIEWS, 'login' , 'phtml', $conf);
$router->route();