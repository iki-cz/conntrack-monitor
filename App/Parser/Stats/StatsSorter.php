<?php 
namespace App\Parser\Stats;
class StatsSorter{
	private $list;
	private static $index;
	
	public function __construct(array $list){
		$this->list = $list;
	}
	
	public function sort($i = 1){
// 		self::$index = $i;
		$index = $i;
		
		$compare = function($a, $b) use ($index){
			$i = $index; //self::$index;
			if($a->getValueByIndex($i) == $b->getValueByIndex($i)) return 0;
			return $a->getValueByIndex($i) > $b->getValueByIndex($i) ? -1 : 1;
		};
		
		usort($this->list, $compare);
		return $this->list;
	}
}