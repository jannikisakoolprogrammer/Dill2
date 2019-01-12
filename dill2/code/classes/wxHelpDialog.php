<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxHelpDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the wxDialog for getting and displaying help.

*******************************************************************************/


// Include required .php project files.
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"wxphp_ids.php"
);
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


class myWxHyperlinkCtrl extends wxhyperlinkctrl
{
	public function SetVisited( $val = TRUE )
	{
		// do nothing
	}
}


class wxHelpDialog extends wxDialog
{
	public function __construct(
		$website_project,
		$parent,
		$id,
		$title
	)
	{
		parent::__construct(
			$parent,
			$id,
			$title
		);
		
		$this->website_project = $website_project;
		
		// Controls.
		$this->wxpanel = new wxPanel(
			$this,
			DILL2_WXID_WXHELPDIALOG_WXPANEL
		);
		
		$this->wxboxsizer_vertical_parent = new wxBoxSizer(
			wxVERTICAL
		);
		$this->wxboxsizer_horizontal_child_row1 = new wxBoxSizer(
			wxVERTICAL
		);
		
		
		// Users manual.  PDF link.
		$this->wxhyperlinkctrl = new myWxHyperlinkCtrl(
			$this->wxpanel,
			wxID_ANY,
			DILL2_USERS_MANUAL_LABEL,
			".." .
			DIRECTORY_SEPARATOR .
			"docs" .
			DIRECTORY_SEPARATOR .
			"dill2_usersmanual.pdf"
		);		
        $this->wxboxsizer_horizontal_child_row1->Add(
        	$this->wxhyperlinkctrl,
        	2,
        	wxEXPAND | wxALL
        );
		
		// Website.  Link.
		$this->wxhyperlinkctrl_website = new myWxHyperlinkCtrl(
			$this->wxpanel,
			wxID_ANY,
			DILL2_WEBSITE_LABEL,
			DILL2_WEBSITE_LINK
		);		
        $this->wxboxsizer_horizontal_child_row1->Add(
        	$this->wxhyperlinkctrl_website,
        	2,
        	wxEXPAND | wxALL
        );		
		
		
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_child_row1,
			1,
			wxEXPAND | wxALL,
			10
		);
		
		
		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel->SetSizer( $this->wxboxsizer_vertical_parent );
		$this->wxboxsizer_vertical_parent->SetSizeHints( $this );
	}
	
	
	public function run()
	{
		parent::ShowModal();
	}
}
?>
