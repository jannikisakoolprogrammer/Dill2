<?php

require_once("UploadWebsiteOperationBase.php");

class UploadWebsiteFTPS_Operation_UploadWebsite_AuthFTPS extends UploadWebsiteOperationBase
{	
	protected $password = NULL;
	
	
	public function run()
	{
		$this->webserver_ip_address = $this->website_project_settings[0]["ftps_webserver_ip_address"];
		$this->username = $this->website_project_settings[0]["ftps_username"];
		$this->password = file_get_contents($this->website_project_settings[0]["ftps_password"]);
		$this->port = 21;
		$this->webserver_path = $this->website_project_settings[0]["ftps_webserver_path"];
		
		echo "Total files: " . $this->total_files_dirs . PHP_EOL;
		
		echo "IMPORTANT NOTICE: FTPS support will be added once wxPHP is ready for PHP7 :)." . PHP_EOL;
		
		// Connent using FTP
		// Note: FTPS support will be added once wxPHP is ready for PHP7 :).
		$ftps_conn = ftp_connect(
			$this->webserver_ip_address,
			$this->port);
		
		if ($ftps_conn)
		{
			echo "Connection established." . PHP_EOL;
			
			if(ftp_login(
				$ftps_conn,
				$this->username,
				$this->password))
			{
				echo "Logged in." . PHP_EOL;			
				
				// Change directory on local computer.
				$root_dir = getcwd() . DIRECTORY_SEPARATOR . $this->website_project->abspath_websiteproject_website;
				
				@$this->upload_sync_delete_all(
					$ftps_conn,
					$this->webserver_path);			
				
				chdir($root_dir);
				ftp_chdir(
					$ftps_conn,
					$this->webserver_path);
				
				echo "Upload files now..." . PHP_EOL;
				
				$this->upload_sync(
					$ftps_conn,
					$root_dir,
					$this->webserver_path
				);

				chdir( "../../../bin" );
				
				echo "Done with uploading files." . PHP_EOL;
			}
			
			ftp_close($ftps_conn);
		}	
	}
	
	
	function ends_with($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}	
	
	
	public function upload_sync_delete_all(
		$_ftps_conn,
		$_dst)
	{
		$dirs_files = ftp_nlist(
			$_ftps_conn,
			$_dst);
			
		foreach ($dirs_files as $x)
		{
			if ($this->ends_with(
				$x,
				".."))
			{
				continue;
			}
			else if ($this->ends_with(
				$x,
				"."))
			{
				continue;
			}
			else
			{
				// Assume it is a file and try to delete it.				
				if (ftp_delete(
					$_ftps_conn,
					$x) == FALSE)
				{
					// If it is not a file, then it is a directory.  Go into that directory.
					$this->upload_sync_delete_all(
						$_ftps_conn,
						$x);
					
					// Delete the directory now.
					ftp_rmdir(
						$_ftps_conn,
						$x);
				}
			}
		}
	}
	
	
	public function upload_sync(
		$_ftps_conn,
		$_src,
		$_dst)
	{
		$dirs_and_files_created_or_updated_or_existing = array();
	
		$dirit = new DirectoryIterator( $_src );
		foreach( $dirit as $fileinfo )
		{
			if( $fileinfo->isDot())
			{
				continue;
			}
			
			if( $fileinfo->isDir())
			{
				$new_src_path = $_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
				$new_dst_path = $_dst . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
				
				echo sprintf(
					"Creating directory %s\n",
					$new_dst_path) . PHP_EOL;
				
				ftp_mkdir(
					$_ftps_conn,
					$fileinfo->getFilename());
					
				ftp_chdir(
					$_ftps_conn,
					$fileinfo->getFilename());					
					
				$this->upload_sync(	
					$_ftps_conn,
					$new_src_path,
					$fileinfo->getFilename());
				
				ftp_cdup($_ftps_conn);
			}
			else if( $fileinfo->isFile())
			{
				// Comparing checksums is not possible with only ftp access.
				// Comparing file sizes is not supported on every ftp server.
				// This, I won't be implementing any of those features at the moment.
				// This may change in future; who knows.
				
				// For now, every file will be replaced.
				// Compare checksums so we know if we need to copy / replace the file or not.

				echo sprintf(
					"Uploading file %s\n",
					$_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename()) . PHP_EOL;
					
				ftp_put(
					$_ftps_conn,
					$fileinfo->getFilename(),					
					$_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
					FTP_BINARY);
			}
			
			// Update progress bar.
			$this->notify_observers();
		}	
	}	
}

?>