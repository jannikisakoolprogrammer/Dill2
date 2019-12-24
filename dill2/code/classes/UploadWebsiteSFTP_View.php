<?php

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