<?php
//Poi Decoder Class
//Licensed under WTFPL
//Author: Angelic47 http://www.angelic47.com

//Example:
//$decode = $new Decoder('1.txt');
//var_dump($decode->getDecode());

class Decoder
{
	private $poiedContents;
	private $poiStruct;
	private $bitBufferLen;
	private $bitBuffer;
	private $finalStr;
	private $already;
	
	public function __construct($fileName)
	{
		$this->poiedContents = file_get_contents($fileName);
		if($this->poiedContents === false)
			throw new Exception('File cannot open exception');
		$this->bitBufferLen = 0;
		$this->bitBuffer = 0;
		$this->finalStr = '';
		$this->already = false;
	}
	
	private function symbolToNumber($symbol)
	{
		$arr = array(')'=>'0','!'=>'1','@'=>'2','#'=>'3','$'=>'4','%'=>'5','^'=>'6','&'=>'7','*'=>'8','('=>'9');
		$length = strlen($symbol);
		$resultStr = '';
		for($i = 0; $i < $length; $i++)
		{
			$resultStr .= $arr[$symbol[$i]];
		}
		return intval($resultStr);
	}
	
	private function decodePoiStruct()
	{
		$this->poiStruct = explode('~', $this->poiedContents);
		foreach($this->poiStruct as $key => $value)
		{
			switch($value)
			{
				case 'poi':
					$this->poiStruct[$key] = 0;
					break;
				case 'nico':
					$this->poiStruct[$key] = 1;
					break;
				case '';
					unset($this->poiStruct[$key]);
					break;
				default:
					$this->poiStruct[$key] = $this->symbolToNumber($value);
					break;
			}
		}
	}
	
	private function bitWorker($bit, $times)
	{
		for($i = 0; $i < $times; $i ++)
		{
			$this->bitBuffer |= $bit << (7 - $this->bitBufferLen);
			$this->bitBufferLen ++;
			if($this->bitBufferLen > 7)
			{
				$this->bitBufferLen = 0;
				$this->finalStr .= chr($this->bitBuffer);
				$this->bitBuffer = 0;
			}
		}
	}
	
	public function getDecode()
	{
		if($this->already)
			return $this->finalStr;
		$this->decodePoiStruct();
		$i = 0;
		$bit = 0;
		$times = 0;
		foreach($this->poiStruct as $value)
		{
			switch($i)
			{
				case 0:
					$bit = $value;
					break;
				case 1:
					$times = $value;
					$this->bitWorker($bit, $times);
					$i = -1;
					break;
				default:
					throw new Exception('Arrived at default while decoding poi struct which shouldn\'t happen');
			}
			$i++;
		}
		if($this->bitBufferLen != 0)
			throw new Exception('Origin file was broken exception');
		$this->already = true;
		return $this->finalStr;
	}
	
}

