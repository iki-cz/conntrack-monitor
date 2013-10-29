<?php
namespace App\Cache;
/**
 *
 */
interface ICache{
	public function get($id);
	public function hit($id);
	public function save($id, $value);
}