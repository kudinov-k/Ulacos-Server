<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Max-Age: 86400');
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header('Access-Control-Allow-Headers: Content-Type, Authorization');

date_default_timezone_set('UTC');

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection(array(
	'driver'    => 'mysql',
	'host'      => 'localhost',
	'database'  => 'ulacos',
	'username'  => 'root',
	'password'  => 'root',
	'charset'   => 'utf8',
	'collation' => 'utf8_general_ci',
	'prefix'    => ''
));
$capsule->setAsGlobal();
$capsule->bootEloquent();

define('APP_ROOT', realpath(__DIR__));
define('APP_MODULES', APP_ROOT . '/modules');

$app = new \Slim\Slim();
$app_config = array(
	'debug'          => TRUE,
	'templates.path' => 'templates'
);
$app->config($app_config);
