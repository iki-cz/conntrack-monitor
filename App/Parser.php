<?php 
namespace App;
use App\Parser\Template\IParserTemplate;
use App\Cache\ICache;

/**
 *
 */
class Parser{
	private $filePath;
	private $stream;
	private $template;
	private $cache;

	public function __construct(IParserTemplate $template){
		$this->template = $template;
	}
	
	/**
	 * parse all lines
	 */
	public function parse(){
		// 		$handle = fopen($this->filePath, "r");
		// 		while($line = fgets($handle)){
		while($line = fgets($this->getStream())){
			$this->template->parse($line);
// 			$parses = $this->parseLine($line);
// 			foreach ($parses as $pars){
// 				$this->createRawStats($pars);
// 			}
		}
		
		$this->template->sumarize();
		// 		fclose($handle);

// 		$this->collectGarbage($this->gcMinimum);
// 		$this->sort();
	}
	
	public function getStats(){
		$this->template->setCache($this->getCache());
		return $this->template->getStats();
	}
	
	public function getStream(){
		return $this->stream;
	}

	public function setStream($stream){
		$this->stream = $stream;
		return $this;
	}
	
	public function setCache(ICache $cache){
		$this->cache = $cache;
		return $this;
	}
	
	public function getCache(){
		return $this->cache;
	}
}
/*
tcp      6 114 TIME_WAIT src=85.143.161.54 dst=194.8.253.108 sport=56293 dport=22 src=194.8.253.108 dst=85.143.161.54 sport=22 dport=56293 [ASSURED] mark=0 use=1
tcp      6 248 ESTABLISHED src=88.100.236.247 dst=194.8.253.85 sport=15599 dport=80 src=194.8.253.85 dst=88.100.236.247 sport=80 dport=15599 [ASSURED] mark=0 use=1
tcp      6 42 TIME_WAIT src=78.110.208.218 dst=194.8.253.85 sport=36676 dport=80 src=194.8.253.85 dst=78.110.208.218 sport=80 dport=36676 [ASSURED] mark=0 use=1
tcp      6 115 TIME_WAIT src=46.135.112.7 dst=194.8.253.85 sport=40286 dport=80 src=194.8.253.85 dst=46.135.112.7 sport=80 dport=40286 [ASSURED] mark=0 use=1
tcp      6 7 TIME_WAIT src=184.169.214.204 dst=194.8.253.34 sport=59303 dport=80 src=194.8.253.34 dst=184.169.214.204 sport=80 dport=59303 [ASSURED] mark=0 use=1
tcp      6 5 TIME_WAIT src=5.10.83.62 dst=194.8.253.228 sport=36033 dport=80 src=194.8.253.228 dst=5.10.83.62 sport=80 dport=36033 [ASSURED] mark=0 use=1
tcp      6 3 TIME_WAIT src=10.17.234.15 dst=77.75.76.72 sport=49284 dport=443 src=77.75.76.72 dst=194.8.252.69 sport=443 dport=49284 [ASSURED] mark=0 use=1
udp      17 177 src=10.16.6.2 dst=157.55.130.158 sport=9375 dport=40015 src=157.55.130.158 dst=194.8.252.66 sport=40015 dport=9375 [ASSURED] mark=0 use=1
tcp      6 150 ESTABLISHED src=37.48.36.203 dst=194.8.253.85 sport=12060 dport=80 src=194.8.253.85 dst=37.48.36.203 sport=80 dport=12060 [ASSURED] mark=0 use=1
tcp      6 94 TIME_WAIT src=194.8.252.194 dst=173.194.10.180 sport=36358 dport=80 src=173.194.10.180 dst=194.8.252.194 sport=80 dport=36358 [ASSURED] mark=0 use=1
tcp      6 112 TIME_WAIT src=10.17.215.230 dst=194.8.253.240 sport=42340 dport=9080 src=194.8.253.240 dst=10.17.215.230 sport=9080 dport=42340 [ASSURED] mark=0 use=1
udp      17 14 src=10.16.13.2 dst=194.8.253.11 sport=28740 dport=53 src=194.8.253.11 dst=10.16.13.2 sport=53 dport=28740 mark=0 use=1 
*/