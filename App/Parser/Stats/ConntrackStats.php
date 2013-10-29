<?php
namespace App\Parser\Stats;
use App\Cache\FileCache;
use App\Cache\ICache;

/**
 *
 */
class ConntrackStats{
	private $ip;
	private $cons;
	private $host;
	
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
	
	public function toString(){
		return str_pad($this->ip, 15). " " . str_pad($this->cons, 6) . " " . $this->host;
	}
}