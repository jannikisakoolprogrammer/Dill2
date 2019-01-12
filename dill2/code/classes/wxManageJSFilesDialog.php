<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxManageJSFilesDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the wxDialog for adding, renaming and deleting JS
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


class wxManageJSFilesDialog extends wxDialog
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
			DILL2_WXID_WXMANAGEJSFILESDIALOG_WXPANEL
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
		$this->wxlistbox_jsfiles = new wxListBox(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEJSFILESDIALOG_WXLISTBOX_JSFILES,
			wxDefaultPosition,
			wxDefaultSize,
			array()
		);
		$this->refresh_related_controls( "JS" );
		$this->wxboxsizer_vertical_child_left->Add(
			$this->wxlistbox_jsfiles,
			1,
			wxEXPAND
		);
		
		// wxButtons.
		$this->wxbutton_addjsfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEJSFILESDIALOG_WXBUTTON_ADDJSFILE,
			DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_ADDJSFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_addjsfile,
			1,
			wxEXPAND
		);
		
		$this->wxbutton_renamejsfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEJSFILESDIALOG_WXBUTTON_RENAMEJSFILE,
			DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_RENAMEJSFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_renamejsfile,
			1,
			wxEXPAND
		);
		
		$this->wxbutton_deletejsfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEJSFILESDIALOG_WXBUTTON_DELETEJSFILE,
			DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_DELETEJSFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_deletejsfile,
			1,
			wxEXPAND
		);
		
		
		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel->SetSizer( $this->wxboxsizer_horizontal_parent );
		$this->wxboxsizer_horizontal_parent->SetSizeHints( $this );
		
		// Now we set up our eventhandlers.
		// Event that opens a dialog where the user can add a new JS file.
		$this->Connect(
			DILL2_WXID_WXMANAGEJSFILESDIALOG_WXBUTTON_ADDJSFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_addjsfile"
			)
		);
		
		// Event that opens a dialog where the user can rename the selected js file.
		$this->Connect(
			DILL2_WXID_WXMANAGEJSFILESDIALOG_WXBUTTON_RENAMEJSFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_renamejsfile"
			)
		);
		
		// Event that calls a function which deletes the selected js file.
		$this->Connect(
			DILL2_WXID_WXMANAGEJSFILESDIALOG_WXBUTTON_DELETEJSFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_deletejsfile"
			)
		);		
	}
	
	
	public function run()
	{
		parent::ShowModal();
	}
	
	
	public function on_click_wxbutton_addjsfile()
	{
		/* This function shows a dialog window through which the user can add
		a new JS file to the currently opened project.  The user must not
		forget to append the .js extension to the name of the JS file.
		A JS file with the same name must not yet exist.
		
		*/
		
		// First we need to create the dialog.
		$dialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_ADDJSFILE_WXDIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_ADDJSFILE_WXDIALOG_CAPTION
		);
		
		// Showing the dialog.
		while( $dialog->ShowModal() == wxID_OK )
		{
			// The user must have entered something into the dialog.
			$user_input = trim( $dialog->GetValue());
			if( strlen( $user_input ) == 0 )
			{
				// Show a dialog telling the user that he should enter a filename.
				$wxmessagedialog = new wxMessageDialog(
					$dialog,
					"Error:  Please enter a filename",
					DILL2_TEXT_DIALOG_ERROR_CAPTION
				);
				$wxmessagedialog->ShowModal();
				continue;
			}
			else
			{
				// Verify that a file with the same name does not yet exist.
				if( $this->website_project->exists_file( "JS", $user_input ) )
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
					/* Create the JS file and refresh the wxListBox of the parent
					dialog. */
					$this->website_project->create_file( "JS", $user_input );
					
					/* Refresh the wxListBox that shows the JS files and quit
					this function. */
					$this->refresh_related_controls( "JS" );
					break;
				}
			}
			
		}
	}
	
	
	public function on_click_wxbutton_renamejsfile()
	{
		/* This function shows a dialog window through which the user can rename
		an existing JS file.  An existing JS file with the same name must not
		yet exist.  The user must not forget to append the .js extension to the
		name of the JS file.
		
		*/
		
		// Store the name of the file which the user has selected.
		$user_listbox_choice = $this->wxlistbox_jsfiles->GetString(
			$this->wxlistbox_jsfiles->GetSelection()
		);
		
		// First we need to create the dialog.
		$dialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_RENAMEJSFILE_WXDIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_RENAMEJSFILE_WXDIALOG_CAPTION
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
				if( $this->website_project->exists_file( "JS", $user_input ) )
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
						"JS",
						$user_listbox_choice,
						$user_input
					);
					// Refresh related controls that display the JS files.
					$this->refresh_related_controls( "JS" );
					break;
				}
			}
		}
	}
	
	
	public function on_click_wxbutton_deletejsfile()
	{
		/* This function deletes a selected JS file.  Nothing more.
		
		*/
		$file_to_delete = $this->wxlistbox_jsfiles->GetString(
			$this->wxlistbox_jsfiles->GetSelection()
		);
		
		$this->website_project->delete_file( "JS", $file_to_delete );
		$this->refresh_related_controls( "JS" );
	}
	
	
	private function refresh_related_controls( $filetype )
	{
		$this->wxlistbox_jsfiles->Set(
			$this->website_project->get_file_names_of_dir( $filetype )
		);
		$this->parent->wxlistbox_mainframe_javascript_files_reload();
	}	
}
?>
