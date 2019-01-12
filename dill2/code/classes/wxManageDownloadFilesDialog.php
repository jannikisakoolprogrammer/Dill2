<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxManageDOWNLOADFilesDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the wxDialog for adding, renaming and deleting DOWNLOAD
	files.

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


class wxManageDOWNLOADFilesDialog extends wxDialog
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
		$this->parent = $parent;
		$this->website_project = $website_project;
		
		// Controls.
		$this->wxpanel = new wxPanel(
			$this,
			DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG_WXPANEL
		);
		
		$this->wxboxsizer_horizontal_parent = new wxBoxSizer(
			wxHORIZONTAL
		);
		$this->wxboxsizer_vertical_child_left = new wxBoxSizer(
			wxVERTICAL
		);		
		$this->wxboxsizer_vertical_child_right = new wxBoxSizer(
			wxVERTICAL
		);
		
		$this->wxboxsizer_horizontal_parent->Add(
			$this->wxboxsizer_vertical_child_left,
			1,
			wxEXPAND | wxALL,
			10
		);
		$this->wxboxsizer_horizontal_parent->Add(
			$this->wxboxsizer_vertical_child_right,
			1,
			wxEXPAND | wxRIGHT | wxBOTTOM | wxTOP,
			10
		);
		
		// wxListBox.
		$this->wxlistbox_downloadfiles = new wxListBox(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG_WXLISTBOX_DOWNLOADFILES,
			wxDefaultPosition,
			wxDefaultSize,
			array()
		);
		$this->refresh_related_controls( "DOWNLOAD" );
		$this->wxboxsizer_vertical_child_left->Add(
			$this->wxlistbox_downloadfiles,
			1,
			wxEXPAND
		);
		
		// wxButtons.
		$this->wxbutton_adddownloadfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_ADDDOWNLOADFILE,
			DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_ADDDOWNLOADFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_adddownloadfile,
			1,
			wxEXPAND
		);
		
		$this->wxbutton_renamedownloadfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_RENAMEDOWNLOADFILE,
			DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_RENAMEDOWNLOADFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_renamedownloadfile,
			1,
			wxEXPAND
		);
		
		$this->wxbutton_deletedownloadfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_DELETEDOWNLOADFILE,
			DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_DELETEDOWNLOADFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_deletedownloadfile,
			1,
			wxEXPAND
		);
		
		
		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel->SetSizer( $this->wxboxsizer_horizontal_parent );
		$this->wxboxsizer_horizontal_parent->SetSizeHints( $this );
		
		// Events.
		$this->Connect(
			DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_ADDDOWNLOADFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_adddownloadfile"
			)
		);
		
		$this->Connect(
			DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_RENAMEDOWNLOADFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_renamedownloadfile"
			)
		);
		
		$this->Connect(
			DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_DELETEDOWNLOADFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_deletedownloadfile"
			)
		);
	}
	
	
	public function run()
	{
		parent::ShowModal();
	}
	
	
	public function on_click_wxbutton_adddownloadfile()
	{
		/* This function shows a dialog window through which the user can add
		a new download file to the currently opened project.
		A download file with the same name must not yet exist.
		
		*/
		
		// First we need to create the dialog.
		$dialog = new wxFileDialog(
			$this,
			DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_ADDDOWNLOADFILE_WXDIALOG_MESSAGE
		);
		
		// Showing the dialog.
		while( $dialog->ShowModal() == wxID_OK )
		{
			// The user must have entered something into the dialog.
			$filepath = trim( $dialog->GetPath());
			$filename = $dialog->GetFilename();
			
			// Verify that a file with the same name does not yet exist.
			if( $this->website_project->exists_file( "DOWNLOAD", $filename ) )
			{
			
				/* Show a dialog telling the user that he should choose
				another filename. */
				$wxmessagedialog = new wxMessageDialog(
					$dialog,
					"Error:  The same file already exists.",
					DILL2_TEXT_DIALOG_ERROR_CAPTION
				);
				$wxmessagedialog->ShowModal();
				continue;
				
			}
			else
			{
				/* Create the download file and refresh the wxListBox of the parent
				dialog. */
				$this->website_project->copy_file( "DOWNLOAD", $filename, $filepath );
				
				/* Refresh the wxListBox that shows the download files and quit
				this function. */
				$this->refresh_related_controls( "DOWNLOAD" );
				break;
			}
		}
	}
	
	
	public function on_click_wxbutton_renamedownloadfile()
	{
		/* This function shows a dialog window through which the user can rename
		an existing download file.  An existing download file with the same name must not
		yet exist.
		
		*/
		
		// Store the name of the file which the user has selected.
		$user_listbox_choice = $this->wxlistbox_downloadfiles->GetString(
			$this->wxlistbox_downloadfiles->GetSelection()
		);
		
		// First we need to create the dialog.
		$dialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_RENAMEDOWNLOADFILE_WXDIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_RENAMEDOWNLOADFILE_WXDIALOG_CAPTION
		);
		
		// Show the dialog so that the user can rename the selected file.
		while( $dialog->ShowModal() == wxID_OK )
		{
			// Verfiy that the user has entered something.
			$user_input = trim( $dialog->GetValue() );
			if( strlen( $user_input ) == 0 )
			{
				/* Show a message dialog telling the user that he should enter
				a new filename. */
				$errordialog = new wxMessageDialog(
					$dialog,
					"Error:  Please enter a new filename.",
					DILL2_TEXT_DIALOG_ERROR_CAPTION				
				);
				$errordialog->ShowModal();
				continue;
			}
			else
			{
				/* Make sure that the user has typed in a new filename which does
				not already exist. */
				if( $this->website_project->exists_file( "DOWNLOAD", $user_input ) )
				{
					// Error:  A file with the same name already exists.
					$errordialog = new wxMessageDialog(
						$dialog,
						"Error:  A file with the same name already exists.",
						DILL2_TEXT_DIALOG_ERROR_CAPTION					
					);
					$errordialog->ShowModal();
					continue;
				}
				else
				{
					// The existing file can be renamed.
					$this->website_project->rename_file(
						"DOWNLOAD",
						$user_listbox_choice,
						$user_input
					);
					// Refresh related controls that display the download files.
					$this->refresh_related_controls( "DOWNLOAD" );
					break;
				}
			}
		}
	}
	
	
	public function on_click_wxbutton_deletedownloadfile()
	{
		/* This function deletes a selected download file.  Nothing more.
		
		*/
		$file_to_delete = $this->wxlistbox_downloadfiles->GetString(
			$this->wxlistbox_downloadfiles->GetSelection()
		);
		
		$this->website_project->delete_file( "DOWNLOAD", $file_to_delete );
		$this->refresh_related_controls( "DOWNLOAD" );
	}
	
	
	private function refresh_related_controls( $filetype )
	{
		$this->wxlistbox_downloadfiles->Set(
			$this->website_project->get_file_names_of_dir( $filetype )
		);
		$this->parent->wxlistbox_mainframe_download_files_reload();
	}	
}
?>
