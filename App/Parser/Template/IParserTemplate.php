<?php 
namespace App\Parser\Template;
interface IParserTemplate{
	public function parse($line);
	public function getStats();
	public function sumarize();
}