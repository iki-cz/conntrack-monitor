<?php 
include_once __DIR__ . '/App/ConntrackMonitor.php';
use App\ConntrackMonitor;


// function __autoload($class_name){
// 	include_once __DIR__ . "/" . str_replace("\\", "/", $class_name) . '.php';
// }

$c = new ConntrackMonitor();
echo $c->run($argv);