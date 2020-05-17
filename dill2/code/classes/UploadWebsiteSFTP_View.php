<?php

class UploadWebsiteSFTP_View extends wxGenericProgressDialog
{
	public function __construct(
		$_wxwindow_parent)
	{
		parent::__construct(
			"SFTP - Updating the website",
			"",
			100,
			$_wxwindow_parent,
			wxPD_APP_MODAL);
	}
}

?>