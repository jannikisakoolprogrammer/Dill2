<?php

class UploadWebsiteFTPS_View extends wxGenericProgressDialog
{
	public function __construct(
		$_wxwindow_parent)
	{
		parent::__construct(
			"FTP(S)",
			"Upload in progress...",
			100,
			$_wxwindow_parent);
	}
}
?>