<?php 
include_once './App/Arguments.php';
include_once './App/Parser.php';
use App\Arguments;
use App\Parser;

$config = parse_ini_file('./config/settings.ini');

$args = new Arguments($argv);
$gcMin = $args->get("m", $config['gc_minimum']);

$parser = new Parser();
$parser->setGcMinimum($gcMin)
	->setStream($args->getStream())
	->parse();

//var_dump($parser->getStats());
foreach($parser->getStats() as $key => $stat){
	echo $key . " " . $stat . "\n";
}






























?>