<?php

abstract class PresenterBase
{
	protected $view = NULL;
	protected $model = NULL;
	
	
	public function __construct(
		$_view,
		$_model)
	{
		$this->view = $_view;
		$this->model = $_model;	
		
		// Register event handlers.
		$this->view->Connect(
			wxEVT_DESTROY,
			array(
				$this,
				"enableParentWindow"));		
	}
	
	
	public function disableParentWindow()
	{
		$this->view->GetParent()->Enable(FALSE);
	}
	
	
	public function enableParentWindow()
	{
		$this->view->GetParent()->Enable(TRUE);
	}
	
	public function run()
	{
		$this->disableParentWindow();
		$this->view->Show(TRUE);
	}
}

?>