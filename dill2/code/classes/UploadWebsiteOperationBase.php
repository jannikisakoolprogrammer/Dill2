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
}

?>