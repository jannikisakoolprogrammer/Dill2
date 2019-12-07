<?php

require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .	
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"core_constants.php"
);
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .	
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"lang_en.php"
);
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"wxphp_ids.php"
);

class UploadWebsiteFTPS_Presenter
{
	protected $view = NULL;
	protected $model = NULL;

	
	public function setView($_view = self::view)
	{
		$this->view = $_view;
	}
	
	
	public function setModel($_model = self::model)
	{
		$this->model = $_model;
	}
	
	
	public function run()
	{
		$this->view->ShowModal();
	}
}
?>