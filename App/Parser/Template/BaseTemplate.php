<?php
namespace App\Parser\Template;
/**
 * 
 *
 */
abstract class BaseTemplate{
	protected $config;

	public function getConfig() {
		return $this->config;
	}

	public function setConfig($config) {
		$this->config = $config;
		return $this;
	}
	
	
// 	const VAR_PATTERN = "pattern";
// 	const VAR_REMOVE = "remove";
	
// 	abstract function setupConfig();
	
// 	public function __construct(){
// 		$this->setupConfig();
// 	}
	
// 	public function getConfig()
// 	{
// 	    return $this->config;
// 	}
}