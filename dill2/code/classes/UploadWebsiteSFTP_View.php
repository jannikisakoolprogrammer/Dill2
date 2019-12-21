<?php
/*
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"UploadWebsiteSFTP_wxphp_ids.php"
);
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"UploadWebsiteSFTP_lang_en.php"
);
*/

class UploadWebsiteSFTP_View extends wxGenericProgressDialog
{
	public function __construct(
		$_wxwindow_parent)
	{
		parent::__construct(
			"TITLE",
			"MESSAGE",
			100,
			$_wxwindow_parent);
	}
}


?>