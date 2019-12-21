<?php

require_once("OperationBase.php");


abstract class UploadWebsiteOperationBase extends OperationBase
{
	protected $website_project_settings = NULL;
	protected $webserver_ip_address = NULL;
	protected $webserver_path = NULL;
	protected $username = NULL;
	protected $port = NULL;
	
	
	public function __construct($_website_project_settings)
	{
		$this->website_project_settings = $_website_project_settings;
		
		$this->observers = [];
	}
		
	
	public function notify_observers()
	{
		foreach($this->observers as $obs)
		{
			$obs->update_bar(1);
		}
	}	
}

?>