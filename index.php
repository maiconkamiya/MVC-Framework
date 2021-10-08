<?php

define('TITLE', 'INDEX TITLE');
define('DESCRIPTION', 'INDEX DESCRIPTION');
define('KEYWORDS', 'INDEX KEYWORDS');

session_start();

require 'vendor/autoload.php';

use criativa\lib\System;

$System = new System();
$System->run();