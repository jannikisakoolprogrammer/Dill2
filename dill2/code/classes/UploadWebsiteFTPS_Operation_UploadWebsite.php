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

class wxDialogUploadWebsiteFTPS extends wxDialog
{
	protected static $_instance = NULL;	
	
	protected function __construct(){}
	protected function __clone(){}
	
	public static function getInstance(
		$_parent,
		$_id,
		$_title)
	{
		if (self::$_instance == NULL)
		{
			self::$_instance = parent::__construct(
				$_parent,
				$_id,
				$_title);
			
			self::$_instance->prepare_dialog();
		}
	}
}
?>