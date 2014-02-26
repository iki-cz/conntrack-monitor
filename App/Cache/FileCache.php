<?php namespace App\Cache;
/**
 *
 */
class FileCache implements ICache{
	private $data = array();
	private $file;
	
	public function __construct($file){
		$this->file = $file;
		if(file_exists($this->file)){
			$content = file_get_contents($this->file);
			if(!empty($content)){
				$this->data = (array) json_decode($content);
			}
		}
	}
	
	public function getData(){
		return $this->data;
	}
	
	public function save($id, $value){
		$this->data[$id] = $value;
		
		file_put_contents($this->file, json_encode($this->data));
	}
	
	public function get($id){
		return $this->data[$id];
	}

	public function hit($id){
		return isset($this->data[$id]);
	}
}