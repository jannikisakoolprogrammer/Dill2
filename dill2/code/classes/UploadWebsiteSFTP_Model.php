<?php

require_once("ModelBase.php");


class UploadWebsiteSFTP_Model extends ModelBase
{
	protected $website_project_settings = NULL;
	protected $authentication_method = NULL;
	
	
	public function retrieve_website_project_settings()
	{
		$this->website_project_settings = $this->website_project->db_select(
			"website_project_settings");
	}
	
	
	public function retrieve_authentication_method()
	{
		$this->authentication_method = $this->website_project_settings[0]["sftp_authentication_method"];
	}
	
	
	public function get_authentication_method()
	{
		return $this->authentication_method;
	}
	
	
	public function get_website_project_settings()
	{
		return $this->website_project_settings;
	}
}
?>