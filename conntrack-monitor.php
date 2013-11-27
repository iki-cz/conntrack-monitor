<?php 
include_once __DIR__ . '/App/Arguments.php';
include_once __DIR__ . '/App/Parser.php';
include_once __DIR__ . '/App/Parser/Template/BaseTemplate.php';
include_once __DIR__ . '/App/Parser/Template/IParserTemplate.php';
include_once __DIR__ . '/App/Parser/Template/IPTrafTemplate.php';
include_once __DIR__ . '/App/Parser/Template/MailLogTemplate.php';
include_once __DIR__ . '/App/Parser/Template/ConntrackTemplate.php';
include_once __DIR__ . '/App/Parser/Stats/ConntrackStats.php';
include_once __DIR__ . '/App/Cache/ICache.php';
include_once __DIR__ . '/App/Cache/FileCache.php';
include_once __DIR__ . '/App/Color/Colors.php';
include_once __DIR__ . '/App/Killer/ConntrackKiller.php';

use App\Arguments;
use App\Parser;
use App\Parser\Template\IPTrafTemplate;
use App\Parser\Template\MailLogTemplate;
use App\Parser\Template\ConntrackTemplate;
use App\Killer\ConntrackKiller;
$config = parse_ini_file(__DIR__ . '/config/settings.ini');

$args = new Arguments($argv);
$gcMin = $args->get("minimum", $config['gc_minimum']);
$tempSet = $args->get("template", $config['template']);
$mode = $config['mode'];

switch($tempSet){
	case "iptraf":
		$template = new IPTrafTemplate();
		break;
	case "maillog":
		$template = new MailLogTemplate();
		break;
	default:
	case "conntrack":
		$template = new ConntrackTemplate();
		$template->setGcMinimum($gcMin);
		break;
}

$parser = new Parser($template);
$parser->setStream($args->getStream())
	->parse();

//var_dump($parser->getStats());
$killer = new ConntrackKiller();
$killer->setConfig($config);
	

foreach($parser->getStats() as $stat){
	echo $stat->toString() . "\n";
	
	$killer->check($stat->getIp(), $stat->getConnections());
}
$killer->sendMailInfo();

/*
$connectionsTemplate = new ConnectionsTemplate();
$connectionsTemplate->setGcMinimum($gcMin); 

$scanPortTemplate = new ScanPortTemplate();

$parser = new Parser();
$parser->addTemplate($connectionsTemplate)
	->addTemplate($scanPortTemplate)
	->setStream($args->getStream())
	->parse();

foreach($parser->getStats() as $stat){
	echo $stat->toString() . "\n";
}
*/




?>