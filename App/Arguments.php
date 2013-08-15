<?php
namespace App;
class Arguments{
	private $arguments;
	
	public function __construct($argv){
//  		var_dump($argv);die;
	}
	
	public function get($name, $default){
		return $default;
	}
	
	public function getStream(){
		return fopen("php://stdin", "r");
	}
}