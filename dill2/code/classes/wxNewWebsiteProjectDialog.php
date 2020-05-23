<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxNewWebsiteProjectDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the wxDialog class for used for creating a new website
	project for dill2.

*******************************************************************************/

// Include project related .php files.
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
	"classes" . DIRECTORY_SEPARATOR .
	"Dill2WebsiteProject.php"
);


class wxNewWebsiteProjectDialog extends wxDialog
{
	public function __construct(
		$website_project,	
		$parent,
		$message,
		$title
	)
	{
		parent::__construct(
			$parent,
			$message,
			$title
		);
		
		// Set some important variables.
		$this->parent = $parent;
		
		//
		// Create controls.
		//
		
		// wxPanel.
		$this->wxpanel = new wxPanel(
			$this,
			DILL2_WXID_WXNEWWEBSITEPROJECTDIALOG_WXPANEL
		);
		
		// wxBoxSizer.
		$this->wxboxsizer = new wxBoxSizer(
			wxVERTICAL
		);
		
		// Message.
		$this->wxstatictext_message = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXNEWWEBSITEPROJECTDIALOG_WXSTATICTEXT_MESSAGE,
			DILL2_TEXT_WXNEWWEBSITEPROJECTDIALOG_WXSTATICTEXT_MESSAGE
		);
		$this->wxboxsizer->Add(
			$this->wxstatictext_message,
			0,
			wxEXPAND | wxCENTRE | wxALL,
			10
		);
		
		// Textentry to enter the name of the new website project.
		$this->wxtextentry_newproject = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXNEWWEBSITEPROJECTDIALOG_WXTEXTCTRL_NEWPROJECT,
			wxEmptyString,
			wxDefaultPosition,
			wxDefaultSize,
			wxTE_PROCESS_ENTER,
			new wxTextValidator(
				wxFILTER_ALPHANUMERIC
			)
		);
		$this->wxboxsizer->Add(
			$this->wxtextentry_newproject,
			0,
			wxEXPAND | wxCENTRE | wxALL,
			10
		);
		
		// 'Cancel' and 'Ok' buttons.
		$this->wxboxsizer_buttons = new wxBoxSizer(
			wxHORIZONTAL
		);
		$this->wxbutton_cancel = new wxButton(
			$this->wxpanel,
			wxID_CANCEL
		);
		$this->wxboxsizer_buttons->Add(
			$this->wxbutton_cancel,
			0
		);		
		$this->wxbutton_ok = new wxButton(
			$this->wxpanel,
			wxID_OK
		);
		$this->wxboxsizer_buttons->Add(
			$this->wxbutton_ok,
			0
		);
		
		$this->wxboxsizer->Add(
			$this->wxboxsizer_buttons,
			1,
			wxALL | wxALIGN_RIGHT,
			10
		);
		
		
        // Dstribute / layout the controls in the mainframe.
        $this->wxpanel->SetSizer( $this->wxboxsizer );
	    $this->wxboxsizer->SetSizeHints( $this );
	}	
	
	public function run()
	{
		while( TRUE )
		{
			$result = $this->ShowModal();
			if ( $result == wxID_OK )
			{
				// Check if filename is valid.
				if (Dill2WebsiteProject::is_valid_filename_static($this->wxtextentry_newproject->GetValue()) == FALSE)
				{
					/* Show a dialog telling the user that the filename is invalid. */
					$wxmessagedialog = new wxMessageDialog(
						$this,
						"Error:  Invalid filename.  Only printable ASCII characters allowed.",
						DILL2_TEXT_DIALOG_ERROR_CAPTION
					);
					$wxmessagedialog->ShowModal();
					continue;				
				}	
				
				// Procedure for creating a new website project:
				// A website project with the same name must not yet exist.
				if( !file_exists( ".." .
									DIRECTORY_SEPARATOR .
									DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH .
									DIRECTORY_SEPARATOR .
									$this->wxtextentry_newproject->GetValue()
				))
				{
					// The website project can be created.
					$this->parent->website_project = new Dill2WebsiteProject(
						$this->wxtextentry_newproject->GetValue()
					);
					
					$this->parent->on_new_website_project_created();
					return;
				}
				else
				{
					/* A website project with the same name already exists.  The
					website project will not be created. */
					$wxmessagedialog = new wxMessageDialog(
						$this,
						sprintf(
							DILL2_TEXT_WXNEWWEBSITEPROJECTDIALOG_ERROR_NO_DUPLICATE_MESSAGE,
							$this->wxtextentry_newproject->GetValue()
						),
						"Caption",
						wxOK | wxICON_ERROR
					);
					$wxmessagedialog->ShowModal();
				}
			}
			else
			{
				return;
			}	
		}
	}
}

?>
