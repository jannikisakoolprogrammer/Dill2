<?php

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


class wxManagePHPFilesDialog extends wxDialog
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
			DILL2_WXID_WXMANAGEPHPFILESDIALOG_WXPANEL
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
		$this->wxlistbox_phpfiles = new wxListBox(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEPHPFILESDIALOG_WXLISTBOX_PHPFILES,
			wxDefaultPosition,
			wxDefaultSize,
			array()
		);
		$this->refresh_related_controls( "PHP" );
		$this->wxboxsizer_vertical_child_left->Add(
			$this->wxlistbox_phpfiles,
			1,
			wxEXPAND
		);
		
		// wxButtons.
		$this->wxbutton_addphpfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEPHPFILESDIALOG_WXBUTTON_ADDPHPFILE,
			DILL2_TEXT_WXMANAGEPHPFILESDIALOG_WXBUTTON_ADDPHPFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_addphpfile,
			0,
			wxEXPAND
		);
		
		$this->wxbutton_renamephpfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEPHPFILESDIALOG_WXBUTTON_RENAMEPHPFILE,
			DILL2_TEXT_WXMANAGEPHPFILESDIALOG_WXBUTTON_RENAMEPHPFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_renamephpfile,
			0,
			wxEXPAND
		);
		
		$this->wxbutton_deletephpfile = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEPHPFILESDIALOG_WXBUTTON_DELETEPHPFILE,
			DILL2_TEXT_WXMANAGEPHPFILESDIALOG_WXBUTTON_DELETEPHPFILE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_deletephpfile,
			0,
			wxEXPAND
		);
		
		
		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel->SetSizer( $this->wxboxsizer_horizontal_parent );
		$this->wxboxsizer_horizontal_parent->SetSizeHints( $this );
		
		// Now we set up our eventhandlers.
		// Event that opens a dialog where the user can add a new PHP file.
		$this->Connect(
			DILL2_WXID_WXMANAGEPHPFILESDIALOG_WXBUTTON_ADDPHPFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_addphpfile"
			)
		);
		
		// Event that opens a dialog where the user can rename the selected php file.
		$this->Connect(
			DILL2_WXID_WXMANAGEPHPFILESDIALOG_WXBUTTON_RENAMEPHPFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_renamephpfile"
			)
		);
		
		// Event that calls a function which deletes the selected php file.
		$this->Connect(
			DILL2_WXID_WXMANAGEPHPFILESDIALOG_WXBUTTON_DELETEPHPFILE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_deletephpfile"
			)
		);
	}
	
	
	public function run()
	{
		parent::ShowModal();
	}
	
	
	public function on_click_wxbutton_addphpfile()
	{
		/* This function shows a dialog window through which the user can add
		a new php file to the currently opened project.  The user must not
		forget to append the .php extension to the name of the php file.
		A php file with the same name must not yet exist.
		
		*/
		
		// First we need to create the dialog.
		$dialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGEPHPFILESDIALOG_WXBUTTON_ADDPHPFILE_WXDIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGEPHPFILESDIALOG_WXBUTTON_ADDPHPFILE_WXDIALOG_CAPTION
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
				// Check if filename is valid.
				if ($this->website_project->is_valid_filename($user_input) == FALSE)
				{
					/* Show a dialog telling the user that the filename is invalid. */
					$wxmessagedialog = new wxMessageDialog(
						$dialog,
						"Error:  Invalid filename.  Only printable ASCII characters allowed.",
						DILL2_TEXT_DIALOG_ERROR_CAPTION
					);
					$wxmessagedialog->ShowModal();
					continue;				
				}	
				
				// Verify that a file with the same name does not yet exist.
				if( $this->website_project->exists_file( "PHP", $user_input ) )
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
					/* Create the PHP file and refresh the wxListBox of the parent
					dialog. */
					$this->website_project->create_file( "PHP", $user_input );
					
					foreach(array("sync_file_generate") as $tmp_table)
					{
						$this->website_project->sync_table_add_file(
							$tmp_table,
							"PHP" .
							DIRECTORY_SEPARATOR .
							$user_input);					
					}						
					
					/* Refresh the wxListBox that shows the PHP files and quit
					this function. */
					$this->refresh_related_controls( "PHP" );
					break;
				}
			}
			
		}
	}
	
	
	public function on_click_wxbutton_renamephpfile()
	{
		/* This function shows a dialog window through which the user can rename
		an existing php file.  An existing php file with the same name must not
		yet exist.  The user must not forget to append the .php extension to the
		name of the php file.
		
		*/
		
		// Store the name of the file which the user has selected.
		$user_listbox_choice = $this->wxlistbox_phpfiles->GetString(
			$this->wxlistbox_phpfiles->GetSelection()
		);
		
		// First we need to create the dialog.
		$dialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGEPHPFILESDIALOG_WXBUTTON_RENAMEPHPFILE_WXDIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGEPHPFILESDIALOG_WXBUTTON_RENAMEPHPFILE_WXDIALOG_CAPTION
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
				// Check if filename is valid.
				if ($this->website_project->is_valid_filename($user_input) == FALSE)
				{
					/* Show a dialog telling the user that the filename is invalid. */
					$wxmessagedialog = new wxMessageDialog(
						$dialog,
						"Error:  Invalid filename.  Only printable ASCII characters allowed.",
						DILL2_TEXT_DIALOG_ERROR_CAPTION
					);
					$wxmessagedialog->ShowModal();
					continue;				
				}	
				
				/* Make sure that the user has typed in a new filename which does
				not already exist. */
				if( $this->website_project->exists_file( "PHP", $user_input ) )
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
						"PHP",
						$user_listbox_choice,
						$user_input
					);

					foreach(array("sync_file_generate") as $tmp_table)
					{
						$this->website_project->sync_table_update_file(
							$tmp_table,
							"PHP" .
							DIRECTORY_SEPARATOR .
							$user_listbox_choice,
							"PHP" .
							DIRECTORY_SEPARATOR .
							$user_input);
					}
					
					// Refresh related controls that display the PHP files.
					$this->refresh_related_controls( "PHP" );
					break;
				}
			}
		}
	}
	
	
	public function on_click_wxbutton_deletephpfile()
	{
		/* This function deletes a selected PHP file.  Nothing more.
		
		*/
		$file_to_delete = $this->wxlistbox_phpfiles->GetString(
			$this->wxlistbox_phpfiles->GetSelection()
		);
		
		$this->website_project->delete_file( "PHP", $file_to_delete );
		
		foreach(array("sync_file_generate") as $tmp_table)
		{
			$this->website_project->sync_table_delete_file(
				$tmp_table,
				"PHP" .
				DIRECTORY_SEPARATOR .
				$file_to_delete);
		}					
		
		$this->refresh_related_controls( "PHP" );
	}
	
	
	private function refresh_related_controls( $filetype )
	{
		$this->wxlistbox_phpfiles->Set(
			$this->website_project->get_file_names_of_dir( $filetype )
		);
		$this->parent->wxlistbox_mainframe_php_files_reload();
	}
}
?>
