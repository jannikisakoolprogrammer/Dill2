<?php

require_once("UploadWebsiteOperationBase.php");

class UploadWebsiteFTPS_Operation_UploadWebsite_AuthFTP extends UploadWebsiteOperationBase
{	
	protected $password = NULL;
	
	protected $f_d_to_process_on_source = 0;
	protected $f_d_to_process_on_destination = 0;
	
	
	public function run()
	{
		$this->webserver_ip_address = $this->website_project_settings[0]["ftps_webserver_ip_address"];
		$this->username = $this->website_project_settings[0]["ftps_username"];
		$this->password = file_get_contents($this->website_project_settings[0]["ftps_password"]);
		$this->port = 21;
		$this->webserver_path = $this->website_project_settings[0]["ftps_webserver_path"];
		$this->mode_passive = $this->website_project_settings[0]["ftps_mode_passive"];
		
		$ftps_conn = ftp_connect(
			$this->webserver_ip_address,
			$this->port);
		
		if ($ftps_conn)
		{
			// Set timeout to 20 seconds.
			ftp_set_option(
				$ftps_conn,
				FTP_TIMEOUT_SEC,
				20);
				
			echo "Connection established." . PHP_EOL;
			
			if(ftp_login(
				$ftps_conn,
				$this->username,
				$this->password))
			{
				echo "Logged in." . PHP_EOL;
				
				// Set mode to either active or passive.  True = passive.
				ftp_pasv(
					$ftps_conn,
					$this->mode_passive);		
				
				// Change directory on local computer.
				$root_dir = getcwd() . 
					DIRECTORY_SEPARATOR .
					$this->website_project->abspath_websiteproject_website;
								
				chdir($root_dir);
				
				// Change directory on remote computer.
				ftp_chdir(
					$ftps_conn,
					$this->webserver_path);
				
				echo "Updating website." . PHP_EOL;
				
				// Calculate files and directories to process on source.
				$this->calculate_files_dirs_to_process_on_source(
					$root_dir);
					
				$this->f_d_to_process_on_source += 1; // Prevent "Done".
					
				$this->reset_percent();					
					
				$this->progress_bar_set_range(
					$this->f_d_to_process_on_source);				
				
				// Upload sync add / update
				$this->upload_sync_add_update(
					$ftps_conn,
					$root_dir,
					$this->webserver_path);
					
				// Calculate files and directories to process on destination.
				$this->calculate_files_dirs_to_process_on_destination(
					$ftps_conn);
					
				$this->f_d_to_process_on_destination += 1; // Prevent "Done".
					
				$this->reset_percent();
					
				$this->progress_bar_set_range(
					$this->f_d_to_process_on_destination);					
					
				// Upload sync delete
				$this->upload_sync_delete(
					$ftps_conn,
					".",
					$this->webserver_path,
					$root_dir);

				chdir( "../../../bin" );
				
				echo "Updating website finished." . PHP_EOL;
				
				// Update progress bar.
				$this->progress_bar_update(
					"Website updated.");
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
			else
			{
			
				// Update progress bar.
				$this->progress_bar_update(
					sprintf(
						"Step 1: Processing source path: %s",
						$local_file_path));
						
				if($fileinfo->isDir())
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
					
					if (empty($listing) == TRUE)
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
					else
					{
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
				}
			}					
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
				// Update progress bar.
				$this->progress_bar_update(
					sprintf(
						"Step 2: Processing destination path: %s",
						$cur_remote_path));
					
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
	
	
	public function calculate_files_dirs_to_process_on_source(
		$_local_file_path)
	{
		$dirit = new DirectoryIterator($_local_file_path);
		foreach($dirit as $fileinfo)
		{
			$local_file_path = $_local_file_path . DIRECTORY_SEPARATOR . $fileinfo->getFilename();
			
			if( $fileinfo->isDot())
			{
				continue;
			}
			else if($fileinfo->isDir())
			{
				$this->f_d_to_process_on_source += 1;
				
				$this->calculate_files_dirs_to_process_on_source(
					$local_file_path);
			}				
			else if($fileinfo->isFile())
			{
				$this->f_d_to_process_on_source += 1;
			}	
		}			
	}
	
	
	public function calculate_files_dirs_to_process_on_destination(
		$_ftps_conn)
	{			
		$dirs_files = ftp_nlist(
			$_ftps_conn,
			".");
			
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
				$listing = ftp_nlist(
					$_ftps_conn,
					$x);

				if (count($listing) >= 2)
				{
					// It is a directory with files in it.  Change into it.					
					$this->f_d_to_process_on_destination += 1;
					
					ftp_chdir(
						$_ftps_conn,
						$x);
								
					$this->calculate_files_dirs_to_process_on_destination(
						$_ftps_conn);						
														
					ftp_cdup($_ftps_conn);				
				}
				else if (count($listing) == 1)
				{
					// It is a file.
					$this->f_d_to_process_on_destination += 1;
				}
			}	
		}			
	}
}

?>