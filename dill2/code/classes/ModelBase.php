<?php

abstract class ModelBase
{
	protected $website_project = NULL;
	
	
	public function __construct($_website_project)
	{
		$this->website_project = $_website_project;
	}
}

?>