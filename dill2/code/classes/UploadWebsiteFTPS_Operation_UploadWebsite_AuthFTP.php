<?php

require_once("UploadWebsiteOperationBase.php");

class UploadWebsiteFTPS_Operation_UploadWebsite_AuthFTP extends UploadWebsiteOperationBase
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
		
		$ftps_conn = ftp_connect(
			$this->webserver_ip_address,
			$this->port);
		
		if ($ftps_conn)
		{
			// Change timeout to 10 seconds.
			ftp_set_option(
				$ftps_conn,
				FTP_TIMEOUT_SEC,
				10);
				
			echo "Connection established." . PHP_EOL;
			
			if(ftp_login(
				$ftps_conn,
				$this->username,
				$this->password))
			{
				echo "Logged in." . PHP_EOL;			
				
				// Change directory on local computer.
				$root_dir = getcwd() . DIRECTORY_SEPARATOR . $this->website_project->abspath_websiteproject_website;								
				/*
				@$this->upload_sync_delete_all(
					$ftps_conn,
					$this->webserver_path);			
				*/
				chdir($root_dir);
				ftp_chdir(
					$ftps_conn,
					$this->webserver_path);
				
				echo "Uploading files now..." . PHP_EOL;
				
				// Reset checked field -> set it it 0.
				$this->upload_sync_reset();
				
				// Upload sync add / update
				$this->upload_sync_add_update(
					$ftps_conn,
					$root_dir,
					$this->webserver_path);
				
				// Remove records not generated.
				$this->website_project->sync_file_upload_delete("sync_file_upload_ftps");
					
				// Upload sync delete
				$this->upload_sync_delete(
					$ftps_conn,
					".",
					$this->webserver_path,
					$root_dir);
				/*
				$this->upload_sync(
					$ftps_conn,
					$root_dir,
					$this->webserver_path
				);
				*/

				chdir( "../../../bin" );
				
				echo "Done uploading files." . PHP_EOL;
			}
			else
			{
				throw new Exception("Login failed.");
			}			
			
			ftp_close($ftps_conn);
		}
		else
		{
			throw new Exception("Connection failed.");
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
	
	
	public function do_search_in_sync_file_ftps_upload_table($_dir)
	{
		$js_path = $this->webserver_path . DIRECTORY_SEPARATOR . "js";
		$php_path = $this->webserver_path . DIRECTORY_SEPARATOR . "php";
		$css_path = $this->webserver_path . DIRECTORY_SEPARATOR . "css";
		$download_path = $this->webserver_path . DIRECTORY_SEPARATOR . "download";
		$media_path = $this->webserver_path . DIRECTORY_SEPARATOR . "media";
		
		switch($_dir)
		{
			case $js_path:
			case $php_path:
			case $css_path:
			case $download_path:
			case $media_path:
				return TRUE;
				
			default:
				return FALSE;
		}
	}
	
	
	public function upload_sync_reset()
	{
		// Reset the checked field.
		$this->website_project->db_update(
			"sync_page_upload_ftps",
			array("checked"),
			array(0),
			array(SQLITE3_INTEGER),
			array(
				"checked",
				"=",
				1,
				SQLITE3_INTEGER));
	}
	
	
	public function upload_sync_add_update(
		$_ftps_conn,
		$_local_file_path,
		$_remote_file_path)	
	{
		// Add, update files in that exist in "sync_file_upload_ftps" and "sync_page_upload_ftps".
		// We iterate through local dir.
		$dirit = new DirectoryIterator($_local_file_path);
		foreach($dirit as $fileinfo)
		{
			$local_file_path = $_local_file_path . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
			$remote_file_path = $_remote_file_path . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
			
			if( $fileinfo->isDot())
			{
				continue;
			}
			else if($fileinfo->isDir())
			{
				
				$listing = ftp_nlist(
					$_ftps_conn,
					$fileinfo->getFilename());				
				
				if (count($listing) >= 2)
				{
					// Directory already exists but without any files.
					// Go into that directory locally. (Recursion).					
					ftp_chdir(
						$_ftps_conn,
						$fileinfo->getFilename());
						
					$this->upload_sync_add_update(
						$_ftps_conn,
						$local_file_path,
						$remote_file_path);
						
					ftp_cdup($_ftps_conn);						
				}
				else
				{
					// Create directory remotely.
					ftp_mkdir(
						$_ftps_conn,
						$fileinfo->getFilename());
						
					echo sprintf(
						"Created directory '%s'." . PHP_EOL,
						$remote_file_path);	

					// Go into that directory locally. (Recursion).					
					ftp_chdir(
						$_ftps_conn,
						$fileinfo->getFilename());
						
					$this->upload_sync_add_update(
						$_ftps_conn,
						$local_file_path,
						$remote_file_path);
						
					ftp_cdup($_ftps_conn);	
				}
			}				
			else if($fileinfo->isFile())
			{
				// Check if the file already exists.
				// If the file already exists, compare creation dates.
				$listing = ftp_nlist(
					$_ftps_conn,
					$fileinfo->getFilename());
				
				if (count($listing) == 1)
				{
					// The file exists already.  Check creation dates.
					$remote_mod_date = ftp_mdtm($_ftps_conn, $fileinfo->getFilename());
					$local_mod_date = filemtime($local_file_path);
					
					if ($local_mod_date > $remote_mod_date)
					{
						// Update file.
						ftp_put(
							$_ftps_conn,
							$fileinfo->getFilename(),					
							$local_file_path,
							FTP_BINARY);			

						echo sprintf(
							"Updated file '%s'." . PHP_EOL,
							$remote_file_path);
					}
				}
				else
				{
					// Upload file.
					ftp_put(
						$_ftps_conn,
						$fileinfo->getFilename(),					
						$local_file_path,
						FTP_BINARY);
						
					echo sprintf(
						"Created file '%s'." . PHP_EOL,
						$remote_file_path);
				}
			}
			
			// Update progress bar.
			$this->notify_observers();
		}		
	}
	
	
	public function upload_sync_delete(
		$_ftps_conn,
		$_dst,
		$_cur_remote_path,
		$_cur_local_path)	
	{			
		$dirs_files = ftp_nlist(
			$_ftps_conn,
			".");
			
		foreach ($dirs_files as $x)
		{
			$cur_remote_path = $_cur_remote_path . DIRECTORY_SEPARATOR . $x;
			$cur_local_path = $_cur_local_path . DIRECTORY_SEPARATOR . $x;
			
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
				// If the path is a dir, change into it.
				$listing = ftp_nlist(
					$_ftps_conn,
					$x);
					
				if (count($listing) == 2)
				{
					// It is a directory.
					// Check if the directory exists locally.  If not, delete it remotely.

					if (file_exists($cur_local_path) == FALSE)
					{
						ftp_rmdir(
							$_ftps_conn,
							$x);
						echo sprintf(
							"Deleted directory '%s'." . PHP_EOL,
							$cur_remote_path);
					}
				}
				else if (count($listing) > 2)
				{
					// It is a directory with files in it.  Change into it.
					ftp_chdir(
						$_ftps_conn,
						$x);
								
					$this->upload_sync_delete(
						$_ftps_conn,
						$_dst,
						$cur_remote_path,
						$cur_local_path);						
														
					ftp_cdup($_ftps_conn);
					
					// Check if dir exists locally.  If not, deleted it remotely.
					if (file_exists($cur_local_path) == FALSE)
					{
						ftp_rmdir(
							$_ftps_conn,
							$x);
						echo sprintf(
							"Deleted directory '%s'." . PHP_EOL,
							$cur_remote_path);
					}					
				}
				else if (count($listing) == 1)
				{
					// It is a file.
					if (file_exists($cur_local_path) == FALSE)
					{
						ftp_delete(
							$_ftps_conn,
							$x);
						echo sprintf(
							"Deleted file '%s'." . PHP_EOL,
							$cur_remote_path);							
					}
				}
			}
		}		
	}
}

?>