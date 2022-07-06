<?php 

namespace Vulcan\lib\utils;

class VulcanException extends \Exception
{
	public function __construct( $message )
	{
		parent::__construct( $message );
	}
	
	public function print()
	{
		$trace = $this->getTrace();

		$result = 'Exception: "';
		$result .= $this->getMessage();
		$result .= '" @ ';
		if($trace[0]['class'] != '') {
		  $result .= $trace[0]['class'];
		  $result .= '->';
		}
		$result .= $trace[0]['function'];
		$result .= '();<br />';

		return $result;
	}
}