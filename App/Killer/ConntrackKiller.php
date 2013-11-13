<?php
namespace App\Killer;
class ConntrackKiller{
	private $log = "";
	private $excludedSubnets = array('194.8.252.0/23','193.150.12.0/22','10.0.0.0/8');
	
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

		$connections *= 100;
		
		if($max < $connections){
			$ll = $this->getLogLevel($ip);

			switch ($ll){
				case self::LEVEL_KILL:
					$killed = $this->kill($ip); 
					if($killed){
						$this->logToFile('IP '. $ip .' is AUTO DROPped' . "\n");
					}
					$this->log .= "<br />\n" . $ip . " -> " . $connections . " | shell out, DROP: ". $killed;
// 					echo "killing " . $ip . ", connections " . $connections . "\n";
					
					break;
				case self::LEVEL_WARNING:
					$this->log .= "<br />\n" . $ip . " -> " . $connections . " | BH IP warning";
// 					echo "warning " . $ip . ", connections " . $connections . "\n";
					
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
		if (!empty($this->log)){
			$this->sendMail("auto-drop-ip-IGW2@best-hosting.cz","podpora@best-hosting.cz","CONNTRACK on IGW2", $this->log);
			$this->sendMail("tmobile@best-hosting.cz","774458851@sms.t-mobile.cz","CONNTRACK on IGW2", $this->log);
		}
	}
	
	/**
	 * zabanuje IP adresu
	 * @param string $ip
	 * @return boolean|string buď true, nebo chybové hlášení
	 */
	public function kill($ip){
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
		$f = fopen("/opt/badguys-auto.txt", "a");
		fwrite($f, $message);
		fclose($f);
	}

	/**
	 * array subnetů, které jsou privilegované, může se změnit na config
	 * @return multitype:string
	 */
	private function getExcludedSubnets(){ 
		// pouze warningy na nase ostatni ip ze subnetu - vetsinou je utok veden z cizich IP :-)
		return $this->excludedSubnets;
	}
	
	/**
	 * do jaké skupiny ip adresa patří
	 * @param string $ip
	 * @return string
	 */
	private function getLogLevel($ip){
		//ve vyhrazených subnetech se pouze varuje
		foreach ($this->getExcludedSubnets() as $subnet){
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
		foreach ($this->getExcludedSubnets() as $subnet){
			if($this->cidrMatch($ip, $subnet)){
				$ip = "insubnet";
			}
		}
				
		switch ($ip){
			case '194.8.253.85':
			case '194.8.253.11':
			case '194.8.253.115': 
			case '8.8.8.8':
			case '82.113.33.42':
				return 500000;
			case "insubnet":
				return 15000;
			default:
				return 9000;
		}
	}
	
	/**
	 * 
	 * @param string $ip
	 * @param int $range
	 * @return boolean
	 */
	private function cidrMatch($ip, $range){
		list ($subnet, $bits) = split('/', $range);
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
}




// pouze pro kontrolu auto odpojenych IP, po overeni funkcnosti do /opt/badguys
// 		$mailDrop=false;
// 		$mailWarn=false;

// 		foreach($parser->getStats() as $stat){
// 			$isex = false;
// 			foreach ($ExcludedIPs as &$exip){
// 				if ( $exip == $stat->getIp() ){
// 					$isex=true;
// 				}
// 			}
	
// 			$isexsub = false;
// 			foreach ($ExcludedSubnets as &$exsub){
// 				if(cidr_match($stat->getIp(),$exsub)){
// 					$isexsub=true;
// 				}
// 			}
	
// 			if (!$isex && !$isexsub && $stat->getConnections() >= $ConnHaltIP){
// 				$outSh=shell_exec('/opt/banip -d '.$stat->getIp());
// 				if ($outSh=='ok') $this->log('IP '.$stat->getIp().' is AUTO DROP'."\n");
// 				$mailDrop=true;
// 				$this->log .= "<br />\n".$stat->getIp() ." -> ". $stat->getConnections() . " | shell out, DROP: ". $outSh;
// 			}
	
// 			else if ( !$isex && $isexsub && $stat->getConnections() >= $ConnHaltIPBH ) {
// 				$mailWarn=true;
// 				$this->log .="<br />\n".$stat->getIp() ." -> ". $stat->getConnections() . " | BH IP warning";
// 			}
// 		}