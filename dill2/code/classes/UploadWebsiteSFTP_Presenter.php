<?php

require_once("PresenterBase.php");


class UploadWebsiteSFTP_Presenter extends PresenterBase
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
		
		// Decide which authentication method to use.
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
			
			// Init operation to use.
			$this->logic->init_operation_upload_website();
			
			// Update progress bar count to the amount of files to upload.
			$this->view->setRange(
				$this->logic->get_operation()->get_total_files_dirs());
			
			// Register presenter as observer.
			$this->logic->get_operation()->register_observer($this);			
				
			$this->logic->run();

			$status = TRUE;
		}
		catch(Exception $e)
		{
			$status = FALSE;
			$exception_msg = $e->GetMessage();
			echo $exception_msg . PHP_EOL;
			$this->view->destroy();			
		}
		
		
		// Show new dialog with either an success message or error message.
		// TODO...
	}
	
	
	public function update_bar($_percent)
	{
		$this->percent += $_percent;		
		$this->view->update($this->percent);
	}
	
	
	public function update(
		$_percent,
		$_status_text)
	{
		$this->percent += $_percent;
		$this->view->update(
			$this->percent,
			$_status_text);
		
		//$this->view->Fit();
	}
	
	
	public function set_range(
		$_elements)
	{
		$this->view->setRange($_elements);
	}
	
	
	public function reset_percent()
	{
		$this->percent = 0;
		$this->view->update($this->percent);
	}	
}

?>