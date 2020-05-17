<?php

require_once("UploadWebsiteOperationBase.php");

class UploadWebsiteSFTP_Operation_UploadWebsite_AuthSFTP extends UploadWebsiteOperationBase
{	
	protected $password = NULL;
	
	protected $f_d_to_process_on_source = 0;
	protected $f_d_to_process_on_destination = 0;
	protected $dirs_and_files_created_or_updated_or_existing = [];
	protected $remote_dirs_and_files = [];
	protected $remote_dirs_and_files_to_delete_recursively = [];

	
	public function run()
	{
		$this->webserver_ip_address = $this->website_project_settings[0]["sftp_webserver_ip_address"];
		$this->username = $this->website_project_settings[0]["sftp_username"];
		$this->password = file_get_contents($this->website_project_settings[0]["sftp_password"]);
		$this->port = 22;
		$this->webserver_path = $this->website_project_settings[0]["sftp_webserver_path"];
		
		// Let's try to establish the connection.
		$ssh_conn = ssh2_connect(
			$this->webserver_ip_address,
			$this->port);
			
		if($ssh_conn != FALSE)
		{
			// A connection to the web-server has now been established.
			echo "Connection established." . PHP_EOL;
			
			// Accecpt the fingerprint automatically.
			ssh2_fingerprint($ssh_conn);	

			// Login user username and password.
			if( ssh2_auth_password(
				$ssh_conn,
				$this->username,
				$this->password))
			{
				echo "Logged in." . PHP_EOL;
				
				// And now it is time to upload the website to the webserver.
				// We change the current directory on the local computer and
				// on the webserver aswell.

				// Local computer:
				$root_dir = getcwd() .
					DIRECTORY_SEPARATOR .
					$this->website_project->abspath_websiteproject_website;	

				chdir($root_dir);
					
				// Now let's upload the website.
				$ssh2_sftp = ssh2_sftp($ssh_conn);
				
				echo "Updating website." . PHP_EOL;
				
				// Calculate files and directories to process on source.
				$this->calculate_files_dirs_to_process_on_source(
					$root_dir);

				$this->f_d_to_process_on_source += 1; // Prevent "Done".
				
				$this->reset_percent();					
					
				$this->progress_bar_set_range(
					$this->f_d_to_process_on_source);				
				
				$this->upload_sync_add_update(
					$ssh_conn,
					$ssh2_sftp,
					$root_dir,
					$this->webserver_path);
				
				
				// Calculate files and directories to process on destination.
				$this->calculate_files_dirs_to_process_on_destination();
					
				$this->f_d_to_process_on_destination += 1; // Prevent "Done".			
					
				$this->reset_percent();
					
				$this->progress_bar_set_range(
					$this->f_d_to_process_on_destination);					
					
				$this->upload_sync_delete(
					$ssh_conn);

				chdir( "../../../bin" );
				
				echo "Updating website finished." . PHP_EOL;
				
				// Update progress bar.
				$this->progress_bar_update(
					"Website updated.");				
			}
			else
			{
				unset( $ssh_conn );				
				throw new Exception("Updating website has failed.");
			}
			
			// Logout
			ssh2_exec( $ssh_conn, "exit" );
			unset( $ssh_conn );
		}
		else
		{
			throw new Exception("Updating website has failed.");
		}
	}
	
	
	public function upload_sync_add_update(
		$_ssh2_conn,
		$_sftp_conn,
		$_src,
		$_dst)
	{	
		$dirit = new DirectoryIterator( $_src );
		foreach( $dirit as $fileinfo )
		{
			if( $fileinfo->isDot())
			{
				continue;
			}
			else
			{
				if( $fileinfo->isDir())
				{
					$new_dir_path = $_dst . "/" . $fileinfo->getFilename();
					
					// Update progress bar.
					$this->progress_bar_update(
						sprintf(
							"Step 1: Processing source path: %s",
							$_src .
							DIRECTORY_SEPARATOR .
							$fileinfo->getFilename(),
							$new_dir_path));
					
					ssh2_sftp_mkdir( $_sftp_conn, $new_dir_path );
					$this->upload_sync_add_update(	
						$_ssh2_conn,
						$_sftp_conn,
						$_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
						$new_dir_path
					);

					$this->dirs_and_files_created_or_updated_or_existing[] = $new_dir_path;
				
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
					$local_file_md5sum = calculate_md5_checksum($_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename());

					$remote_file_stream = ssh2_exec(
						$_ssh2_conn,
						sprintf(
							"md5sum %s",
							$_dst . "/" . $fileinfo->getFilename()
						)
					);
					stream_set_blocking( $remote_file_stream, TRUE );
					$remote_file_md5sum = explode(
						" ",
						stream_get_contents(
							$remote_file_stream
						)
					)[0];
					fclose( $remote_file_stream );

					if( $local_file_md5sum !== $remote_file_md5sum )
					{
						echo sprintf( "Sending %s\n", $_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename() ) . PHP_EOL;
						ssh2_scp_send(
							$_ssh2_conn,
							$_src . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
							$_dst . "/" . $fileinfo->getFilename()
						);		
					}
				
					$this->dirs_and_files_created_or_updated_or_existing[] = $_dst . "/" . $fileinfo->getFilename();
							
				}
			}
		}

		// Delete directories and files inside the current directory.
		// Get an array of all remote directories and files of the current $_dst.
		$stream1 = ssh2_exec(
			$_ssh2_conn,
			sprintf(
				"dir -m %s",
				$_dst
			)
		);

		stream_set_blocking( $stream1, TRUE );
		$remote_dirs_and_files_raw = explode(
			",",
			stream_get_contents(
				$stream1
			)
		);
		fclose( $stream1 );

		foreach( $remote_dirs_and_files_raw as $thing )
		{
			if( strlen( trim( $thing ) )  > 0 )
			{
				$this->remote_dirs_and_files[] = $_dst . "/" . trim( $thing );
			}
		}		
	}
	
	
	public function upload_sync_delete(
		$_ssh2_conn)
	{
		$this->remote_dirs_and_files_to_delete_recursively = array_diff(
			$this->remote_dirs_and_files,
			$this->dirs_and_files_created_or_updated_or_existing
		);

		foreach( $this->remote_dirs_and_files_to_delete_recursively as $d )
		{
			// Update progress bar.
			$this->progress_bar_update(
				sprintf(
					"Step 2: Processing destination path: %s",
					$d));
						
			if( ssh2_exec(
				$_ssh2_conn,
				sprintf(
					"rm -r %s",
					$d)))
			{
				echo sprintf( "Directory/File %s has been deleted.", $d ) . PHP_EOL;	
			}	
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