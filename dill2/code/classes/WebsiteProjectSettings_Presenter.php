<?php

require_once("PresenterBase.php");

class WebsiteProjectSettings_Presenter extends PresenterBase
{
	public function __construct(
		$_view,
		$_model)
	{
		parent::__construct(
			$_view,
			$_model);
			
		$this->view->wxbutton_ok->Connect(
			wxEVT_COMMAND_BUTTON_CLICKED,
			array(
				$this,
				"ok_button_clicked"));			
	}		

	
	public function ok_button_clicked()
	{
		echo "Ok button has been clicked.";
	}

}

?>