<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxManageMediaFilesDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the wxDialog for adding, renaming and deleting media
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


class wxManageMediaFilesDialog extends wxDialog
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
			DILL2_WXID_WXMANAGEMEDIAFILESDIALOG_WXPANEL
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
		$this->wxlistbox_mediafiles = new wxListBox(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEMEDIAFILESDIALOG_WXLISTBOX_MEDIAFILES,
			wxDefaultPosition,
			wxDefaultSize,
			array()
		);
		$this->refresh_related_controls( "MEDIA" );
		$this->wxboxsizer_vertical_child_left->Add(
			$this->wxlistbox_mediafiles,
			1,
			wxEXPAND
		);
		
		// wxButtons.
		$this->wxbutton_addmediafile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_ADDMEDIAFILE,
			DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_ADDMEDIAFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_addmediafile,
			0,
			wxEXPAND
		);
		
		$this->wxbutton_renamemediafile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_RENAMEMEDIAFILE,
			DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_RENAMEMEDIAFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_renamemediafile,
			0,
			wxEXPAND
		);
		
		$this->wxbutton_deletemediafile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_DELETEMEDIAFILE,
			DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_DELETEMEDIAFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_deletemediafile,
			0,
			wxEXPAND
		);
		
		
		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel->SetSizer( $this->wxboxsizer_horizontal_parent );
		$this->wxboxsizer_horizontal_parent->SetSizeHints( $this );
		
		
		// Events.
		$this->Connect(
			DILL2_WXID_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_ADDMEDIAFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_addmediafile"
			)
		);
		
		$this->Connect(
			DILL2_WXID_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_RENAMEMEDIAFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_renamemediafile"
			)
		);
		
		$this->Connect(
			DILL2_WXID_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_DELETEMEDIAFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_deletemediafile"
			)
		);
	}
	
	
	public function run()
	{
		parent::ShowModal();
	}
	
	
	public function on_click_wxbutton_addmediafile()
	{
		/* This function shows a dialog window through which the user can add
		a new media file to the currently opened project.
		A media file with the same name must not yet exist.
		
		*/
		
		// First we need to create the dialog.
		$dialog = new wxFileDialog(
			$this,
			DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_ADDMEDIAFILE_WXDIALOG_MESSAGE
		);
		
		// Showing the dialog.
		while( $dialog->ShowModal() == wxID_OK )
		{
			// The user must have entered something into the dialog.
			$filepath = trim( $dialog->GetPath());
			$filename = $dialog->GetFilename();
			
			// Verify that a file with the same name does not yet exist.
			if( $this->website_project->exists_file( "MEDIA", $filename ) )
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
				/* Create the media file and refresh the wxListBox of the parent
				dialog. */
				$this->website_project->copy_file( "MEDIA", $filename, $filepath );
				
				foreach(array("sync_file_generate") as $tmp_table)
				{
					$this->website_project->sync_table_add_file(
						$tmp_table,
						"MEDIA" .
						DIRECTORY_SEPARATOR .
						$filename);					
				}			
				
				/* Refresh the wxListBox that shows the media files and quit
				this function. */
				$this->refresh_related_controls( "MEDIA" );
				break;
			}
		}
	}
	
	
	public function on_click_wxbutton_renamemediafile()
	{
		/* This function shows a dialog window through which the user can rename
		an existing media file.  An existing media file with the same name must not
		yet exist.
		
		*/
		
		// Store the name of the file which the user has selected.
		$user_listbox_choice = $this->wxlistbox_mediafiles->GetString(
			$this->wxlistbox_mediafiles->GetSelection()
		);
		
		// First we need to create the dialog.
		$dialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_RENAMEMEDIAFILE_WXDIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_RENAMEMEDIAFILE_WXDIALOG_CAPTION
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
				if( $this->website_project->exists_file( "MEDIA", $user_input ) )
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
						"MEDIA",
						$user_listbox_choice,
						$user_input
					);
					
					foreach(array("sync_file_generate") as $tmp_table)
					{
						$this->website_project->sync_table_update_file(
							$tmp_table,
							"MEDIA" .
							DIRECTORY_SEPARATOR .
							$user_listbox_choice,
							"MEDIA" .
							DIRECTORY_SEPARATOR .
							$user_input);
					}							
					
					// Refresh related controls that display the MEDIA files.
					$this->refresh_related_controls( "MEDIA" );
					break;
				}
			}
		}
	}
	
	
	public function on_click_wxbutton_deletemediafile()
	{
		/* This function deletes a selected MEDIA file.  Nothing more.
		
		*/
		$file_to_delete = $this->wxlistbox_mediafiles->GetString(
			$this->wxlistbox_mediafiles->GetSelection()
		);
		
		$this->website_project->delete_file( "MEDIA", $file_to_delete );
		
		foreach(array("sync_file_generate") as $tmp_table)
		{
			$this->website_project->sync_table_delete_file(
				$tmp_table,
				"MEDIA" .
				DIRECTORY_SEPARATOR .
				$file_to_delete);
		}				
		
		$this->refresh_related_controls( "MEDIA" );
	}
	
	
	private function refresh_related_controls( $filetype )
	{
		$this->wxlistbox_mediafiles->Set(
			$this->website_project->get_file_names_of_dir( $filetype )
		);
		$this->parent->wxlistbox_mainframe_media_files_reload();
	}	
}
?>
