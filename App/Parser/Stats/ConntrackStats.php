<?php
namespace App\Parser\Stats;
use App\Cache\FileCache;
use App\Cache\ICache;
use App\Color\Colors;

/**
 *
 */
class ConntrackStats{
	private $ip;
	private $cons;
	private $host;
	private $destinations = array();
	
	public function __construct($ip, $cons, $getHost = false, ICache $cache = null){
		$id = $this->ip = $ip;
		$this->cons = $cons;
		
		//má se generovat host
		if($getHost){
			if($cache instanceof ICache){
				if($cache->hit($id)){
					$value = $cache->get($id);
				}else{
					$value = gethostbyaddr($id);
					$cache->save($id, $value);
				}
			}else{
				$value = gethostbyaddr($id);
			}
			$this->host = $value;
		}
	}
	
	public function addDestination($dest){
		if(isset($this->destinations[$dest])){
			$this->destinations[$dest]++;
		}else{
			$this->destinations[$dest] = 1;
		}
	}
	
	public function toString(){
		$c = new Colors();
		
		$dst = round(count($this->getDestinations()) / $this->cons * 100);
		return 	str_pad($this->ip, 15, " ", STR_PAD_LEFT). " " . 
				str_pad($this->cons, 6, " ", STR_PAD_LEFT) . " " . 
				$c->getColoredString(str_pad($dst, 5, " ", STR_PAD_LEFT) . "% " , $c->intToColor($dst)) . " " . 
				$this->host;
	}
	
	public function getIp(){
		return $this->ip;
	}
	
	public function getDestinations(){
		return $this->destinations;
	}
	
	public function getConnections(){
		return $this->cons;
	}
	
	public function addConnection(){
		if(empty($this->cons)){
			$this->cons = 0;
		}
		$this->cons++;
	}
	
	public function getHost(){
		return $this->host;
	}
}