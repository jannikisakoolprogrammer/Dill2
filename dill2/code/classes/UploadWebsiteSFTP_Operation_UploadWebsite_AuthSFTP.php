<?php

require_once("UploadWebsiteOperationBase.php");

class UploadWebsiteSFTP_Operation_UploadWebsite_AuthSFTP extends UploadWebsiteOperationBase
{	
	protected $password = NULL;
	
	
	public function run()
	{
		$this->webserver_ip_address = $this->website_project_settings[0]["sftp_webserver_ip_address"];
		$this->username = $this->website_project_settings[0]["sftp_username"];
		$this->password = $this->website_project_settings[0]["sftp_password"];
		$this->port = 22;
		$this->webserver_path = $this->website_project_settings[0]["sftp_webserver_path"];
		
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
				// Now let's upload the website.
				$ssh2_sftp = ssh2_sftp($ssh_conn);
				
				echo "Upload files now...";
				
				for($x = 1; $x <= 100; $x++)
				{
					$this->notify_observers();
					usleep(250000);
				}
				
				echo "DONE with uploading files.";				
			}
			
			// Logout
			ssh2_exec( $ssh_conn, "exit" );
			unset( $ssh_conn );
		}		
	}
}

?>