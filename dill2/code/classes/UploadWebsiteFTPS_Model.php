<?php

require_once("ModelBase.php");
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"WebsiteProjectSettings_constants.php");


class UploadWebsiteFTPS_Model extends ModelBase
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
		if ($this->website_project_settings[0]["ftps_use_ftp"])
		{
			$this->authentication_method = DILL2_FTPS_AUTHENTICATION_METHOD_FTP;
		}
		else			
		{
			$this->authentication_method = DILL2_FTPS_AUTHENTICATION_METHOD_FTPS;
		}
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