<?php 
namespace App\Parser\Template;
use App\Cache\ICache;
interface IParserTemplate{
	public function parse($line);
	public function getStats();
	public function sumarize();
	public function setConfig(array $config);
	public function setCache(ICache $cache);
}