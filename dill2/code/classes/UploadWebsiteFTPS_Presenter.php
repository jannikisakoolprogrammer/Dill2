<?php

require_once("PresenterBase.php");


class UploadWebsiteFTPS_Presenter extends PresenterBase
{
	protected $percent = 0;
	
	public function __construct(
		$_view,
		$_model,
		$_logic)
	{
		parent::__construct(
			$_view,
			$_model,
			$_logic);
	}
	
	
	public function run()
	{
		$status = TRUE;
		$exception_msg = "";
		
		// Fetch website project settings.
		$this->model->retrieve_website_project_settings();
		
		// Decide whether to use FTPS or FTP.
		$this->model->retrieve_authentication_method();
		
		try
		{
			// Run upload.  Has been put into logic class.
			$this->logic->set_authentication_method(
				$this->model->get_authentication_method());
				
			$this->logic->set_website_project_settings(
				$this->model->get_website_project_settings());
				
			$this->logic->set_website_project(
				$this->model->get_website_project());
			
			// init operation to use.
			$this->logic->init_operation_upload_website();
			
			// Update progress bar count to the amount of files to upload.
			$this->view->setRange(
				$this->logic->get_operation()->get_total_files_dirs());
			
			$this->logic->run();
			
			$status = TRUE;
		}
		catch(Exception $e)
		{
			$status = FALSE;
			$exception_msg = $e->GetMessage();
		}
			
		// Show new dialog with either an success message or error message.
		// TODO...
	}
	
	
	public function update_bar($_percent)
	{
		$this->percent += $_percent;		
		$this->view->update($this->percent);			
	}
}
?>