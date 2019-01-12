<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxManageTemplatesDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the class that shows a dialog through which the user
	can add, rename and delete templates.

*******************************************************************************/

// Include required .php modules.
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
	"error_messages.php"
);


class wxManageTemplatesDialog extends wxDialog
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
		
		
		//
		// Add controls to the dialog.
		//
		// Panel.
		$this->wxpanel = new wxPanel(
			$this,
			DILL2_WXID_WXMANAGETEMPLATESDIALOG_PANEL
		);
		// wxBoxSizers.
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
		
		// Left side.
		// wxListBox.
		$this->wxlistbox_templates = new wxListBox(
			$this->wxpanel,
			DILL2_WXID_WXMANAGETEMPLATESDIALOG_WXLISTBOX_TEMPLATES,
			wxDefaultPosition,
			wxDefaultSize,
			$this->website_project->get_template_names()
		);
		$this->wxboxsizer_vertical_child_left->Add(
			$this->wxlistbox_templates,
			1,
			wxEXPAND
		);
		
		// Right side.
		// Buttons.
		$this->wxbutton_addtemplate = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGETEMPLATESDIALOG_WXBUTTON_ADDTEMPLATE,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_ADDTEMPLATE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_addtemplate,
			1,
			wxEXPAND
		);
		$this->wxbutton_renametemplate = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_renametemplate,
			1,
			wxEXPAND
		);		
		$this->wxbutton_deletetemplate = new wxButton(
			$this->wxpanel,
			DILL2_WXID_WXMANAGETEMPLATESDIALOG_WXBUTTON_DELETETEMPLATE,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_DELETETEMPLATE
		);
		$this->wxboxsizer_vertical_child_right->Add(
			$this->wxbutton_deletetemplate,
			1,
			wxEXPAND
		);
		
		// Fit all controls in the dialog.
		$this->wxpanel->SetSizer( $this->wxboxsizer_horizontal_parent );
		$this->wxboxsizer_horizontal_parent->SetSizeHints( $this );
		
		
		// Bind events.
		$this->connect(
			DILL2_WXID_WXMANAGETEMPLATESDIALOG_WXBUTTON_ADDTEMPLATE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_wxbutton_addtemplate"
			)
		);
		$this->connect(
			DILL2_WXID_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_wxbutton_renametemplate"
			)
		);
		$this->connect(
			DILL2_WXID_WXMANAGETEMPLATESDIALOG_WXBUTTON_DELETETEMPLATE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_wxbutton_deletetemplate"
			)
		);
	}
	
	
	public function run()
	{
		parent::ShowModal();
	}
	
	
	public function on_wxbutton_addtemplate()
	{
		/* Shows a dialog to add a new template and adds the new template if the
		name was valid.
		
		*/
		// Will hold the text wich the user has entered inside the textbox.
		$user_input = "";
		
		/* Our dialog so the user can enter a name for the new template to
		create. */
		$wxdialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_ADDTEMPLATE_DIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_ADDTEMPLATE_DIALOG_CAPTION
		);
		
		/* Represents a dialog that displays an error message if an error has
		occured. */
		$wxdialog_error = NULL;
		
		while( $wxdialog->ShowModal() == wxID_OK )
		{
			$user_input = $wxdialog->GetValue();
			
			/* The name for the template which the user has entered must not
			already be taken. */
			if( !$this->website_project->db_value_exists(
				$user_input,
				SQLITE3_TEXT,
				DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_COLUMN_TEMPLATENAME,
				DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_NAME
			) )
			{
				// Create the new template.
				if( !$this->website_project->db_insert(
					DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_NAME,
					array( "name", "content" ),
					array( $user_input, "" ),
					array( SQLITE3_TEXT, SQLITE3_TEXT )
				) )
				{
					// The new template could not be created.
					$wxdialog_error = new wxMessageDialog(
						$wxdialog,
						DILL2_ERROR_MSG_DB_CREATE_TEMPLATE,
						DILL2_TEXT_DIALOG_ERROR_CAPTION,
						wxOK | wxCENTRE | wxICON_ERROR
					);
					$wxdialog_error->ShowModal();					
					continue;
					
				}
				else
				{
					/* A template has been added, so we need to show the newly
					created template in the wxListBox aswell. */
					$this->refresh_related_controls();
				}
				break;
			}
			else
			{
				/* Don't create a new template and inform the user that a
				template with the same name already exists. */
				$wxdialog_error = new wxMessageDialog(
					$wxdialog,
					DILL2_ERROR_MSG_DB_TEMPLATE_EXISTS,
					DILL2_TEXT_DIALOG_ERROR_CAPTION,
					wxOK | wxCENTRE | wxICON_ERROR
				);
				$wxdialog_error->ShowModal();
				continue;
			}
			break;
		}
	}
	
	
	public function on_wxbutton_renametemplate()
	{
		/* Shows the user a dialog with the name of the chosen template so that
		he can change the name of the template.  Another template with the same
		name must not yet exists, or the template won't be renamed.
		
		*/
		
		// Will hold the user input.
		$user_selection = $this->wxlistbox_templates->GetSelection();
		if( $user_selection == wxNOT_FOUND ) return;
		
		$user_choice = $this->wxlistbox_templates->GetString(
			$user_selection
		);
		
		
		$wxdialog = new wxTextEntryDialog(
			$this,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE_DIALOG_MESSAGE,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE_DIALOG_CAPTION,
			$user_choice
		);
		
		while( $wxdialog->ShowModal() == wxID_OK )
		{
			$user_input = $wxdialog->GetValue();
			// A template with the same name must not already exist.
			if( !$this->website_project->db_value_exists(
				$user_input,
				SQLITE3_TEXT,
				DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_COLUMN_TEMPLATENAME,
				DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_NAME
			) )
			{
				// Rename the template.
				$this->website_project->db_update(
					DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_NAME,
					array(
						"name"
					),
					array(
						$user_input,
					),
					array(
						SQLITE3_TEXT
					),
					array(
						"name",
						"=",
						$user_choice,
						SQLITE3_TEXT
					)
				);
				/* A template has been renamed, so we need to show the newly
				renamed template in the wxListBox aswell. */
				$this->refresh_related_controls();		
				break;
			}
			else
			{
				/* Don't rename the template and inform the user that a
				template with the same name already exists. */
				$wxdialog_error = new wxMessageDialog(
					$wxdialog,
					DILL2_ERROR_MSG_DB_TEMPLATE_EXISTS,
					DILL2_TEXT_DIALOG_ERROR_CAPTION,
					wxOK | wxCENTRE | wxICON_ERROR
				);
				$wxdialog_error->ShowModal();
				continue;
			}
		}
	}
	
	
	public function on_wxbutton_deletetemplate()
	{
		/* Removes the template which has been selected in the wxListBox widget.
		
		*/
		// Which template does the user wish to delete ?
		$user_selection = $this->wxlistbox_templates->GetSelection();
		if( $user_selection == wxNOT_FOUND ) return;
		
		$user_choice = $this->wxlistbox_templates->GetString(
			$user_selection
		);
		
		
		$result = $this->website_project->db_delete(
			DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_NAME,
			array(
				DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_COLUMN_TEMPLATENAME,
				"=",
				$user_choice,
				SQLITE3_TEXT
			)
		);
		
		if( $result )
		{
			$this->refresh_related_controls();
		}
		else
		{
			// The template could not be deleted due to an error.
			$wxdialog_error = new wxMessageDialog(
				$wxdialog,
				"The selected template could not be deleted.",
				DILL2_TEXT_DIALOG_ERROR_CAPTION,
				wxOK | wxCENTRE | wxICON_ERROR
			);
			$wxdialog_error->ShowModal();			
		}
	}
	
	private function refresh_related_controls()
	{
		$template_names = $this->website_project->get_template_names();
		$this->wxlistbox_templates->Set( $template_names );
		$this->parent->wxlistbox_mainframe_templates_reload( $template_names );
	}
}

?>
