<?php 
namespace App\Parser\Template;
/**
 *
 */
class ConntrackTemplate extends BaseTemplate implements IParserTemplate{
	public function setupConfig(){
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
	}
}

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