<?php

require_once("UploadWebsiteOperationBase.php");

class UploadWebsiteSFTP_Operation_UploadWebsite_AuthSFTP extends UploadWebsiteOperationBase
{	
	protected $password = NULL;
	
	
	public function run()
	{
		$this->webserver_ip_address = $this->website_project_settings[0]["sftp_webserver_ip_address"];
		$this->username = $this->website_project_settings[0]["sftp_username"];
		$this->password = file_get_contents($this->website_project_settings[0]["sftp_password"]);
		$this->port = 22;
		$this->webserver_path = $this->website_project_settings[0]["sftp_webserver_path"];
		
		echo "Total files: " . $this->total_files_dirs;
		
		// Let's try to establish the connection.
		$ssh_conn = ssh2_connect(
			$this->webserver_ip_address,
			$this->port);
			
		if($ssh_conn != FALSE)
		{
			// A connection to the web-server has now been established.
			// Accecpt the fingerprint automatically.
			ssh2_fingerprint($ssh_conn);	

			// Login user username and password.
			if( ssh2_auth_password(
				$ssh_conn,
				$this->username,
				$this->password))
			{
				// And now it is time to upload the website to the webserver.
				// We change the current directory on the local computer and
				// on the webserver aswell.

				// Local computer:
				$root_dir = getcwd() . DIRECTORY_SEPARATOR . $this->website_project->abspath_websiteproject_website;	

				chdir($root_dir);
					
				// Now let's upload the website.
				$ssh2_sftp = ssh2_sftp($ssh_conn);
				
				echo "Upload files now..." . PHP_EOL;
				
				$this->upload_sync(
					$ssh_conn,
					$ssh2_sftp,
					$root_dir,
					$this->webserver_path
				);

				chdir( "../../../bin" );
				
				echo "Done with uploading files." . PHP_EOL;
			}
			
			// Logout
			ssh2_exec( $ssh_conn, "exit" );
			unset( $ssh_conn );
		}		
	}
}

?>