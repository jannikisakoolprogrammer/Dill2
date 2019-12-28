<?php

abstract class OperationBase
{
	protected $observers;

	abstract function run();
	
	public function register_observer($_observer)
	{
		array_push(
			$this->observers,
			$_observer);
	}
	
	
	public function unregister_observer($_observer)
	{
		// TODO...
	}
	
	
	abstract function notify_observers();
}

?>