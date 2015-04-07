<?php 
namespace Tests\App;

include_once __DIR__ . '/../../App/ConntrackMonitor.php';

use \PHPUnit_Framework_TestCase;
use App\Arguments;


class ArgumentsTest extends PHPUnit_Framework_TestCase{
	public function testInputArguments(){
		$tests = array(
			//verbose + filter 
			array(
				"data" => array(
					"conntrack-monitor.php",
					"--verbose",
					"--filter",
					"194.8.253.11",
				),
				"result" => array(
					"verbose" => true,
					"filter" => "194.8.253.11",
				),
			),
			//verbose only 
			array(
				"data" => array(
					"conntrack-monitor.php",
					"--verbose",
				),
				"result" => array(
					"verbose" => true,
				),
			),
			//help 
			array(
				"data" => array(
					"conntrack-monitor.php",
					"--help",
				),
				"result" => array(
					"action" => "help",
				),
			),
			
			//parametr s výchozí hodnotou
			array(
				"data" => array(
					"conntrack-monitor.php",
					"--show",
					"alias"
				),
				"result" => array(
					"action" => "show",
					"value" => "alias",
				),
			),
			
			//parametr s výchozí hodnotou
			array(
				"data" => array(
					"conntrack-monitor.php",
					"-v",
				),
				"result" => array(
					"verbose" => true,
				),
			),
			
			//dlouhý parametry s více proměnnými
			array(
				"data" => array(
					"conntrack-monitor.php",
					"--alias",
					"194.8.253.77",
					"vps0008.best-hosting.cz",
				),
				"result" => array(
					"action" => "alias",
					"ip" => "194.8.253.77",
					"value" => "vps0008.best-hosting.cz",
				),
			),
			
			//krátký parametr
			array(
				"data" => array(
					"conntrack-monitor.php",
					"-a",
					"194.8.253.77",
					"vps0008.best-hosting.cz",
				),
				"result" => array(
					"action" => "alias",
					"ip" => "194.8.253.77",
					"value" => "vps0008.best-hosting.cz",
				),
			),
			
			//jednoduchý parametr
			array(
				"data" => array(
					"conntrack-monitor.php",
					"-m",
					500,
				),
				"result" => array(
					"minimum" => 500,
				),
			),
		);
		
		foreach ($tests as $t){
// 			var_dump($t["data"]);die;
			$args = new Arguments($t["data"]);
			$this->assertTrue($t["result"] == $args->getArguments());
		}
	}
}