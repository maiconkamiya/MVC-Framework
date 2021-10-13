<?php
session_start();

define('RAIZ_PATH', '');
define('APP_ROOT', 'http'.(isset($_SERVER['HTTPS']) ? (($_SERVER['HTTPS']=="on") ? "s" : "") : "") .'://' . $_SERVER['HTTP_HOST'] . '/'. RAIZ_PATH);

define('TITLE', 'INDEX TITLE');
define('DESCRIPTION', 'INDEX DESCRIPTION');
define('KEYWORDS', 'INDEX KEYWORDS');


require 'vendor/autoload.php';

use criativa\lib\Config;
use criativa\lib\Router;
use criativa\lib\System;

Config::setConfig((object) array(
    'prefix' => 'tab',
    'host' => 'localhost',
    'user' => 'root',
    'pwd' => '',
    'dbname' => 'teste',
    'charset' => 'utf8'
));

Router::setRouters(array(
    'web' => 'web'
));

Router::setRouterOnDefault('web');

$System = new System();
$System->run();