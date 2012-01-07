<?php

header('Content-Type: text/html; charset=utf-8');

ini_set('display_errors', 1);

$realpath = realpath('').'/';
$realpath = str_replace("\\", "/", $realpath);

define('ENVIRONMENT', 'development');
define('BASEPATH', $realpath);
define('ABSPATH', dirname(__FILE__));

switch (ENVIRONMENT) {
    case 'development':
        error_reporting(E_ALL);
        break;
    case 'production':
        error_reporting(0);
        break;
}

include_once('core/Mikroengine.php');