<?php

class UploadWebsiteFTPS_View extends wxGenericProgressDialog
{
	public function __construct(
		$_wxwindow_parent)
	{
		parent::__construct(
			"FTP(S) - Updating the website",
			"",
			100,
			$_wxwindow_parent,
			wxPD_APP_MODAL);
		
		$this->SetSize(1024, 150);
	}
}
?>