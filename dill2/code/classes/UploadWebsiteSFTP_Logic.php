<?php

require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"WebsiteProjectSettings_constants.php");
	
require_once("UploadWebsiteLogicBase.php");
require_once("UploadWebsiteSFTP_Operation_UploadWebsite_AuthSFTP.php");
require_once("UploadWebsiteSFTP_Operation_UploadWebsite_AuthSSH.php");


class UploadWebsiteSFTP_Logic extends UploadWebsiteLogicBase
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
			case DILL2_SFTP_AUTHENTICATION_METHOD_SSH:
				$this->operation = new UploadWebsiteSFTP_Operation_UploadWebsite_AuthSSH(
					$this->website_project_settings);
				break;
				
			case DILL2_SFTP_AUTHENTICATION_METHOD_SFTP:
				$this->operation = new UploadWebsiteSFTP_Operation_UploadWebsite_AuthSFTP(
					$this->website_project_settings);
				break;
				
			default:
				throw new Exception("No authentication method set.");
		}
	}
}

?>