<?php
namespace App;
class Arguments{
	private $arguments = array();
	private $map = array();
	private $inlineArguments = array();
	
	/**
	 * nastaví výchozí mapování parametrů
	 * všechny argumenty dá do mapy 
	 */
	public function __construct($argv){
		$this->map = array(
			"m" => "minimum",
			"min" => "minimum",
			"k" => "kill",
			//"minimum" => "minimum",
			"t" => "template",
			"c" => "connection",
			"s" => "subnet",
			//"template" => "template",
		);

		$this->inlineArguments["alias"] = array("ip", "value");
		$this->inlineArguments["subnet"] = array("ip", "value");
		$this->inlineArguments["connection"] = array("ip", "value");
		$this->inlineArguments["limit"] = array("ip", "value");
		
		//nektere prikazy maji vice polozek za sebou
		$argv = $this->preprocess($argv);
		
		array_shift($argv);
		
// 		var_dump($argv);die;
		for($i = 0; $i < count($argv); $i++){
			$id = $this->getMap(trim($argv[$i], "-"));
			$this->arguments[$id] = $argv[$i + 1];
			$i++;
		}
	}
	
	/**
	 * pokud je nastaven parametr, vrátí ho, jinak default
	 */
	public function get($name, $default){
		if(isset($this->arguments[$name])){
			return $this->arguments[$name];
		}
		return $default;
	}

	/**
	 * 
	 */
	public function getStream(){
		return fopen("php://stdin", "r");
	}
	
	/**
	 * vrací pro více identifikátorů jeden název parametru
	 */
	private function getMap($name){
		if(isset($this->map[$name])){
			return $this->map[$name];
		}
		return $name;
	}
	
	public function fill(array $what, array $to){
// 		var_dump($this->arguments);die;
		foreach ($what as $key => $value){
			$to[$key] = $value;
		}
		return $to;
	}
	
	public function getArguments(){
		return $this->arguments;
	}
	
	private function preprocess($arguments){
		$out = array();
		for ($i = 0; $i < count($arguments); $i++){
			
			if(isset($this->inlineArguments[$arguments[$i]])){
				//když se potká některý z inline argumentů, tak nastavit "action alias param value param value"
				$out[] = "action";
				$out[] = $arguments[$i];
				
				$params = $this->inlineArguments[$arguments[$i]];
				for($j = 0; $j < count($params); $j++){
					$out[] = $params[$j];
					$out[] = $arguments[++$i];
				}
			}else{
				//když není v mapě, tak pouze nastavit
				$out[] = $arguments[$i];
			}
		}
//  		var_dump($out);die;
		return $out;
	}
}
/*return "tcp      6 117 TIME_WAIT src=193.150.12.80 dst=37.157.197.240 sport=48604 dport=25 src=37.157.197.240 dst=193.150.12.80 sport=25 dport=48604 [ASSURED] mark=0 use=1
udp      17 11 src=10.16.20.101 dst=157.55.235.162 sport=5635 dport=40027 src=157.55.235.162 dst=194.8.252.66 sport=40027 dport=5635 mark=0 use=1
udp      17 5 src=193.150.12.80 dst=194.8.253.11 sport=38526 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=38526 mark=0 use=1
tcp      6 86 TIME_WAIT src=10.17.236.1 dst=31.222.68.38 sport=53602 dport=80 src=31.222.68.38 dst=194.8.252.69 sport=80 dport=53602 [ASSURED] mark=0 use=1
udp      17 156 src=193.150.12.94 dst=194.8.253.11 sport=34734 dport=53 src=194.8.253.11 dst=193.150.12.94 sport=53 dport=34734 [ASSURED] mark=0 use=1
tcp      6 67 TIME_WAIT src=10.16.17.252 dst=194.8.253.240 sport=3294 dport=9080 src=194.8.253.240 dst=10.16.17.252 sport=9080 dport=3294 [ASSURED] mark=0 use=1
tcp      6 26 TIME_WAIT src=37.188.231.35 dst=194.8.253.85 sport=6050 dport=80 src=194.8.253.85 dst=37.188.231.35 sport=80 dport=6050 [ASSURED] mark=0 use=1
udp      17 17 src=194.8.253.174 dst=8.8.8.8 sport=59446 dport=53 src=8.8.8.8 dst=194.8.253.174 sport=53 dport=59446 mark=0 use=1
tcp      6 101 TIME_WAIT src=217.195.202.9 dst=194.8.253.226 sport=64801 dport=80 src=194.8.253.226 dst=217.195.202.9 sport=80 dport=64801 [ASSURED] mark=0 use=1
udp      17 2 src=194.8.253.170 dst=8.8.8.8 sport=45170 dport=53 src=8.8.8.8 dst=194.8.253.170 sport=53 dport=45170 mark=0 use=1
tcp      6 71 TIME_WAIT src=112.123.168.148 dst=194.8.253.188 sport=4862 dport=80 src=194.8.253.188 dst=112.123.168.148 sport=80 dport=4862 [ASSURED] mark=0 use=1
tcp      6 20 TIME_WAIT src=194.8.252.35 dst=194.8.253.245 sport=33544 dport=3306 src=194.8.253.245 dst=194.8.252.35 sport=3306 dport=33544 [ASSURED] mark=0 use=1
tcp      6 97 TIME_WAIT src=10.106.1.5 dst=194.213.222.29 sport=2548 dport=80 src=194.213.222.29 dst=194.8.252.84 sport=80 dport=2548 [ASSURED] mark=0 use=1
tcp      6 46 TIME_WAIT src=66.249.75.198 dst=194.8.253.115 sport=50816 dport=80 src=194.8.253.115 dst=66.249.75.198 sport=80 dport=50816 [ASSURED] mark=0 use=1
tcp      6 73 TIME_WAIT src=10.17.208.246 dst=194.8.253.240 sport=4912 dport=9080 src=194.8.253.240 dst=10.17.208.246 sport=9080 dport=4912 [ASSURED] mark=0 use=1
udp      17 3 src=10.16.20.25 dst=157.55.235.166 sport=20514 dport=40005 src=157.55.235.166 dst=194.8.252.66 sport=40005 dport=20514 [ASSURED] mark=0 use=1
tcp      6 112 TIME_WAIT src=194.8.253.184 dst=77.75.76.42 sport=33335 dport=25 src=77.75.76.42 dst=194.8.253.184 sport=25 dport=33335 [ASSURED] mark=0 use=1
tcp      6 16 TIME_WAIT src=194.8.252.146 dst=74.125.24.94 sport=62883 dport=443 src=74.125.24.94 dst=194.8.252.146 sport=443 dport=62883 [ASSURED] mark=0 use=1
tcp      6 116 TIME_WAIT src=81.90.164.1 dst=194.8.253.50 sport=21017 dport=80 src=194.8.253.50 dst=81.90.164.1 sport=80 dport=21017 [ASSURED] mark=0 use=2
tcp      6 289 ESTABLISHED src=37.48.34.75 dst=194.8.253.85 sport=56421 dport=80 src=194.8.253.85 dst=37.48.34.75 sport=80 dport=56421 [ASSURED] mark=0 use=1
tcp      6 100 TIME_WAIT src=10.16.20.92 dst=109.71.162.206 sport=58223 dport=80 src=109.71.162.206 dst=194.8.252.66 sport=80 dport=58223 [ASSURED] mark=0 use=1
tcp      6 112 TIME_WAIT src=10.17.236.1 dst=31.222.68.38 sport=53620 dport=80 src=31.222.68.38 dst=194.8.252.69 sport=80 dport=53620 [ASSURED] mark=0 use=1
tcp      6 35 TIME_WAIT src=10.17.211.36 dst=84.33.37.104 sport=62729 dport=80 src=84.33.37.104 dst=194.8.252.68 sport=80 dport=62729 [ASSURED] mark=0 use=1
tcp      6 15 TIME_WAIT src=194.8.253.174 dst=77.75.76.42 sport=59244 dport=25 src=77.75.76.42 dst=194.8.253.174 sport=25 dport=59244 [ASSURED] mark=0 use=1
udp      17 24 src=194.8.253.170 dst=8.8.8.8 sport=52291 dport=53 src=8.8.8.8 dst=194.8.253.170 sport=53 dport=52291 [ASSURED] mark=0 use=1
udp      17 18 src=194.8.253.170 dst=8.8.8.8 sport=34953 dport=53 src=8.8.8.8 dst=194.8.253.170 sport=53 dport=34953 mark=0 use=1
tcp      6 229 ESTABLISHED src=37.188.235.88 dst=194.8.253.85 sport=24166 dport=80 src=194.8.253.85 dst=37.188.235.88 sport=80 dport=24166 [ASSURED] mark=0 use=1
tcp      6 86 TIME_WAIT src=194.228.11.8 dst=194.8.253.85 sport=6537 dport=80 src=194.8.253.85 dst=194.228.11.8 sport=80 dport=6537 [ASSURED] mark=0 use=1
udp      17 3 src=208.43.53.240 dst=193.150.13.8 sport=55732 dport=53 src=193.150.13.8 dst=208.43.53.240 sport=53 dport=55732 mark=0 use=1
tcp      6 67 TIME_WAIT src=194.8.252.2 dst=192.150.16.64 sport=58800 dport=80 src=192.150.16.64 dst=194.8.252.2 sport=80 dport=58800 [ASSURED] mark=0 use=1
udp      17 5 src=193.150.12.92 dst=194.8.253.11 sport=53251 dport=53 src=194.8.253.11 dst=193.150.12.92 sport=53 dport=53251 mark=0 use=1
tcp      6 119 TIME_WAIT src=62.77.85.144 dst=193.150.12.33 sport=4470 dport=80 src=193.150.12.33 dst=62.77.85.144 sport=80 dport=4470 [ASSURED] mark=0 use=1
udp      17 101 src=193.150.12.80 dst=194.8.253.11 sport=50497 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=50497 [ASSURED] mark=0 use=1
tcp      6 22 SYN_SENT src=10.17.221.96 dst=194.8.252.157 sport=34230 dport=9080 [UNREPLIED] src=194.8.252.157 dst=10.17.221.96 sport=9080 dport=34230 mark=0 use=1
tcp      6 5 TIME_WAIT src=46.135.76.24 dst=194.8.253.85 sport=47093 dport=80 src=194.8.253.85 dst=46.135.76.24 sport=80 dport=47093 [ASSURED] mark=0 use=1
tcp      6 227 ESTABLISHED src=37.188.237.39 dst=194.8.253.34 sport=39950 dport=993 src=194.8.253.34 dst=37.188.237.39 sport=993 dport=39950 [ASSURED] mark=0 use=1
tcp      6 69 ESTABLISHED src=195.28.64.144 dst=194.8.252.66 sport=20006 dport=64328 [UNREPLIED] src=194.8.252.66 dst=195.28.64.144 sport=64328 dport=20006 mark=0 use=2
tcp      6 298 ESTABLISHED src=10.17.211.58 dst=23.62.237.86 sport=60195 dport=80 src=23.62.237.86 dst=194.8.252.68 sport=80 dport=60195 [ASSURED] mark=0 use=1
udp      17 155 src=194.8.253.174 dst=8.8.8.8 sport=52286 dport=53 src=8.8.8.8 dst=194.8.253.174 sport=53 dport=52286 [ASSURED] mark=0 use=1
tcp      6 38 CLOSE_WAIT src=93.153.17.137 dst=194.8.253.85 sport=27670 dport=80 src=194.8.253.85 dst=93.153.17.137 sport=80 dport=27670 [ASSURED] mark=0 use=1
udp      17 25 src=64.106.251.82 dst=194.8.253.22 sport=20934 dport=53 src=194.8.253.22 dst=64.106.251.82 sport=53 dport=20934 mark=0 use=1
tcp      6 190 ESTABLISHED src=212.47.3.163 dst=194.8.253.85 sport=39452 dport=80 src=194.8.253.85 dst=212.47.3.163 sport=80 dport=39452 [ASSURED] mark=0 use=1
tcp      6 74 TIME_WAIT src=66.249.78.153 dst=194.8.253.229 sport=54709 dport=80 src=194.8.253.229 dst=66.249.78.153 sport=80 dport=54709 [ASSURED] mark=0 use=1
udp      17 87 src=194.8.252.35 dst=194.8.253.11 sport=47851 dport=53 src=194.8.253.11 dst=194.8.252.35 sport=53 dport=47851 [ASSURED] mark=0 use=1
tcp      6 17 TIME_WAIT src=10.16.20.53 dst=77.75.73.9 sport=3627 dport=80 src=77.75.73.9 dst=194.8.252.66 sport=80 dport=3627 [ASSURED] mark=0 use=1
tcp      6 154 ESTABLISHED src=80.188.40.98 dst=194.8.253.85 sport=46155 dport=80 src=194.8.253.85 dst=80.188.40.98 sport=80 dport=46155 [ASSURED] mark=0 use=1
tcp      6 77 TIME_WAIT src=194.8.253.174 dst=173.194.70.26 sport=54733 dport=25 src=173.194.70.26 dst=194.8.253.174 sport=25 dport=54733 [ASSURED] mark=0 use=1
tcp      6 80 SYN_SENT src=10.17.211.55 dst=86.61.43.86 sport=59724 dport=44152 [UNREPLIED] src=86.61.43.86 dst=194.8.252.68 sport=44152 dport=59724 mark=0 use=1
tcp      6 60 TIME_WAIT src=37.188.229.109 dst=194.8.253.85 sport=41653 dport=80 src=194.8.253.85 dst=37.188.229.109 sport=80 dport=41653 [ASSURED] mark=0 use=1
tcp      6 2 TIME_WAIT src=10.100.2.77 dst=81.0.212.200 sport=3642 dport=80 src=81.0.212.200 dst=194.8.252.78 sport=80 dport=3642 [ASSURED] mark=0 use=1
tcp      6 85 TIME_WAIT src=46.33.102.131 dst=194.8.253.229 sport=1068 dport=80 src=194.8.253.229 dst=46.33.102.131 sport=80 dport=1068 [ASSURED] mark=0 use=1
tcp      6 44 CLOSE_WAIT src=37.188.234.186 dst=194.8.253.85 sport=20269 dport=80 src=194.8.253.85 dst=37.188.234.186 sport=80 dport=20269 [ASSURED] mark=0 use=1
udp      17 12 src=10.17.211.8 dst=194.8.253.11 sport=61716 dport=53 src=194.8.253.11 dst=10.17.211.8 sport=53 dport=61716 mark=0 use=1
tcp      6 25 TIME_WAIT src=91.232.85.254 dst=194.8.253.254 sport=2416 dport=21 src=194.8.253.254 dst=91.232.85.254 sport=21 dport=2416 [ASSURED] mark=0 use=1
tcp      6 25 CLOSE_WAIT src=81.0.223.73 dst=194.8.253.85 sport=54915 dport=80 src=194.8.253.85 dst=81.0.223.73 sport=80 dport=54915 [ASSURED] mark=0 use=2
tcp      6 286 ESTABLISHED src=10.17.204.7 dst=54.225.150.135 sport=53552 dport=80 src=54.225.150.135 dst=194.8.252.67 sport=80 dport=53552 [ASSURED] mark=0 use=1
udp      17 13 src=75.126.43.207 dst=194.8.253.248 sport=22228 dport=53 src=194.8.253.248 dst=75.126.43.207 sport=53 dport=22228 mark=0 use=1
tcp      6 60 TIME_WAIT src=37.48.33.148 dst=194.8.253.85 sport=44960 dport=80 src=194.8.253.85 dst=37.48.33.148 sport=80 dport=44960 [ASSURED] mark=0 use=1
tcp      6 16 TIME_WAIT src=194.8.252.194 dst=90.183.101.168 sport=53767 dport=80 src=90.183.101.168 dst=194.8.252.194 sport=80 dport=53767 [ASSURED] mark=0 use=1
tcp      6 14 TIME_WAIT src=208.177.76.2 dst=194.8.253.188 sport=61320 dport=80 src=194.8.253.188 dst=208.177.76.2 sport=80 dport=61320 [ASSURED] mark=0 use=1
udp      17 23 src=194.8.252.194 dst=192.0.2.15 sport=27444 dport=53 [UNREPLIED] src=192.0.2.15 dst=194.8.252.194 sport=53 dport=27444 mark=0 use=1
tcp      6 49 TIME_WAIT src=109.231.191.222 dst=194.8.253.228 sport=31454 dport=80 src=194.8.253.228 dst=109.231.191.222 sport=80 dport=31454 [ASSURED] mark=0 use=1
tcp      6 44 TIME_WAIT src=193.193.172.200 dst=194.8.253.113 sport=51066 dport=80 src=194.8.253.113 dst=193.193.172.200 sport=80 dport=51066 [ASSURED] mark=0 use=1
udp      17 22 src=194.8.253.248 dst=194.8.253.11 sport=59205 dport=53 src=194.8.253.11 dst=194.8.253.248 sport=53 dport=59205 mark=0 use=1
tcp      6 292 ESTABLISHED src=37.188.226.49 dst=194.8.253.85 sport=60618 dport=80 src=194.8.253.85 dst=37.188.226.49 sport=80 dport=60618 [ASSURED] mark=0 use=1
udp      17 24 src=10.17.211.55 dst=72.65.227.50 sport=32692 dport=37896 [UNREPLIED] src=72.65.227.50 dst=194.8.252.68 sport=37896 dport=27215 mark=0 use=1
udp      17 11 src=194.8.253.248 dst=194.8.253.11 sport=59073 dport=53 src=194.8.253.11 dst=194.8.253.248 sport=53 dport=59073 mark=0 use=1
tcp      6 286 ESTABLISHED src=194.228.32.97 dst=194.8.252.243 sport=45227 dport=80 src=194.8.252.243 dst=194.228.32.97 sport=80 dport=45227 [ASSURED] mark=0 use=1
udp      17 27 src=194.8.253.174 dst=8.8.8.8 sport=53093 dport=53 src=8.8.8.8 dst=194.8.253.174 sport=53 dport=53093 mark=0 use=1
tcp      6 82 TIME_WAIT src=194.8.252.194 dst=88.86.110.124 sport=50121 dport=80 src=88.86.110.124 dst=194.8.252.194 sport=80 dport=50121 [ASSURED] mark=0 use=1
tcp      6 52 TIME_WAIT src=10.106.1.1 dst=194.8.253.240 sport=43363 dport=9080 src=194.8.253.240 dst=10.106.1.1 sport=9080 dport=43363 [ASSURED] mark=0 use=1
tcp      6 76 TIME_WAIT src=194.8.252.194 dst=90.183.101.168 sport=51938 dport=80 src=90.183.101.168 dst=194.8.252.194 sport=80 dport=51938 [ASSURED] mark=0 use=1
tcp      6 72 TIME_WAIT src=194.8.252.180 dst=217.31.49.116 sport=50928 dport=110 src=217.31.49.116 dst=194.8.252.180 sport=110 dport=50928 [ASSURED] mark=0 use=2
udp      17 4 src=193.150.12.80 dst=194.8.253.11 sport=37114 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=37114 [ASSURED] mark=0 use=1
udp      17 15 src=193.150.12.16 dst=194.8.253.11 sport=37925 dport=53 src=194.8.253.11 dst=193.150.12.16 sport=53 dport=37925 mark=0 use=1
tcp      6 64 TIME_WAIT src=88.146.158.137 dst=194.8.253.227 sport=50158 dport=80 src=194.8.253.227 dst=88.146.158.137 sport=80 dport=50158 [ASSURED] mark=0 use=1
udp      17 130 src=193.150.12.80 dst=194.8.253.11 sport=34779 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=34779 [ASSURED] mark=0 use=1
udp      17 93 src=194.8.253.180 dst=194.8.253.11 sport=47674 dport=53 src=194.8.253.11 dst=194.8.253.180 sport=53 dport=47674 [ASSURED] mark=0 use=1
tcp      6 15 TIME_WAIT src=168.65.65.230 dst=194.8.252.243 sport=17848 dport=80 src=194.8.252.243 dst=168.65.65.230 sport=80 dport=17848 [ASSURED] mark=0 use=1
udp      17 27 src=10.17.211.30 dst=194.8.253.11 sport=4816 dport=53 src=194.8.253.11 dst=10.17.211.30 sport=53 dport=4816 mark=0 use=1
tcp      6 68 TIME_WAIT src=78.80.178.62 dst=194.8.253.85 sport=40129 dport=80 src=194.8.253.85 dst=78.80.178.62 sport=80 dport=40129 [ASSURED] mark=0 use=1
udp      17 109 src=194.8.253.170 dst=8.8.8.8 sport=58841 dport=53 src=8.8.8.8 dst=194.8.253.170 sport=53 dport=58841 [ASSURED] mark=0 use=1
tcp      6 19 TIME_WAIT src=85.132.159.234 dst=194.8.253.50 sport=26305 dport=80 src=194.8.253.50 dst=85.132.159.234 sport=80 dport=26305 [ASSURED] mark=0 use=1
tcp      6 37 CLOSE_WAIT src=194.228.20.95 dst=194.8.253.85 sport=19334 dport=80 src=194.8.253.85 dst=194.228.20.95 sport=80 dport=19334 [ASSURED] mark=0 use=1
tcp      6 269 ESTABLISHED src=10.17.211.113 dst=94.245.119.145 sport=56415 dport=443 src=94.245.119.145 dst=194.8.252.68 sport=443 dport=56415 [ASSURED] mark=0 use=1
tcp      6 298 ESTABLISHED src=10.106.1.246 dst=194.8.253.240 sport=55588 dport=9080 src=194.8.253.240 dst=10.106.1.246 sport=9080 dport=55588 [ASSURED] mark=0 use=1
tcp      6 66 TIME_WAIT src=194.8.253.180 dst=23.63.30.49 sport=49379 dport=80 src=23.63.30.49 dst=194.8.253.180 sport=80 dport=49379 [ASSURED] mark=0 use=1
tcp      6 24 TIME_WAIT src=66.220.152.112 dst=194.8.253.118 sport=46452 dport=80 src=194.8.253.118 dst=66.220.152.112 sport=80 dport=46452 [ASSURED] mark=0 use=1
tcp      6 11 TIME_WAIT src=88.206.65.227 dst=194.8.253.18 sport=3023 dport=80 src=194.8.253.18 dst=88.206.65.227 sport=80 dport=3023 [ASSURED] mark=0 use=1
tcp      6 227 ESTABLISHED src=37.48.36.55 dst=194.8.253.85 sport=62941 dport=80 src=194.8.253.85 dst=37.48.36.55 sport=80 dport=62941 [ASSURED] mark=0 use=1
udp      17 164 src=10.17.215.44 dst=77.93.197.49 sport=5064 dport=5060 src=77.93.197.49 dst=194.8.252.68 sport=5060 dport=5064 [ASSURED] mark=0 use=1
tcp      6 50 TIME_WAIT src=173.213.97.207 dst=194.8.253.18 sport=39966 dport=80 src=194.8.253.18 dst=173.213.97.207 sport=80 dport=39966 [ASSURED] mark=0 use=2
tcp      6 36 TIME_WAIT src=194.8.253.185 dst=77.75.76.42 sport=46580 dport=25 src=77.75.76.42 dst=194.8.253.185 sport=25 dport=46580 [ASSURED] mark=0 use=1
tcp      6 41 SYN_SENT src=10.17.211.55 dst=96.233.19.132 sport=59556 dport=14781 [UNREPLIED] src=96.233.19.132 dst=194.8.252.68 sport=14781 dport=59556 mark=0 use=1
udp      17 162 src=194.8.253.174 dst=8.8.8.8 sport=58055 dport=53 src=8.8.8.8 dst=194.8.253.174 sport=53 dport=58055 [ASSURED] mark=0 use=1
tcp      6 17 TIME_WAIT src=194.8.253.180 dst=23.63.28.88 sport=55128 dport=80 src=23.63.28.88 dst=194.8.253.180 sport=80 dport=55128 [ASSURED] mark=0 use=1
tcp      6 298 ESTABLISHED src=178.23.219.5 dst=194.8.253.162 sport=5408 dport=80 src=194.8.253.162 dst=178.23.219.5 sport=80 dport=5408 [ASSURED] mark=0 use=1
tcp      6 257 ESTABLISHED src=37.48.32.57 dst=194.8.253.85 sport=42922 dport=80 src=194.8.253.85 dst=37.48.32.57 sport=80 dport=42922 [ASSURED] mark=0 use=1
tcp      6 91 TIME_WAIT src=85.71.9.250 dst=194.8.253.85 sport=37240 dport=80 src=194.8.253.85 dst=85.71.9.250 sport=80 dport=37240 [ASSURED] mark=0 use=1
tcp      6 95 TIME_WAIT src=10.17.234.15 dst=173.194.112.240 sport=51851 dport=80 src=173.194.112.240 dst=194.8.252.69 sport=80 dport=51851 [ASSURED] mark=0 use=1
tcp      6 288 ESTABLISHED src=10.113.1.40 dst=23.63.81.224 sport=65456 dport=443 src=23.63.81.224 dst=194.8.252.88 sport=443 dport=65456 [ASSURED] mark=0 use=1
udp      17 18 src=194.8.253.11 dst=208.78.71.34 sport=24983 dport=53 src=208.78.71.34 dst=194.8.253.11 sport=53 dport=24983 mark=0 use=1
tcp      6 101 TIME_WAIT src=193.150.12.94 dst=77.75.76.42 sport=49386 dport=25 src=77.75.76.42 dst=193.150.12.94 sport=25 dport=49386 [ASSURED] mark=0 use=1
tcp      6 46 TIME_WAIT src=10.17.221.96 dst=194.8.253.240 sport=39638 dport=9080 src=194.8.253.240 dst=10.17.221.96 sport=9080 dport=39638 [ASSURED] mark=0 use=1
tcp      6 287 ESTABLISHED src=84.244.66.225 dst=194.8.252.35 sport=1729 dport=80 src=194.8.252.35 dst=84.244.66.225 sport=80 dport=1729 [ASSURED] mark=0 use=1
udp      17 150 src=193.150.12.80 dst=194.8.253.11 sport=41786 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=41786 [ASSURED] mark=0 use=1
tcp      6 23 TIME_WAIT src=194.8.253.180 dst=193.186.32.251 sport=51239 dport=80 src=193.186.32.251 dst=194.8.253.180 sport=80 dport=51239 [ASSURED] mark=0 use=1
tcp      6 22 LAST_ACK src=37.188.239.121 dst=194.8.253.85 sport=2582 dport=80 src=194.8.253.85 dst=37.188.239.121 sport=80 dport=2582 [ASSURED] mark=0 use=1
udp      17 156 src=193.150.12.57 dst=194.8.253.11 sport=36423 dport=53 src=194.8.253.11 dst=193.150.12.57 sport=53 dport=36423 [ASSURED] mark=0 use=1
udp      17 8 src=193.150.12.80 dst=194.8.253.11 sport=45269 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=45269 mark=0 use=2
tcp      6 102 TIME_WAIT src=112.123.168.54 dst=194.8.253.188 sport=1798 dport=80 src=194.8.253.188 dst=112.123.168.54 sport=80 dport=1798 [ASSURED] mark=0 use=1
udp      17 4 src=194.8.252.245 dst=194.8.252.1 sport=45326 dport=53 src=194.8.252.1 dst=194.8.252.245 sport=53 dport=45326 mark=0 use=1
tcp      6 43 SYN_SENT src=10.17.211.55 dst=76.106.164.13 sport=59569 dport=15781 [UNREPLIED] src=76.106.164.13 dst=194.8.252.68 sport=15781 dport=59569 mark=0 use=1
tcp      6 116 TIME_WAIT src=37.48.42.134 dst=194.8.253.85 sport=59676 dport=80 src=194.8.253.85 dst=37.48.42.134 sport=80 dport=59676 [ASSURED] mark=0 use=1
udp      17 18 src=193.150.12.80 dst=194.8.253.11 sport=47772 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=47772 mark=0 use=1
tcp      6 65 TIME_WAIT src=194.8.253.180 dst=80.149.246.1 sport=49268 dport=80 src=80.149.246.1 dst=194.8.253.180 sport=80 dport=49268 [ASSURED] mark=0 use=1
tcp      6 297 ESTABLISHED src=10.17.211.97 dst=23.62.237.16 sport=53732 dport=443 src=23.62.237.16 dst=194.8.252.68 sport=443 dport=53732 [ASSURED] mark=0 use=1
tcp      6 100 TIME_WAIT src=10.16.20.92 dst=109.71.162.222 sport=58196 dport=80 src=109.71.162.222 dst=194.8.252.66 sport=80 dport=58196 [ASSURED] mark=0 use=1
tcp      6 64 TIME_WAIT src=178.33.217.26 dst=194.8.253.18 sport=53982 dport=80 src=194.8.253.18 dst=178.33.217.26 sport=80 dport=53982 [ASSURED] mark=0 use=1
tcp      6 60 TIME_WAIT src=10.17.234.51 dst=217.20.147.94 sport=62817 dport=80 src=217.20.147.94 dst=194.8.252.69 sport=80 dport=62817 [ASSURED] mark=0 use=1
udp      17 4 src=193.150.12.80 dst=194.8.253.11 sport=37488 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=37488 mark=0 use=1
tcp      6 26 TIME_WAIT src=194.8.253.117 dst=91.240.109.28 sport=55930 dport=80 src=91.240.109.28 dst=194.8.253.117 sport=80 dport=55930 [ASSURED] mark=0 use=1
udp      17 105 src=193.150.12.80 dst=194.8.253.11 sport=46440 dport=53 src=194.8.253.11 dst=193.150.12.80 sport=53 dport=46440 [ASSURED] mark=0 use=1
tcp      6 164 ESTABLISHED src=212.71.166.135 dst=194.8.253.85 sport=57184 dport=80 src=194.8.253.85 dst=212.71.166.135 sport=80 dport=57184 [ASSURED] mark=0 use=1
tcp      6 92 TIME_WAIT src=10.17.216.3 dst=173.194.70.190 sport=60917 dport=80 src=173.194.70.190 dst=194.8.252.68 sport=80 dport=60917 [ASSURED] mark=0 use=1
tcp      6 188 ESTABLISHED src=37.48.40.181 dst=194.8.253.85 sport=29939 dport=80 src=194.8.253.85 dst=37.48.40.181 sport=80 dport=29939 [ASSURED] mark=0 use=1
tcp      6 103 TIME_WAIT src=112.123.168.56 dst=194.8.253.188 sport=2898 dport=80 src=194.8.253.188 dst=112.123.168.56 sport=80 dport=2898 [ASSURED] mark=0 use=1
tcp      6 96 TIME_WAIT src=194.8.253.174 dst=77.75.76.42 sport=59820 dport=25 src=77.75.76.42 dst=194.8.253.174 sport=25 dport=59820 [ASSURED] mark=0 use=2
tcp      6 62 TIME_WAIT src=10.17.234.15 dst=72.47.228.101 sport=51893 dport=80 src=72.47.228.101 dst=194.8.252.69 sport=80 dport=51893 [ASSURED] mark=0 use=1
tcp      6 44 TIME_WAIT src=194.8.253.229 dst=217.31.59.45 sport=36907 dport=3306 src=217.31.59.45 dst=194.8.253.229 sport=3306 dport=36907 [ASSURED] mark=0 use=1
tcp      6 282 ESTABLISHED src=10.16.20.93 dst=23.62.237.72 sport=57543 dport=443 src=23.62.237.72 dst=194.8.252.66 sport=443 dport=57543 [ASSURED] mark=0 use=1
tcp      6 72 TIME_WAIT src=90.180.185.55 dst=194.8.253.85 sport=46592 dport=80 src=194.8.253.85 dst=90.180.185.55 sport=80 dport=46592 [ASSURED] mark=0 use=1
tcp      6 73 TIME_WAIT src=24.35.36.72 dst=194.8.253.229 sport=63820 dport=80 src=194.8.253.229 dst=24.35.36.72 sport=80 dport=63820 [ASSURED] mark=0 use=1
tcp      6 62 TIME_WAIT src=10.17.211.55 dst=95.95.122.26 sport=59689 dport=53942 src=95.95.122.26 dst=194.8.252.68 sport=53942 dport=59689 [ASSURED] mark=0 use=1
tcp      6 30 CLOSE_WAIT src=176.62.232.3 dst=194.8.253.85 sport=35584 dport=80 src=194.8.253.85 dst=176.62.232.3 sport=80 dport=35584 [ASSURED] mark=0 use=1
tcp      6 237 ESTABLISHED src=37.48.42.197 dst=194.8.253.85 sport=31102 dport=80 [UNREPLIED] src=194.8.253.85 dst=37.48.42.197 sport=80 dport=31102 mark=0 use=1
tcp      6 166 ESTABLISHED src=10.17.208.2 dst=173.194.112.238 sport=51202 dport=443 src=173.194.112.238 dst=194.8.252.68 sport=443 dport=51202 [ASSURED] mark=0 use=1
udp      17 12 src=193.150.12.93 dst=194.8.253.11 sport=37979 dport=53 src=194.8.253.11 dst=193.150.12.93 sport=53 dport=37979 mark=0 use=1";
		*/