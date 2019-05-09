<?php
//Poi Encoder Class
//Licensed under WTFPL
//Author: Angelic47 http://www.angelic47.com

//Example:
//$encode = $new Encoder('1.txt');
//var_dump($encode->getEncode());

class Encoder
{
	private $file_origin_contents;
	private $currectNumber;
	private $finalString;
	private $currectBit;
	private $already;
	
	public function __construct($fileName)
	{
		$this->file_origin_contents = file_get_contents($fileName);
		if($this->file_origin_contents === false)
			throw new Exception('File cannot open exception');
		$this->currectNumber = 1;
		$this->finalString = '';
		$this->currectBit = false;
		$this->already = false;
	}
	
	private function numberToSymbol($num)
	{
		if($num <= 0)
			throw new Exception('Illegal argument exception while number to symbol which shouldn\'t happen');
		$arr = array(')','!','@','#','$','%','^','&','*','(');
		$strNum = strval($num);
		$length = strlen($strNum);
		$resultStr = '';
		for($i = 0; $i < $length; $i++)
		{
			$resultStr .= $arr[intval($strNum[$i])];
		}
		return $resultStr;
	}
	
	private function bitWriteWorker($bit, $len)
	{
		switch($bit)
		{
			case 0:
				$this->finalString .= 'poi~';
				break;
			case 1:
				$this->finalString .= 'nico~';
				break;
			default:
				throw new Exception('Mismatch bit exception while covering bit which shouldn\'t happen');
		}
		$this->finalString .= $this->numberToSymbol($len);
		$this->finalString .= '~';
	}
	
	private function addBit($bit)
	{
		if($this->currectBit === false)
		{
			$this->currectBit = $bit;
			$this->currectNumber = 1;
			return;
		}
		if($this->currectBit != $bit)
		{
			$this->bitWriteWorker($this->currectBit, $this->currectNumber);
			$this->currectBit = $bit;
			$this->currectNumber = 1;
			return;
		}
		$this->currectNumber ++;
	}
	
	private function doneEncode()
	{
		$this->bitWriteWorker($this->currectBit, $this->currectNumber);
	}
	
	private function addByte($byte)
	{
		$byteMask = 128;
		$byte = ord($byte);
		for($i = 0; $i < 8; $i ++)
		{
			$byteMaskActive = $byteMask >> $i;
			$bit = ($byte & $byteMaskActive) > 0 ? 1 : 0;
			$this->addBit($bit);
		}
	}
	
	public function getEncode()
	{
		if($this->already)
			return $this->finalString;
		$contentLength = strlen($this->file_origin_contents);
		for($i = 0; $i < $contentLength; $i++)
			$this->addByte($this->file_origin_contents[$i]);
		$this->doneEncode();
		$this->already = true;
		return $this->finalString;
	}
	
}
