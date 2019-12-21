<?php

require_once("LogicBase.php");


class UploadWebsiteLogicBase extends LogicBase
{
	protected $website_project_settings = NULL;
	protected $authentication_method = NULL;
	
	public function run()
	{
	}
	
	
	public function set_authentication_method($_authentication_method)
	{
		$this->authentication_method = $_authentication_method;
	}
	
	
	public function set_website_project_settings($_website_project_settings)
	{
		$this->website_project_settings = $_website_project_settings;
	}	
}

?>