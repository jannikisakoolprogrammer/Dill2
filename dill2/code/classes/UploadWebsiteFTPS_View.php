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

class UploadWebsiteFTPS_View extends wxDialog
{
	public function __construct(
		$_parent,
		$_id,
		$_title
	)
	{
		parent::__construct(
			$_parent,
			$_id,
			$_title);		
	}
}
?>