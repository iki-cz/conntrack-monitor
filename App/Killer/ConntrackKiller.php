<?php
namespace App\Killer;
class ConntrackKiller{
	private $log = "";
	private $mode;
	private $connectionLimits;
	private $defaultConnections;
	private $subnetConnections;
	private $mailFrom;
	private $mailsTo;
	private $kill;
	
	const LEVEL_KILL = "kill";
	const LEVEL_WARNING = "warn";
	const LEVEL_NOTHING = "nothing";
	
	/**
	 * zkontroluje počet spojení pro ip adresu, podle toho, do které skupiny ip adresa patří 
	 * @param string $ip
	 * @param int $connections
	 */
	public function check($ip, $connections){
		$max = $this->getMaxConnections($ip);
// 		echo "killing mode " . $this->kill . "\n";
		
		if($max < $connections){
			$ll = $this->getLogLevel($ip);

			switch ($ll){
				case self::LEVEL_KILL:
					$killed = $this->kill($ip);
					if($killed){
						$this->logToFile('IP '. $ip .' automaticaly DROPped' . "\n");
					}
					$this->log .= "<br />\n" . $ip . " -> " . $connections . " | shell out, DROP: ". $killed;
					break;
				case self::LEVEL_WARNING:
					$this->log .= "<br />\n" . $ip . " -> " . $connections . " | BH IP warning";
					break;
				case self::LEVEL_NOTHING:
				default:
					//nothing
			}
		}
	}
	
	/**
	 * pokud existují nějaké záznamy v logu, pošle je mailem
	 */
	public function sendMailInfo(){
		if($this->mode != "production" && !empty($this->log)){
			print "debug: sending info email " . $this->log . "\n";
			return;
		}
		
		if (!empty($this->log)){
			foreach ($this->mailsTo as $receiver){
				$this->sendMail($this->mailFrom, $receiver, "CONNTRACK on IGW2", $this->log);
			}
		}
	}
	
	/**
	 * zabanuje IP adresu
	 * @param string $ip
	 * @return boolean|string buď true, nebo chybové hlášení
	 */
	public function kill($ip){
		if(!$this->kill){
			echo "no killing this time: " . $ip . "\n";
			return;
		}
		
		if($this->mode != "production"){
			print "debug: banning ip " . $ip . "\n";
			return true;
		}
		
		$out = shell_exec('/opt/banip -d '. $ip);
		if("ok" == $out){
			return true;
		}
		return $out; 
	}
	
	/**
	 * zapíše do souboru log
	 * @param string $message
	 */
	private function logToFile($message){
		if($this->mode != "production"){
			print "debug: logging to file\n";
			return;
		}
		
		$f = fopen("/opt/badguys-auto.txt", "a");
		fwrite($f, $message);
		fclose($f);
	}

	/**
	 * do jaké skupiny ip adresa patří
	 * @param string $ip
	 * @return string
	 */
	private function getLogLevel($ip){
		//ve vyhrazených subnetech se pouze varuje
		foreach ($this->subnetConnections as $subnet => $limit){
			if($this->cidrMatch($ip, $subnet)){
				return self::LEVEL_WARNING;
			}
		}
		
		return self::LEVEL_KILL;
	}

	/**
	 * maximální počet konexí pro ip adresu
	 * @param string $ip
	 * @return number
	 */
	private function getMaxConnections($ip){
// 		var_dump($this->subnetConnections);die;
		foreach ($this->subnetConnections as $subnet => $limit){
			if($this->cidrMatch($ip, $subnet)){
				return $limit;
			}
		}
				
		if(isset($this->connectionLimits[$ip])){
			return 0 + $this->connectionLimits[$ip];
		}
		
		return $this->defaultConnections;
	}
	
	/**
	 * 
	 * @param string $ip
	 * @param int $range
	 * @return boolean
	 */
	private function cidrMatch($ip, $range){
		list ($subnet, $bits) = explode('/', $range);
		$ip = ip2long($ip);
		$subnet = ip2long($subnet);
		$mask = -1 << (32 - $bits);
		$subnet &= $mask; //# nb: in case the supplied subnet wasn't correctly aligned
		return ($ip & $mask) == $subnet;
	}
	
	/**
	 * jednoduché zasílání mailů
	 * @param string $from
	 * @param string $to
	 * @param string $subject
	 * @param string $msg
	 */
	private function sendMail($from="auto-drop-ip-IGW2@best-hosting.cz", $to="admin@best-net.cz", $subject="IGW Conntrack !!", $msg=""){
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=utf-8" . "\r\n";
		$headers .= 'From: <'.$from.'>' . "\r\n";
		$message = "check conntrack on IGW2! ".$msg;
		@mail($to,$subject,$message,$headers);
	}
	
	public function setConfig($config){
		$this->connectionLimits 	= $config['connections'];
		$this->subnetConnections 	= $config['subnets'];
		$this->defaultConnections 	= $config['default_connections'];
		$this->mode 				= $config['mode'];
		
		$this->mailFrom = $config['mail_from'];
		$this->mailsTo = $config['mail_to'];
		$this->kill = $config['kill'];
	}
}
