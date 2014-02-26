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
	private $getHost;
	private $host;
	private $destinations = array();
	private $rating = 0;
	private $cache;
	
	public function __construct($ip, $cons, $getHost = false, ICache $cache = null){
		$id = $this->ip = $ip;
		$this->cons = $cons;
		
		$this->getHost = $getHost;
		$this->cache = $cache;
	}
	
	public function addDestination($dest){
		if(isset($this->destinations[$dest])){
			$this->destinations[$dest]++;
		}else{
			$this->destinations[$dest] = 1;
		}
	}
	
	public function addRating($rating){
		$this->rating += $rating;
		return $this;
	}
	
	public function getRating(){
		return $this->rating;
	}
	
	public function toString(){
		$c = new Colors();
		
// 		$dst = round(count($this->getDestinations) / $this->cons * 100);
		//hodnocení zaokrouhli rating + počet destinací * 10 / počet konexí
 		$rating = round(($this->rating + count($this->destinations) * 10 ) / $this->cons);
 		
 		// IP address    connections   destinations    rank
		return 	str_pad($this->ip, 15, " ", STR_PAD_LEFT) . " " . 
				str_pad($this->cons, 6, " ", STR_PAD_LEFT) . " " . 
				str_pad(count($this->destinations), 6, " ", STR_PAD_LEFT) . " " . 
// 				$c->getColoredString(str_pad($dst, 5, " ", STR_PAD_LEFT) . "% " , $c->intToColor($dst)) . " " .
// 				$c->getColoredString(str_pad($rating, 5, " ", STR_PAD_LEFT) . "% " , $c->intToColor($rating)) . " " .
				str_pad($this->rating, 10, " ", STR_PAD_LEFT) . "   " . 
				$this->getHost();
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
		//má se generovat host
		$id = $this->ip;
		if($this->getHost){
// 			var_dump("getting host " . $id . "\n");
			if($this->cache instanceof ICache){
				if($this->cache->hit($id)){
					$value = $this->cache->get($id);
// 					var_dump("cache hit");
				}else{
// 					var_dump("no cache hit");
					$value = gethostbyaddr($id);
					$this->cache->save($id, $value);
				}
			}else{
// 				dump("host by ip");
				$value = gethostbyaddr($id);
			}
			$this->host = $value;
		}
		return $this->host;
	}
}