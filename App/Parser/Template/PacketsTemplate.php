<?php 
namespace App\Parser\Template;
use App\Cache\ICache;
class PacketsTemplate implements IParserTemplate{
	private $stats = array();
	private $counter = 0;
	private $lineParts = array();
	
	public function parse($line) {
		$this->lineParts[] = $line;
		$this->counter++;
		
		if($this->counter > 5){
			$this->counter = 0;
			/*
			0 string(82) "class htb 1:12c parent 1:1 rate 1000bit ceil 512000Kbit burst 1600b cburst 1536b"
			1 string(76) " Sent 23489656705 bytes 112013866 pkt (dropped 0, overlimits 0 requeues 0)"
			2 string(42) " rate 0bit 0pps backlog 0b 0p requeues 0"
			3 string(41) " lended: 0 borrowed: 108129141 giants: 0
			4 string(33) " tokens: -937486644 ctokens: 273"
			5 string(1) ""
			*/

			//$classId = 
			
			var_dump($this->lineParts);
		}
	}

	public function getStats() {
		
		return $this->stats; 
	}

	public function sumarize() {
		// TODO: Auto-generated method stub

	}

	public function setConfig(array $config) {
		return $this;
	}

	public function setCache(ICache $cache) {
		// TODO: Auto-generated method stub

	}
}