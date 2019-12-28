<?php

abstract class LogicBase
{
	protected $operation;
	
	
	abstract function run();
	
	
	public function get_operation()
	{
		return $this->operation;
	}
}

?>