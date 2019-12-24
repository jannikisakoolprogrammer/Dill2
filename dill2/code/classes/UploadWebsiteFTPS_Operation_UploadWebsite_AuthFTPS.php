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
		
		// Connent using FTP
		// Note: FTPS support will come once wxPHP is ready for PHP7 :).
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
				
				ftp_chdir(
					$ftps_conn,
					"public_html");
				print_r(ftp_nlist($ftps_conn, ".")) . PHP_EOL;
				print_r(
					ftp_raw(
						$ftps_conn,
						sprintf(
							"md5sum %s",
							"index.php")));
			}
			
			ftp_close($ftps_conn);
		}		
		
		return;
		
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
	
	
	public function upload_sync( $ssh2_conn, $sftp_conn, $src, $dst )
	{
		$dirs_and_files_created_or_updated_or_existing = array();
	
		$dirit = new DirectoryIterator( $src );
		foreach( $dirit as $fileinfo )
		{
			if( $fileinfo->isDot())
			{
				continue;
			}
			if( $fileinfo->isDir())
			{
				$new_dir_path = $dst . "/" . $fileinfo->getFilename();
				ssh2_sftp_mkdir( $sftp_conn, $new_dir_path );
				$this->upload_sync(	
					$ssh2_conn,
					$sftp_conn,
					$src . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
					$new_dir_path
				);

				$dirs_and_files_created_or_updated_or_existing[] = $new_dir_path;
			
			}
			else if( $fileinfo->isFile())
			{
				// Compare checksums so we know if we need to copy / replace the file or not.
				$local_file_md5sum = calculate_md5_checksum($src . DIRECTORY_SEPARATOR . $fileinfo->getFilename());

				$remote_file_stream = ssh2_exec(
					$ssh2_conn,
					sprintf(
						"md5sum %s",
						$dst . "/" . $fileinfo->getFilename()
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
					echo sprintf( "Sending %s\n", $src . DIRECTORY_SEPARATOR . $fileinfo->getFilename() ) . PHP_EOL;
					ssh2_scp_send(
						$ssh2_conn,
						$src . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
						$dst . "/" . $fileinfo->getFilename()
					);		
				}
			
				$dirs_and_files_created_or_updated_or_existing[] = $dst . "/" . $fileinfo->getFilename();
						
			}
			
			// Update progress bar.
			$this->notify_observers();
		}
		//$dirs_and_files_created_or_updated_or_existing[] = $dst;
		//print_r( $dirs_and_files_created_or_updated_or_existing );

		// Delete directories and files inside the current directory.
		// Get an array of all remote directories and files of the current $dst.
		$stream1 = ssh2_exec(
			$ssh2_conn,
			sprintf(
				"dir -m %s",
				$dst
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

		$remote_dirs_and_files = array();

		foreach( $remote_dirs_and_files_raw as $thing )
		{
			if( strlen( trim( $thing ) )  > 0 )
			{
				$remote_dirs_and_files[] = $dst . "/" . trim( $thing );
			}
		}

		//$remote_dirs_and_files[] = $dst;

		$remote_dirs_and_files_to_delete_recursively = array_diff(
			$remote_dirs_and_files,
			$dirs_and_files_created_or_updated_or_existing
		);

		foreach( $remote_dirs_and_files_to_delete_recursively as $d )
		{
			if( ssh2_exec(
				$ssh2_conn,
				sprintf(
					"rm -r %s",
					$d
				)
			) )
			{
				echo sprintf( "Directory/File %s has been deleted.", $d ) . PHP_EOL;	
			}	
		}	
	}	
}

?>