<?php

require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"WebsiteProjectSettings_constants.php");
	
require_once("UploadWebsiteLogicBase.php");
require_once("UploadWebsiteFTPS_Operation_UploadWebsite_AuthFTPS.php");
require_once("UploadWebsiteFTPS_Operation_UploadWebsite_AuthFTP.php");


class UploadWebsiteFTPS_Logic extends UploadWebsiteLogicBase
{	
	public function run()
	{		
		if ($this->operation != NULL)
		{
			$this->operation->run();			
		}
	}
	
	
	public function init_operation_upload_website()
	{
		switch($this->authentication_method)
		{
			case DILL2_FTPS_AUTHENTICATION_METHOD_FTPS:
				$this->operation = new UploadWebsiteFTPS_Operation_UploadWebsite_AuthFTPS(
					$this->website_project_settings,
					$this->website_project);
				break;
				
			case DILL2_FTPS_AUTHENTICATION_METHOD_FTP:
				$this->operation = new UploadWebsiteFTPS_Operation_UploadWebsite_AuthFTP(
					$this->website_project_settings,
					$this->website_project);
				break;
				
			default:
				throw new Exception("No authentication method set.");
		}
	}
}
?>