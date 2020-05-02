<?php

class GenerateWebsite_View extends wxGenericProgressDialog
{
	public $percent = NULL;
	public $number_of_pages_to_generate = NULL;
	
	
	public function __construct(
		$_wxwindow_parent,
		$_website_project)
	{
		$this->percent = 0;
		
		// Fetch the number of pages to generate.
		$this->number_of_pages_to_generate = count($_website_project->db_select("page"));
		
		parent::__construct(
			"Generating website",
			"Please wait while the website is being generated...",
			$this->number_of_pages_to_generate,
			$_wxwindow_parent);
		
		$_website_project->register_observer_generate_website($this);
	}
	
	
	public function update_progress_dialog()
	{
		$this->percent += 1;
		$this->update($this->percent);			
	}	
}
?>