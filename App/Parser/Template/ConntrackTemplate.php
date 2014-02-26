<?php 
namespace App\Parser\Template;
use App\Parser\Template\BaseTemplate;
use App\Parser\Template\IParserTemplate;
use App\Parser\Stats\ConntrackStats;
use App\Cache\FileCache;
use App\Cache\ICache;

/**
 *
 */
class ConntrackTemplate extends BaseTemplate implements IParserTemplate{
	private $rawStats = array();
	private $stats = array();
	private $gcMinimum = 100;
	private $cache;
	private $getHosts = true;
	
	/**
	 * get IPs from line
	 * @param string $line
	 */
	public function parse($line){
		//udp      17 12 src=10.17.224.60 dst=194.8.253.11 sport=22 dport=53 src=194.8.253.11 dst=10.17.224.60 sport=53 dport=22 mark=0 use=1
		
		// ke každé IP se ++ načítá konexe
// 		$matches = array();
// 		preg_match_all("/[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+/", $line, $ips);
// 		if(isset($ips[0]) && is_array($ips[0])){
// 			foreach ($ips[0] as $ip){
// 				$this->createRawStats($ip);
// 			}
// 		}
		
		//první dst a první src se parsuje, aby to dostalo nějaké informace		
		preg_match_all("/dst=[0-9\.]+/", $line, $dsts);
		preg_match_all("/src=[0-9\.]+/", $line, $srcs);
		preg_match_all("/sport=[0-9\.]+/", $line, $sports);
		preg_match_all("/dport=[0-9\.]+/", $line, $dports);
		
		//zbaví se divného array z preg_match_all
		if(isset($dsts[0])){ $dsts = $dsts[0]; }
		if(isset($srcs[0])){ $srcs = $srcs[0]; }
		if(isset($sports[0])){ $sports = $sports[0]; }
		if(isset($dports[0])){ $dports = $dports[0]; }
		
		$ips = array();
		foreach ($srcs as $ip){
			$ip = str_replace(array("src=", "dst="), "", $ip);
			$ips[$ip] = $ip;
		}
		foreach ($dsts as $ip){
			$ip = str_replace(array("src=", "dst="), "", $ip);
			$ips[$ip] = $ip;
		}

		$ports = array();
		foreach ($sports as $port){
			$port = str_replace(array("sport=", "dport="), "", $port);
			$ports[$port] = $port;
		}
		foreach ($dports as $port){
			$port = str_replace(array("sport=", "dport="), "", $port);
			$ports[$port] = $port;
		}
		
		$rating = 0;
		foreach ($ports as $port){
			$rating += $this->getRating($port);
		}
		
		foreach ($ips as $ip){
			$this->createRawStats($ip);
			$this->addRating($ip, $rating);
			$this->addDestinations($ip, $ips);
		}
// 		$this->addRating($src, $this->getRating($dport)); // 100 bodů
		
// 		var_dump($ips);
// 		var_dump($ports);
// 		die;
// 		var_dump($dsts);
// 		var_dump($srcs);
// 		var_dump($sports);
// 		var_dump($dports);
// 		die;
		/*
		for($i = 0; $i < 2; $i++){
			if(substr($line, 0, 4) == "icmp"){
				var_dump("icmpline: " . $line);
				break;
			}
			
// 			var_dump(array($i, $dsts, $srcs, $dports, $sports));
			if(!isset($dsts[$i])){ die("1: " . $line);}
			if(!isset($srcs[$i])){ die("2: " . $line);}
			if(!isset($dports[$i])){ die("3: " . $line);}
			if(!isset($sports[$i])){ die("4: " . $line);}
			
			$src = str_replace("src=", "", $srcs[$i]);
			$dst = str_replace("dst=", "", $dsts[$i]);
			$sport = str_replace("sport=", "", $sports[$i]);
			$dport = str_replace("dport=", "", $dports[$i]);

			/ *
tcp      6 12 TIME_WAIT src=125.65.245.146 dst=194.8.252.174 sport=58922 dport=22 src=194.8.252.174 dst=125.65.245.146 sport=22 dport=58922 [ASSURED] mark=0 use=1
				
			src + rank(dstport1)
			dst 
			
			// port scan - za každou DST dostane body + 
			
			* /
			
			$this->createRawStats($src);
			$this->addDestination($src, $dst);
			$this->addRating($src, $this->getRating($sport)); // 10 bodů
			$this->addRating($src, $this->getRating($dport)); // 100 bodů

			$this->createRawStats($dst);
			$this->addDestination($dst, $src);
			$this->addRating($dst, $this->getRating($dport)); // 100 bodů
			$this->addRating($dst, $this->getRating($sport)); // 10 bodů
		}
		*/
		
		
// 		var_dump($this->rawStats);
// 		die;
		
// 		$dst = "";
// 		for($i = 0; $i < 2; $i++){
// 			if(isset($dsts[$i])){
// 				$dst = str_replace("dst=", "", $dsts[$i]);
// 				$sou = "";
// 				if(isset($srcs[$i])){
// 					$sou = str_replace("src=", "", $srcs[$i]);
// 					$this->addDestination($dst, $sou);
// 	 				$this->addDestination($sou, $dst);
// 	 				//var_dump($dst . " " . $i);
// 				}
// 			}
// 		}
// // var_dump($this->rawStats);die;
// 		//bodové ohodnocení za informace
// 		$this->addRating($ip, $this->getRating($line));
	}
	
	private function getRating($port){
		$rating = 0;
// 		//udp      17 5 src=193.150.12.80 dst=194.8.253.11 sport=38526 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=38526 mark=0 use=1
// 		preg_match_all("/port=[0-9\.]+/", $line, $ports);
// 		$ports = $ports[0];
// 		foreach ($ports as $key => $port){
// 			$ports[$key] = str_replace("port=", "", $port);
// 		}
// 		foreach ($ports as $port){

		//udp      17 12 src=10.17.224.60 dst=194.8.253.11 sport=800 dport=22 src=194.8.253.12 dst=10.17.224.61 sport=54 dport=23 mark=0 use=1
		// src (dport) + 100, sport (+10)
		// dst (
		switch ($port){
			case 21://ftp
			case 22://ssh
				$rating += 100;
				break;
			case 25://emaily
				$rating += 5;
				break;
			case ($port <= 1024): //standardní porty
				$rating += 10;
				break;
			default:
				$rating += 1;
		}

// 		}
		return $rating;
	}
	
	public function sumarize(){
		//var_dump($this->rawStats);die;
		$this->collectGarbage($this->gcMinimum);
		$this->sort();
	}

	public function getStats(){
		return $this->rawStats;
	}
	
	public function setCache(ICache $cache){
		$this->cache = $cache;
		return $this;
	}
	
	public function getCache(){
		return $this->cache;
	}
	
	private function addDestinations($ip, $destinations){
		foreach ($destinations as $dst){
			if($dst != $ip){
				$this->addDestination($ip, $dst);
			}
		}
	}
	
	private function addDestination($ip, $destination){
		$stats = $this->rawStats[$ip];
		$stats->addDestination($destination);
	}
	
	private function addRating($ip, $rating){
		$stats = $this->rawStats[$ip];
		$stats->addRating($rating);
	}
	
// 		$fileCache = new FileCache();

// 		foreach ($this->getRawStats() as $key => $value){
//			$this->stats[] = new ConntrackStats($key, $value, true, $fileCache);
// 			$this->stats[] = new ConntrackStats($key, $value);
// 		}

	
	
	//tcp      6 117 TIME_WAIT src=193.150.12.80 dst=37.157.197.240 sport=48604 dport=25 src=37.157.197.240 dst=193.150.12.80 sport=25 dport=48604 [ASSURED] mark=0 use=1
	private function parseScanPort($line){
		if(!empty($dst) && !empty($sou)){
// 			if(isset($this->rawStats[$dst])){
// 				$this->rawStats[$dst]["p"] = 
// 			}
		}
	}
	
	/**
	 * sort rawStats
	 */
	private function sort(){
// 		$out = array();
// 		foreach ($this->rawStats as $stat){
// 			$i = 0;
// 			while(isset($out[$stat->getConnections() + $i])){
// 				$i++;
// 			}
// 			$out[$stat->getConnections() + $i] = $stat;
// 		}
// 		$this->rawStats = asort($out);
// 		var_dump($this->rawStats);
		//TODO seradit
// 		arsort($this->rawStats);
	}
	
	/**
	 * delete rawStats lower than minimum
	 * @param integer $minimum
	 */
	private function collectGarbage($minimum){
// 		die($minimum);
		foreach ($this->rawStats as $key => $rawStats){
			if($rawStats->getConnections() < $minimum){
// 				var_dump($rawStats->getConnections());
				unset($this->rawStats[$key]);
			}
// 			else{
// 				var_dump($rawStats->getConnections());
// 			}
		}
	}
	
	/**
	 * count rawStats for IP
	 * @param string $ip
	 */
	private function createRawStats($ip){
		if(isset($this->rawStats[$ip])){
			$stat = $this->rawStats[$ip];
			$stat->addConnection();
			$this->rawStats[$ip] = $stat;
		}else{
			$this->rawStats[$ip] = new ConntrackStats($ip, 1, $this->getHosts, $this->getCache());
		}
	}
	
	public function getRawStats(){
		return $this->rawStats;
	}
	
	public function getGcMinimum(){
		return $this->gcMinimum;
	}
	
	public function setGcMinimum($gcMinimum){
		$this->gcMinimum = $gcMinimum;
		return $this;
	}
	
	/* (non-PHPdoc)
	 * @see \App\Parser\Template\IParserTemplate::setConfig()
	 */
	public function setConfig(array $config) {
		if(isset($config['getHosts'])){
			$this->getHosts = ($config['getHosts']);
		}
		if(isset($config['minimum'])){
			$this->setGcMinimum($config['minimum']);
		}
		
		return $this;
	}

}
	//public function setupConfig(){
/*
		$config = 
			array(
				"tcp-udp" => 
					array(
						parent::VAR_PATTERN => "//",
						parent::VAR_REMOVE => 
							array(
								"/^tcp/",
								"/^udp/",
								)
					),
				"src" =>
					array(
						parent::VAR_PATTERN => "/src=/",
						parent::VAR_REMOVE =>
							array(
								"/^src=/",
							)
					),
				"dst" =>
					array(
						parent::VAR_PATTERN => "/dst=/",
						parent::VAR_REMOVE =>
							array(
								"/^dst=/"
							)
					),
			);
		$this->config = $config;
	*/	
//	}

/*
udp      17 11 src=10.16.20.101 dst=157.55.235.162 sport=5635 dport=40027 src=157.55.235.162 dst=194.8.252.66 sport=40027 dport=5635 mark=0 use=1
udp      17 5 src=193.150.12.80 dst=194.8.253.11 sport=38526 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=38526 mark=0 use=1
tcp      6 86 TIME_WAIT src=10.17.236.1 dst=31.222.68.38 sport=53602 dport=80 src=31.222.68.38 dst=194.8.252.69 sport=80 dport=53602 [ASSURED] mark=0 use=1
udp      17 156 src=193.150.12.94 dst=194.8.253.11 sport=34734 dport=53 src=194.8.253.11 dst=193.150.12.94 sport=53 dport=34734 [ASSURED] mark=0 use=1
tcp      6 67 TIME_WAIT src=10.16.17.252 dst=194.8.253.240 sport=3294 dport=9080 src=194.8.253.240 dst=10.16.17.252 sport=9080 dport=3294 [ASSURED] mark=0 use=1
*/

// conntrack-l:
// 	data:
// 		tcp/udp:
// 			pattern:
// 			remove:
// 				regexp-tcp
// 				regexp-udp
// 		src:
// 			pattern:
// 			remove:
				
// 		dst:
// 			regexp: