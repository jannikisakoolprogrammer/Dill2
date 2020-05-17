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
	
	
	protected $f_d_to_process_on_source = 0;
	protected $f_d_to_process_on_destination = 0;	
	protected $dirs_and_files_created_or_updated_or_existing = [];
	protected $remote_dirs_and_files = [];
	protected $remote_dirs_and_files_to_delete_recursively = [];	
	
	
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
		
		// Let's try to establish the connection.
		$sftp_conn = new Net_SFTP($this->webserver_ip_address);
		$key = new Crypt_RSA();
		$key->setPassword($this->privatekey_passphrase);
		$key->setPublicKeyFormat(CRYPT_RSA_PUBLIC_FORMAT_OPENSSH);
		$key->loadKey(file_get_contents($this->privatekey));
		
		if ($sftp_conn->login(
			$this->username,
			$key))
		{
			echo "Connection established." . PHP_EOL;
			echo "Logged in." . PHP_EOL;
			
			// And now it is time to upload the website to the webserver.
			// We change the current directory on the local computer and
			// on the webserver aswell.

			// Local computer:
			$root_dir = getcwd() .				
				DIRECTORY_SEPARATOR .
				$this->website_project->abspath_websiteproject_website;

			chdir($root_dir);
			
			echo "Updating website." . PHP_EOL;
			
			// Calculate files and directories to process on source.
			$this->calculate_files_dirs_to_process_on_source(
				$root_dir);

			$this->f_d_to_process_on_source += 1; // Prevent "Done".
			
			$this->reset_percent();					
				
			$this->progress_bar_set_range(
				$this->f_d_to_process_on_source);				
			
			$this->upload_sync_add_update(
				$sftp_conn,
				$root_dir,
				$this->webserver_path);
				
				
			// Calculate files and directories to process on destination.
			$this->calculate_files_dirs_to_process_on_destination();
				
			$this->f_d_to_process_on_destination += 1; // Prevent "Done".			
				
			$this->reset_percent();
				
			$this->progress_bar_set_range(
				$this->f_d_to_process_on_destination);					
				
			$this->upload_sync_delete(
				$sftp_conn);

			chdir( "../../../bin" );
			
			echo "Updating website finished." . PHP_EOL;
			
			// Update progress bar.
			$this->progress_bar_update(
				"Website updated.");
			
			// Logout
			$sftp_conn->disconnect();			
		}
		else
		{
			throw new Exception("Updating website has failed.");
		}
					
		unset($key);
		unset($sftp_conn);
	}
	
	
	public function upload_sync_add_update(
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
				
				// Update progress bar.
				$this->progress_bar_update(
					sprintf(
						"Step 1: Processing source path: %s",
						$new_src_path,
						$new_dst_path));
				
				$_sftp_conn->mkdir($new_dst_path);
				$this->upload_sync_add_update(	
					$_sftp_conn,
					$new_src_path,
					$new_dst_path
				);

				$this->dirs_and_files_created_or_updated_or_existing[] = $new_dst_path;
			
			}
			else if( $fileinfo->isFile())
			{
				// Update progress bar.
				$this->progress_bar_update(
					sprintf(
						"Step 1: Processing source path: %s",
						$_src .
						DIRECTORY_SEPARATOR .
						$fileinfo->getFilename()));
							
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
			
				$this->dirs_and_files_created_or_updated_or_existing[] = $_dst . "/" . $fileinfo->getFilename();
						
			}
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
				$this->remote_dirs_and_files[] = $_dst . "/" . trim( $thing );
			}
		}	
	}
	
	
	public function upload_sync_delete(
		$_sftp_conn)
	{
		$this->remote_dirs_and_files_to_delete_recursively = array_diff(
			$this->remote_dirs_and_files,
			$this->dirs_and_files_created_or_updated_or_existing
		);

		foreach($this->remote_dirs_and_files_to_delete_recursively as $d)
		{
			// Update progress bar.
			$this->progress_bar_update(
				sprintf(
					"Step 2: Processing destination path: %s",
					$d));
					
			$_sftp_conn->exec(
				sprintf(
					"rm -r %s",
					$d));

			echo sprintf( "Directory/File %s has been deleted.", $d ) .
				PHP_EOL;
		}			
	}
	
	
	public function calculate_files_dirs_to_process_on_source(
		$_src)
	{
		$dirit = new DirectoryIterator( $_src );
		foreach( $dirit as $fileinfo )
		{
			if( $fileinfo->isDot())
			{
				continue;
			}
			if( $fileinfo->isDir())
			{
				$this->f_d_to_process_on_source += 1;
				
				$this->calculate_files_dirs_to_process_on_source(	
					$_src .
					DIRECTORY_SEPARATOR .
					$fileinfo->getFilename());			
			}
			else if( $fileinfo->isFile())
			{
				$this->f_d_to_process_on_source += 1;						
			}
		}			
	}
	
	
	public function calculate_files_dirs_to_process_on_destination()
	{
		$local_remote_dirs_and_files_to_delete_recursively = array_diff(
			$this->remote_dirs_and_files,
			$this->dirs_and_files_created_or_updated_or_existing
		);

		foreach( $local_remote_dirs_and_files_to_delete_recursively as $d )
		{
			$this->f_d_to_process_on_destination += 1;
		}
	}	
	
}

?>