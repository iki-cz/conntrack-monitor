<?php
namespace App\Parser\Template;
abstract class BaseTemplate{
	protected $config;
	
	const VAR_PATTERN = "pattern";
	const VAR_REMOVE = "remove";
	
	abstract function setupConfig();
	
	public function __construct(){
		$this->setupConfig();
	}
	
	public function getConfig()
	{
	    return $this->config;
	}
}