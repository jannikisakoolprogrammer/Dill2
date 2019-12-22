<?php

require_once("OperationBase.php");


abstract class UploadWebsiteOperationBase extends OperationBase
{
	protected $website_project_settings = NULL;
	protected $webserver_ip_address = NULL;
	protected $webserver_path = NULL;
	protected $username = NULL;
	protected $port = NULL;
	
	protected $website_project = NULL;
	protected $total_files_dirs = 0;
	
	
	public function __construct(
		$_website_project_settings,
		$_website_project)
	{
		$this->website_project_settings = $_website_project_settings;
		$this->website_project = $_website_project;
		
		$this->observers = [];
		
		$root_dir = getcwd() . DIRECTORY_SEPARATOR . $this->website_project->abspath_websiteproject_website;		
		
		$this->count_total_files_to_upload($root_dir);
	}
		
	
	public function notify_observers()
	{
		foreach($this->observers as $obs)
		{
			$obs->update_bar(1);
		}
	}
	
	
	protected function count_total_files_to_upload($_dir)
	{
		$dirit = new DirectoryIterator( $_dir );
		foreach( $dirit as $fileinfo )
		{
			if( $fileinfo->isDot())
			{
				continue;
			}
			if( $fileinfo->isDir())
			{
				$this->count_total_files_to_upload(
					$_dir . DIRECTORY_SEPARATOR . $fileinfo->getFilename());
				
				$this->total_files_dirs++;
			}
			else if( $fileinfo->isFile())
			{
				$this->total_files_dirs++;
			}
		}
	}
	
	
	public function get_total_files_dirs()
	{
		return $this->total_files_dirs;
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