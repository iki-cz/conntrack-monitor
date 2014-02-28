<?php
namespace App;
include_once __DIR__ . '/Arguments.php';
include_once __DIR__ . '/Parser.php';
include_once __DIR__ . '/Parser/Template/BaseTemplate.php';
include_once __DIR__ . '/Parser/Template/IParserTemplate.php';
include_once __DIR__ . '/Parser/Template/IPTrafTemplate.php';
include_once __DIR__ . '/Parser/Template/MailLogTemplate.php';
include_once __DIR__ . '/Parser/Template/ConntrackTemplate.php';
include_once __DIR__ . '/Parser/Stats/ConntrackStats.php';
include_once __DIR__ . '/Parser/Stats/StatsSorter.php';
include_once __DIR__ . '/Cache/ICache.php';
include_once __DIR__ . '/Cache/FileCache.php';
include_once __DIR__ . '/Color/Colors.php';
include_once __DIR__ . '/Killer/ConntrackKiller.php';
// include_once __DIR__ . '/Test/TestSuite.php';

use App\Arguments;
use App\Parser;
use App\Parser\Template\IPTrafTemplate;
use App\Parser\Template\MailLogTemplate;
use App\Parser\Template\ConntrackTemplate;
use App\Killer\ConntrackKiller;
use App\Cache\FileCache;

class ConntrackMonitor{
	private $parser;
	private $config;
	const CONFIG_FILE = "/../config/settings.ini";
	const CACHE_ALIAS = "/Cache/Data/aliases.json";
	const CACHE_SUBNETS = "/Cache/Data/subnets.json";
	const CACHE_CONNECTIONS = "/Cache/Data/connections.json";
	
	public function run($argv){
		// CLI arguments 
		$args = new Arguments($argv);
		$this->config = $this->getconfig();
		$this->config = $args->fill($args->getArguments(), $this->config);

		// subnets and connections config
		$fc = new FileCache(__DIR__ . self::CACHE_CONNECTIONS);
		$this->config["connections"] = $fc->getData();
		$fc = new FileCache(__DIR__ . self::CACHE_SUBNETS);
		$this->config["subnets"] = $fc->getData();
		
		switch ($this->config['action']){
			case "alias":
				$out = $this->setCacheValue(__DIR__ . self::CACHE_ALIAS, $this->config['ip'], $this->config['value']);
				break;
			case "connection":
				$out = $this->setCacheValue(__DIR__ . self::CACHE_CONNECTIONS, $this->config['ip'], $this->config['value']);
				break;
			case "subnet":
				$out = $this->setCacheValue(__DIR__ . self::CACHE_SUBNETS, $this->config['ip'], $this->config['value']);
				break;
			case "limit":
				$out = $this->setLimit($this->config['ip'], $this->config['limit']);
				break;
			case "show":
				$out = $this->show($this->config['value']);
				break;			
			case "conntrack":
			default:
				$out = $this->conntrackAction($args->getStream());
				break;
		}
		
		return $out;
	}

	private function show($what){
		switch($what){
			case "connection":
			case "connections":
				$cache = new FileCache(__DIR__ . self::CACHE_CONNECTIONS);
				break;
			case "subnet":
			case "subnets":
				$cache = new FileCache(__DIR__ . self::CACHE_SUBNETS);
				break;
			case "alias":
			case "aliases":
				$cache = new FileCache(__DIR__ . self::CACHE_ALIAS);
				break;
			default:
				return "empty\n";
		}
		$out = "";
		foreach ($cache->getData() as $key => $value){
			$out .= str_pad($key, 20, " ", STR_PAD_RIGHT) . " " . $value . "\n";
		}
		return $out . "\n";
	}	
	
	private function setCacheValue($cache, $ip, $value){
		$cache = new FileCache($cache);
		$cache->save($ip, $value);
		return "setting value for " . $ip . " to " . $value . "\n";
	}
	
	/**
	 * base conntrack monitoring
	 * @return string output
	 */
	private function conntrackAction($stream){
		$cache = new FileCache(__DIR__ . self::CACHE_ALIAS);

		$template = $this->getTemplate($this->config['template']);
		$template->setConfig($this->config)
			->setCache($cache);
		
		$this->parser = new Parser($template);
		$this->parser->setStream($stream)
			->setCache($cache)
			->parse();
		
		//var_dump($parser->getStats());
		$killer = new ConntrackKiller();
		$killer->setConfig($this->config);
		
		$out = "";
		foreach($this->parser->getStats() as $stat){
			$out .= $stat->toString() . "\n";
			$killer->check($stat->getIp(), $stat->getConnections());
		}
		$killer->sendMailInfo();
		return $out;
	}
	
	public function getParser(){
		return $this->parser;
	}
	
	private function getConfig(){
		return parse_ini_file(__DIR__ . self::CONFIG_FILE);
	}
	
	private function setConfig($config){
		
	}
	
	private function getTemplate($tempSet){
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
// 				$template->setGcMinimum($gcMin);
				break;
		}
		return $template;		
	}
}


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



// 		$fc->save("194.8.253.85", 100000);
// 		$fc->save("194.8.253.11", 100000);
// 		$fc->save("194.8.253.115", 100000);
// 		$fc->save("8.8.8.8", 100000);
// 		$fc->save("82.113.33.42", 100000);
// 		$fc->save("77.75.77.24", 15000);
// 		$fc->save("77.93.197.45", 15000);
// 		$fc->save("194.79.52.98", 10000);
// $fc->save("194.8.252.0/23", 15000);
// $fc->save("193.150.12.0/22", 15000);
// $fc->save("10.0.0.0/8", 15000);
