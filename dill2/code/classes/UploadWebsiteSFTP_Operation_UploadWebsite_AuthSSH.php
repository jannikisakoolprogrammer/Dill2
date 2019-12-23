<?php

require_once("UploadWebsiteOperationBase.php");
set_include_path(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"phpseclib1.0.18");

require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"phpseclib1.0.18" . DIRECTORY_SEPARATOR . 
	"NET" . DIRECTORY_SEPARATOR .
	"SFTP.php");
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"phpseclib1.0.18" . DIRECTORY_SEPARATOR . 
	"CRYPT" . DIRECTORY_SEPARATOR .
	"RSA.php");

class UploadWebsiteSFTP_Operation_UploadWebsite_AuthSSH extends UploadWebsiteOperationBase
{
	protected $privatekey = NULL;
	protected $privatekey_passphrase = NULL;	
	protected $publickey = NULL;
	
	
	public function run()
	{
		$this->webserver_ip_address = $this->website_project_settings[0]["sftp_webserver_ip_address"];
		$this->username = $this->website_project_settings[0]["sftp_username"];
		$this->privatekey = $this->website_project_settings[0]["sftp_privatekey"];
		
		if ($this->website_project_settings[0]["sftp_privatekey_passphrase"])
		{
			$this->privatekey_passphrase = file_get_contents($this->website_project_settings[0]["sftp_privatekey_passphrase"]);
		}
		
		$this->publickey = $this->website_project_settings[0]["sftp_publickey"];
		$this->port = 22;
		$this->webserver_path = $this->website_project_settings[0]["sftp_webserver_path"];		
		
		echo "Total files: " . $this->total_files_dirs;

		// Let's try to establish the connection.
		$sftp_conn = new Net_SFTP($this->webserver_ip_address);
		$key = new Crypt_RSA();
		$key->loadKey(file_get_contents($this->privatekey));
		
		if ($sftp_conn->login(
			$this->username,
			$key))
		{
			echo "Connection established.";
			
			// And now it is time to upload the website to the webserver.
			// We change the current directory on the local computer and
			// on the webserver aswell.

			// Local computer:
			$root_dir = getcwd() . DIRECTORY_SEPARATOR . $this->website_project->abspath_websiteproject_website;	

			chdir($root_dir);
			
			echo "Upload files now..." . PHP_EOL;
			
			for ($x = 1; $x <= $this->total_files_dirs; $x++)
			{
				// Update progress bar.
				$this->notify_observers();
				usleep(250000);
			}

			chdir( "../../../bin" );
			
			echo "Done with uploading files." . PHP_EOL;
			
			// Logout
			$sftp_conn->disconnect();			
		}
		else
		{
			echo "Connection failed.";
		}
					
		unset($key);
		unset($sftp_conn);
		
	}
	
}

?>