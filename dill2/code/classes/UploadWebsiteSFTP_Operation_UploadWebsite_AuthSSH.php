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
		
		echo "Total files: " . $this->total_files_dirs . PHP_EOL;

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

			$this->upload_sync(
				$sftp_conn,
				$root_dir,
				$this->webserver_path);

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
	
	
	public function upload_sync(
		$_sftp_conn,
		$_src,
		$_dst)
	{
		$dirs_and_files_created_or_updated_or_existing = array();
	
		$dirit = new DirectoryIterator($_src);
		foreach( $dirit as $fileinfo )
		{
			if( $fileinfo->isDot())
			{
				continue;
			}
			if( $fileinfo->isDir())
			{
				$new_src_path = $_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
				$new_dst_path = $_dst . "/" . $fileinfo->getFilename();				
				$_sftp_conn->mkdir($new_dst_path);
				$this->upload_sync(	
					$_sftp_conn,
					$new_src_path,
					$new_dst_path
				);

				$dirs_and_files_created_or_updated_or_existing[] = $new_dst_path;
			
			}
			else if( $fileinfo->isFile())
			{
				// Compare checksums so we know if we need to copy / replace the file or not.
				$local_file_md5sum = calculate_md5_checksum(
					$_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename());

				$remote_file_stream = $_sftp_conn->exec(
					sprintf(
						"md5sum %s",
						$_dst . "/" . $fileinfo->getFilename()));
				
				$remote_file_md5sum = explode(
					" ",
					$remote_file_stream)[0];

				if( $local_file_md5sum !== $remote_file_md5sum )
				{
					echo sprintf( "Sending %s\n",
						$_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename()) . PHP_EOL;
						
					$_sftp_conn->put(
						$_dst . "/" . $fileinfo->getFilename(),
						file_get_contents($_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename()));		
				}
			
				$dirs_and_files_created_or_updated_or_existing[] = $_dst . "/" . $fileinfo->getFilename();
						
			}
			
			// Update progress bar.
			$this->notify_observers();
		}

		// Delete directories and files inside the current directory.
		// Get an array of all remote directories and files of the current $dst.		
		$stream1 = $_sftp_conn->exec(
			sprintf(
				"dir -m %s\n",
				$_dst
			)
		);	
		
		$remote_dirs_and_files_raw = explode(
			",",
			$stream1);

		$remote_dirs_and_files = array();

		foreach($remote_dirs_and_files_raw as $thing)
		{
			if(strlen(trim($thing)) > 0)
			{
				$remote_dirs_and_files[] = $_dst . "/" . trim( $thing );
			}
		}

		$remote_dirs_and_files_to_delete_recursively = array_diff(
			$remote_dirs_and_files,
			$dirs_and_files_created_or_updated_or_existing
		);

		foreach($remote_dirs_and_files_to_delete_recursively as $d)
		{
			$_sftp_conn->exec(
				sprintf(
					"rm -r %s",
					$d));

			echo sprintf( "Directory/File %s has been deleted.", $d ) . PHP_EOL;
		}	
	}	
	
}

?>