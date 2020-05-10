<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxManageCSSFilesDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the wxDialog for adding, renaming and deleting CSS
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


class wxManageCSSFilesDialog extends wxDialog
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
			$title,
			wxDefaultPosition,
			wxDefaultSize,
			wxDEFAULT_DIALOG_STYLE | wxRESIZE_BORDER			
		);
		
		$this->parent = $parent;
		$this->website_project = $website_project;
		
		// Controls.
		$this->wxpanel = new wxPanel(
			$this,
			DILL2_WXID_WXMANAGECSSFILESDIALOG_WXPANEL
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
			0,
			wxEXPAND | wxRIGHT | wxBOTTOM | wxTOP,
			10
		);
		
		// wxListBox.
		$this->wxlistbox_cssfiles = new wxListBox(
			$this->wxpanel,
			DILL2_WXID_WXMANAGECSSFILESDIALOG_WXLISTBOX_CSSFILES,
			wxDefaultPosition,
			wxDefaultSize,
			array()
		);
		$this->refresh_related_controls( "CSS" );
		$this->wxboxsizer_vertical_child_left->Add(
			$this->wxlistbox_cssfiles,
			1,
			wxEXPAND
		);
		
		// wxButtons.
		$this->wxbutton_addcssfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGECSSFILESDIALOG_WXBUTTON_ADDCSSFILE,
			DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_ADDCSSFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_addcssfile,
			0,
			wxEXPAND
		);
		
		$this->wxbutton_renamecssfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEFILESDIALOG_WXBUTTON_RENAMECSSFILE,
			DILL2_TEXT_WXMANAGEFILESDIALOG_WXBUTTON_RENAMECSSFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_renamecssfile,
			0,
			wxEXPAND
		);
		
		$this->wxbutton_deletecssfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGECSSFILESDIALOG_WXBUTTON_DELETECSSFILE,
			DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_DELETECSSFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_deletecssfile,
			0,
			wxEXPAND
		);
		
		
		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel->SetSizer( $this->wxboxsizer_horizontal_parent );
		$this->wxboxsizer_horizontal_parent->SetSizeHints( $this );
		
		// Now we set up our eventhandlers.
		// Event that opens a dialog where the user can add a new CSS file.
		$this->Connect(
			DILL2_WXID_WXMANAGECSSFILESDIALOG_WXBUTTON_ADDCSSFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_addcssfile"
			)
		);
		
		// Event that opens a dialog where the user can rename the selected css file.
		$this->Connect(
			DILL2_WXID_WXMANAGEFILESDIALOG_WXBUTTON_RENAMECSSFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_renamecssfile"
			)
		);
		
		// Event that calls a function which deletes the selected css file.
		$this->Connect(
			DILL2_WXID_WXMANAGECSSFILESDIALOG_WXBUTTON_DELETECSSFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_deletecssfile"
			)
		);
	}
	
	
	public function run()
	{
		parent::ShowModal();
	}
	
	
	public function on_click_wxbutton_addcssfile()
	{
		/* This function shows a dialog window through which the user can add
		a new CSS file to the currently opened project.  The user must not
		forget to append the .css extension to the name of the CSS file.
		A CSS file with the same name must not yet exist.
		
		*/
		
		// First we need to create the dialog.
		$dialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_ADDCSSFILE_WXDIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_ADDCSSFILE_WXDIALOG_CAPTION
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
				if( $this->website_project->exists_file( "CSS", $user_input ) )
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
					/* Create the CSS file and refresh the wxListBox of the parent
					dialog. */
					$this->website_project->create_file( "CSS", $user_input );
					
					foreach(array("sync_ftps_file", "sync_sftp_file") as $tmp_table)
					{
						$this->website_project->sync_table_add_file(
							$tmp_table,
							"CSS" .
							DIRECTORY_SEPARATOR .
							$user_input);					
					}					
					
					/* Refresh the wxListBox that shows the CSS files and quit
					this function. */
					$this->refresh_related_controls( "CSS" );
					break;
				}
			}
			
		}
	}
	
	
	public function on_click_wxbutton_renamecssfile()
	{
		/* This function shows a dialog window through which the user can rename
		an existing CSS file.  An existing CSS file with the same name must not
		yet exist.  The user must not forget to append the .css extension to the
		name of the CSS file.
		
		*/
		
		// Store the name of the file which the user has selected.
		$user_listbox_choice = $this->wxlistbox_cssfiles->GetString(
			$this->wxlistbox_cssfiles->GetSelection()
		);
		
		// First we need to create the dialog.
		$dialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_RENAMECSSFILE_WXDIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_RENAMECSSFILE_WXDIALOG_CAPTION
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
				if( $this->website_project->exists_file( "CSS", $user_input ) )
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
						"CSS",
						$user_listbox_choice,
						$user_input
					);

					foreach(array("sync_ftps_file", "sync_sftp_file") as $tmp_table)
					{
						$this->website_project->sync_table_update_file(
							$tmp_table,
							"CSS" .
							DIRECTORY_SEPARATOR .
							$user_listbox_choice,
							"CSS" .
							DIRECTORY_SEPARATOR .
							$user_input);
					}	
					
					// Refresh related controls that display the CSS files.
					$this->refresh_related_controls( "CSS" );
					break;
				}
			}
		}
	}
	
	
	public function on_click_wxbutton_deletecssfile()
	{
		/* This function deletes a selected CSS file.  Nothing more.
		
		*/
		$file_to_delete = $this->wxlistbox_cssfiles->GetString(
			$this->wxlistbox_cssfiles->GetSelection()
		);
		
		$this->website_project->delete_file( "CSS", $file_to_delete );
		
		foreach(array("sync_ftps_file", "sync_sftp_file") as $tmp_table)
		{
			$this->website_project->sync_table_delete_file(
				$tmp_table,
				"CSS" .
				DIRECTORY_SEPARATOR .
				$file_to_delete);
		}			
		
		$this->refresh_related_controls( "CSS" );
	}
	
	
	private function refresh_related_controls( $filetype )
	{
		$this->wxlistbox_cssfiles->Set(
			$this->website_project->get_file_names_of_dir( $filetype )
		);
		$this->parent->wxlistbox_mainframe_cssfiles_reload();
	}
}
?>
