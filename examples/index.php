<?php

define('TITLE', 'INDEX TITLE');
define('DESCRIPTION', 'INDEX DESCRIPTION');
define('KEYWORDS', 'INDEX KEYWORDS');

session_start();

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