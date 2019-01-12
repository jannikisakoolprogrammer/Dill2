<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxManageWebsiteStructureDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the class for the dialog through which the user can
	alter the structure of a website project.

*******************************************************************************/

// Include project related .php files.
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



class wxManageWebsiteStructureDialog extends wxDialog
{
	/* This class represents a dialog which is based on the wxDialog class.
	Through this dialog, the user has the possiblity to alter the website
	structure of a website project.
	On the left hand side, the user sees website structure (tree) of the
	website.  The user can click on one of these items to act upon it.  On the
	right side of the wxDialog dialog are various buttons which affect the
	website structure of the website project.
		By using the "Add" button, the user has the ablity to add a new website
	element to the website structure.  If the user hasn't chosen any element
	on the left side (the website structure tree), the new element becomes a
	root element at the very bottom of the tree.  Otherwise the element will
	become a child-element, if the user has previously selected an existing
	page element.
	  	The user can edit an existing page (rename it and change its' template) by
	first selecting an existing page and then clicking on the "Edit" button.
	Deleting a page element works much in the same way, by simply selecting an
	existing page element and clicking on the "Delete" button.
		The next two buttons, "Move element up" and "Move element down" are there
	to move an existing element up or down in its' branch.  The user simply has
	to select an existing page element and click on one of the buttons.
		The last remaining button is probably the most exciting one.  Using the
	"Move element tree" button, the user has the ability to move an entire
	website branch of a tree into another branch or anywhere else he wishes to
	move it.  First an existing element has to be selected.  Next the user clicks
	on the button and a new dialog opens.  This dialog contains the website
	structure once again, but without the website branch (and elements below it).
	
	*/
	function __construct(
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
		// A reference to the currently opened website project.
		$this->website_project = $website_project;
		
		/* A variable which contains the ID of the selected element in the
		widget wxDataViewTreeCtrl, the representation of the website structure.
		*/
		$this->wxtreectrl_selected_element_id = NULL;
		
		/* This variable will be set to TRUE if the user has clicked on the button
		to move a selected element into another element. */
		$this->mode_move_element_tree = FALSE;
		
		
		// First the dialog is created:
		$this->wxpanel = new wxPanel(
			$this,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXPANEL
		);
		
		$this->wxboxsizer_horizontal_parent = new wxBoxSizer(
			wxHORIZONTAL
		);
		
		$this->wxboxsizer_vertical_left_child = new wxBoxSizer(
			wxVERTICAL
		);
		$this->wxboxsizer_horizontal_parent->Add(
			$this->wxboxsizer_vertical_left_child,
			3,
			wxEXPAND | wxALL,
			10
		);
		
		$this->wxboxsizer_vertical_right_child = new wxBoxSizer(
			wxVERTICAL
		);
		$this->wxboxsizer_horizontal_parent->Add(
			$this->wxboxsizer_vertical_right_child,
			1,
			wxEXPAND | wxRIGHT | wxBOTTOM | wxTOP,
			10
		);		
		
		// Left side.
		// Control for the website structure.
		$this->wxtreectrl_website_structure = new wxTreeCtrl(
			$this->wxpanel,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXTREECTRL,
			wxDefaultPosition,
			wxDefaultSize
		);
		$this->wxboxsizer_vertical_left_child->Add(
			$this->wxtreectrl_website_structure,
			1,
			wxEXPAND
		);
		
		
		// Right side.
		// Button for adding a new element.
		$this->wxbutton_addelement = new wxButton(
			$this->wxpanel,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT
		);
		$this->wxboxsizer_vertical_right_child->Add(
			$this->wxbutton_addelement,
			1,
			wxEXPAND
		);
		
		// Button for renaming an existing element.
		$this->wxbutton_renameelement = new wxButton(
			$this->wxpanel,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_RENAMEELEMENT,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_RENAMEELEMENT
		);
		$this->wxbutton_renameelement->Enable( FALSE );
		$this->wxboxsizer_vertical_right_child->Add(
			$this->wxbutton_renameelement,
			1,
			wxEXPAND
		);
		// Button for deleting an existing element.
		$this->wxbutton_deleteelement = new wxButton(
			$this->wxpanel,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_DELETEELEMENT,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_DELETEELEMENT
		);
		$this->wxbutton_deleteelement->Enable( FALSE );		
		$this->wxboxsizer_vertical_right_child->Add(
			$this->wxbutton_deleteelement,
			1,
			wxEXPAND
		);
		// Button for moving an element up.
		$this->wxbutton_moveelementup = new wxButton(
			$this->wxpanel,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTUP,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTUP
		);
		$this->wxbutton_moveelementup->Enable( FALSE );		
		$this->wxboxsizer_vertical_right_child->Add(
			$this->wxbutton_moveelementup,
			1,
			wxEXPAND
		);
		// Button for moving an element down.
		$this->wxbutton_moveelementdown = new wxButton(
			$this->wxpanel,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTDOWN,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTDOWN
		);
		$this->wxbutton_moveelementdown->Enable( FALSE );		
		$this->wxboxsizer_vertical_right_child->Add(
			$this->wxbutton_moveelementdown,
			1,
			wxEXPAND
		);
			
		// Button for moving an entire element tree.
		$this->wxbutton_moveelementtree = new wxButton(
			$this->wxpanel,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTTREE,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTTREE
		);
		$this->wxbutton_moveelementtree->Enable( FALSE );		
		$this->wxboxsizer_vertical_right_child->Add(
			$this->wxbutton_moveelementtree,
			1,
			wxEXPAND
		);
		
		$this->wxpanel->SetSizer( $this->wxboxsizer_horizontal_parent );
		$this->wxboxsizer_horizontal_parent->SetSizeHints( $this );
		
		
		// And these are our eventhandlers.
		$this->Connect(
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_addelement"
			)
		);
		$this->Connect(
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_RENAMEELEMENT,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_rename_item"
			)
		);
		/* Eventhandler that calls a function to delete the selected page and its'
		children. */
		$this->Connect(
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_DELETEELEMENT,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_delete_item"
			)
		);
		/* Eventhandler that handles moving a selected item up. */
		$this->Connect(
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTUP,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_move_item_up"
			)
		);
		/* Eventhandler that handles moving a selected item down. */
		$this->Connect(
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTDOWN,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_move_item_down"
			)
		);
		/* Eventhandler that handles moving the selected item into another item.
		START */
		$this->Connect(
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTTREE,
			wxEVT_BUTTON,
			array(
				$this,
				"on_click_wxbutton_move_element_tree_start"
			)
		);
		/* Eventhandler that handles moving the selected item into another item.
		START */
		$this->Connect(
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXTREECTRL,
			wxEVT_TREE_ITEM_RIGHT_CLICK,
			array(
				$this,
				"on_click_wxbutton_move_element_tree_finish"
			)
		);
		/* Eventhandler that calls a function which activates and deactivates
		certain buttons on the right side of the dialog. */
		$this->Connect(
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXTREECTRL,
			wxEVT_TREE_SEL_CHANGED,
			array(
				$this,
				"on_left_click_wxtreectrl_managewebsitestructuredialog"
			)
		);
	}
	
	
	public function run()
	{
		/* Calling this function refreshes the website structure and shows the
		dialog to the user.
		
		*/
		$this->refresh_website_structure();
		parent::ShowModal();
	}
	
	
	private function refresh_website_structure( $branch_to_ignore = NULL )
	{
		/* Calling this function refreshes the website structure that is shown
		to the user.
		
		 */
		$this->wxtreectrl_website_structure->DeleteAllItems();
		$wxtreeitemdata = new wxTreeItemData();
		$wxtreeitemdata->element_id = -1;
		$this->wxtreectrl_website_structure_root = $this->wxtreectrl_website_structure->AddRoot(
			"ROOT",
			-1,
			-1,
			$wxtreeitemdata
		);
		$this->wxtreectrl_website_structure->SelectItem(
			$this->wxtreectrl_website_structure_root
		);	
		$this->update_website_structure(
			$this->website_project->get_website_structure(),
			NULL,
			TRUE,
			$branch_to_ignore
		);
		$this->wxtreectrl_website_structure->Expand(
			$this->wxtreectrl_website_structure_root
		);
		// The user is only able to add an element to the end of the parent branch.
		$this->wxbutton_addelement->Enable( TRUE );
		$this->wxbutton_renameelement->Enable( FALSE );
		$this->wxbutton_deleteelement->Enable( FALSE );
		$this->wxbutton_moveelementup->Enable( FALSE );
		$this->wxbutton_moveelementdown->Enable( FALSE );
		$this->wxbutton_moveelementtree->Enable( FALSE );
	}
	
	
	public function on_click_wxbutton_addelement()
	{
		/* This function shows a dialog through which the user can add a new
		page element to the website structure tree.  The user also has the
		ability to choose an existing template to use as a base for the page.
		
		*/
		// Store what the user has selected.
		$user_selection = $this->wxtreectrl_website_structure->GetSelection();
		
		// And now the dialog is created.
		$wxdialog = new wxDialog(
			$this,
			wxID_ANY,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT_WXDIALOG_CAPTION
		);
		$wxpanel = new wxPanel(
			$wxdialog
		);
		$wxvbox = new wxBoxSizer(
			wxVERTICAL
		);
		$wxhbox_page_name = new wxBoxSizer(
			wxHORIZONTAL
		);
		$wxhbox_template_name = new wxBoxSizer(
			wxHORIZONTAL
		);
		$wxhbox_okcancel_buttons = new wxBoxSizer(
			wxHORIZONTAL
		);
		$wxstatictext_page_name = new wxStaticText(
			$wxpanel,
			wxID_ANY,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT_WXDIALOG_MESSAGE
		);
		$wxtextctrl_page_name = new wxTextCtrl(
			$wxpanel,
			wxID_ANY,
			""
		);
		$wxstatictext_template_name = new wxStaticText(
			$wxpanel,
			wxID_ANY,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT_WXDIALOG_LABEL_TEMPLATE
		);
		$wxlistbox_template_name = new wxListBox(
			$wxpanel,
			wxID_ANY,
			wxDefaultPosition,
			wxDefaultSize,
			$this->website_project->get_template_names(),
			wxLB_SINGLE
		);
		$wxbutton_canceldialog = new wxButton(
			$wxpanel,
			wxID_CANCEL,
			"Cancel"
		);
		$wxbutton_okdialog = new wxButton(
			$wxpanel,
			wxID_OK,
			"Ok"
		);
		$wxhbox_page_name->Add(
			$wxstatictext_page_name
		);
		$wxhbox_page_name->Add(
			$wxtextctrl_page_name
		);
		$wxhbox_template_name->Add(
			$wxstatictext_template_name
		);
		$wxhbox_template_name->Add(
			$wxlistbox_template_name
		);
		$wxhbox_okcancel_buttons->Add(
			$wxbutton_canceldialog
		);
		$wxhbox_okcancel_buttons->Add(
			$wxbutton_okdialog
		);
		$wxvbox->Add(
			$wxhbox_page_name
		);
		$wxvbox->Add(
			$wxhbox_template_name
		);
		$wxvbox->Add(
			$wxhbox_okcancel_buttons
		);
        // Distribute / layout the controls in the mainframe.
        $wxpanel->SetSizer( $wxvbox );
	    $wxvbox->SetSizeHints( $wxdialog );

		// And now show the dialog to the user.
		while( $wxdialog->ShowModal() == wxID_OK )
		{
			/* The name of the page which the user might have selected is stored
			in here. */
			$user_input = $wxtextctrl_page_name->GetValue();
			$wxtreeitemdata = $this->wxtreectrl_website_structure->GetItemData(
				$user_selection
			);
			if( $wxtreeitemdata->element_id == -1 )
			{
				/* Routine for adding a new parent element to the end of the
				branch. */
				
				/* First let's make sure that a parent element with the same
				name as the one the user has typed in does not yet exist.
				Otherwise creating the page element will not work. */
				$results = $this->website_project->db_select(
					"page",
					"*",
					array(
						"parent_id",
						"IS",
						NULL,
						SQLITE3_INTEGER
					)
				);
				$results = array_filter( $results, function( $element )
				{
					return $element["parent_id"] == NULL ? true : false;
				});
				
				$results_name = array_column( $results, "name" );
				if( !in_array( $user_input, $results_name ) )
				{
					// The parent page with the name the user requested can be created.
					// We need the ID of the template which the page is going to use.
					$templateid = NULL;
					$chosen_template = $this->website_project->db_select(
						"template",
						array( "id" ),
						array(
							"name",
							"=",
							$wxlistbox_template_name->GetString(
								$wxlistbox_template_name->GetSelection()
							),
							SQLITE3_TEXT
						)
					);
					$template_id = $chosen_template[0]["id"];
						
					/* The new page element gets the highest sort-id, because it
					should be appended to the very end of the parent branch. */
					$sort_id = count( $results ) + 1;
					
					// And now create the new page element finally.
					$this->website_project->db_insert(
						"page",
						array(
							"name",
							"content",
							"sort_id",
							"template_id"
						),
						array(
							$user_input,
							"",
							$sort_id,
							$template_id
						),
						array(
							SQLITE3_TEXT,
							SQLITE3_TEXT,
							SQLITE3_INTEGER,
							SQLITE3_INTEGER
						)
					);
					
					/* The page element has been created sucessfully.  Now let's
					show the changes to the user by refreshing the existing
					website structure. */
					$this->refresh_related_controls();
				}
			}
			else
			{
				/* Routine for adding a new child element of a parent to the end
				of the child-branch. */
				/* First, we need to know the "id" of the parent element so that
				we know to which branch the to-be-created element will belong to.
				*/
				$wxclientdata = $this->wxtreectrl_website_structure->GetItemData( $user_selection );
				
				/* Also, we need to make sure that a page element with the same
				name as the user has typed into the dialog in the current child-branch
				does not yet exist.  Otherwise creating the page will simply fail.
				*/
				$results = $this->website_project->db_select(
					"page",
					array( "name" ),
					array(
						"parent_id",
						"=",
						$wxclientdata->element_id,
						SQLITE3_INTEGER
					)
				);
				
				if( !in_array( $user_input, $results ) )
				{
					// The page can be created.
					// We need the ID of the template which the page should use.
					$templateid = NULL;
					$chosen_template = $this->website_project->db_select(
						"template",
						array( "id" ),
						array(
							"name",
							"=",
							$wxlistbox_template_name->GetString(
								$wxlistbox_template_name->GetSelection()
							),
							SQLITE3_TEXT
						)
					);
					$template_id = $chosen_template[0]["id"];
									
					/* Again, the new page element will be appended to the very
					end of the child-branch. */
					$sort_id = count( $results ) + 1;
					// Finally create the new page-element.					
					$this->website_project->db_insert(
						"page",
						array(
							"name",
							"content",
							"sort_id",
							"template_id",
							"parent_id"
						),
						array(
							$user_input,
							"",
							$sort_id,
							$template_id,
							$wxclientdata->element_id
						),
						array(
							SQLITE3_TEXT,
							SQLITE3_TEXT,
							SQLITE3_INTEGER,
							SQLITE3_INTEGER,
							SQLITE3_INTEGER
						)
					);
					
					/* The last task is to refresh the website structure in the
					dialog, so that the user can see the effects immediately. */
					$this->refresh_related_controls();
				}
			}
			break;
		}
	}
	
	
	public function update_website_structure( $treedata, $root = NULL, $is_root = TRUE, $branch_to_ignore_id = NULL )
	{
		/* This function simply updates the website structure that is shown
		to the user.  This function is called recursively in order to build the
		website structure tree. */
		foreach( $treedata as $key => $value )
		{
			if( $branch_to_ignore_id == $value["self"]["id"] )
			{
				continue;
			}
			/* I create a new wxClientData instance for each page-element in the
			website structure, because I need to know which item represents
			which record in the table 'page'. */
			$wxtreeitemdata = new wxTreeItemData();
			$wxtreeitemdata->element_id = $value["self"]["id"];
			$wxtreeitemdata->element_parent_id = $value["self"]["parent_id"];
			$wxtreeitemdata->element_sort_id = $value["self"]["sort_id"];
			$wxtreeitemdata->element_name = $value["self"]["name"];
			
			// Will only be called in the lowest "level" (no recursion).
			if( $is_root )
			{
				// Append a root-element.
				$wxtreeitemid = $this->wxtreectrl_website_structure->AppendItem(
					$this->wxtreectrl_website_structure_root,
					$value["self"]["name"],
					-1,
					-1,
					$wxtreeitemdata
				);
			}
			else
			{
				// Append a child-element.  Called only during recursion.
				$wxtreeitemid = $this->wxtreectrl_website_structure->AppendItem(
					$root,
					$value["self"]["name"],
					-1,
					-1,
					$wxtreeitemdata
				);
			}
			// Go deeper in the tree structure if necessary.
			$this->update_website_structure( $value["children"], $wxtreeitemid, FALSE, $branch_to_ignore_id );
			$this->wxtreectrl_website_structure->Expand( $wxtreeitemid );
		}
	}
	
	
	public function on_click_wxbutton_rename_item()
	{
		/* Opens another dialog where the user can rename the selected item and
		optionally change its template to another template.
		
		*/
		
		// First we need to know the "id" and "name" of the selected item.
		$selected_item_data = $this->wxtreectrl_website_structure->GetItemData(
			$this->wxtreectrl_website_structure->GetSelection()
		);
		$selected_item_name = $this->wxtreectrl_website_structure->GetItemtext(
			$this->wxtreectrl_website_structure->GetSelection()
		);
		$selected_item_id = $selected_item_data->element_id;
		
		/* Now we need to find out whether the user has selected a parent element
		or not.  This is required to check for any naming collisions before
		trying to rename an existing page-element. */
		$selected_page_element = $this->website_project->db_select(
			"page",
			"*",
			array(
				"id",
				"=",
				$selected_item_id,
				SQLITE3_INTEGER
			)
		);
		if( !$selected_page_element[0]["parent_id"] )
		{
			// A parent-page-element has been chosen.
			$siblings = $this->website_project->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"IS",
					NULL,
					SQLITE3_INTEGER
				)
			);			
		}
		else
		{
			// A child-page-element has been chosen.
			$siblings = $this->website_project->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"=",
					$selected_page_element[0]["parent_id"],
					SQLITE3_INTEGER
				)
			);			
		}
		
		// Now we need to create a dialog.
		$wxdialog = new wxDialog(
			$this,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXDIALOG_RENAMEELEMENT,
			DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_RENAMEELEMENT_WXDIALOG_CAPTION
		);
		$wxpanel = new wxPanel(
			$wxdialog
		);
		$wxstatictext_page_name = new wxStaticText(
			$wxpanel,
			wxID_ANY,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE_DIALOG_LABEL_RENAMETEMPLATE
		);
		$wxtextctrl_page_name = new wxTextCtrl(
			$wxpanel,
			wxID_ANY,
			$selected_item_name
		);
		$wxstatictext_template_name = new wxStaticText(
			$wxpanel,
			wxID_ANY,
			DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE_DIALOG_LABEL_CHOOSETEMPLATE
		);
		$wxlistbox_template_name = new wxListBox(
			$wxpanel,
			DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXDIALOG_RENAMEELEMENT_WXLISTBOX_TEMPLATE_NAME,
			wxDefaultPosition,
			wxDefaultSize,
			$this->website_project->get_template_names(),
			wxLB_SINGLE
		);
		$wxbutton_canceldialog = new wxButton(
			$wxpanel,
			wxID_CANCEL,
			"Cancel"
		);
		$wxbutton_okdialog = new wxButton(
			$wxpanel,
			wxID_OK,
			"Ok"
		);
		$wxvbox = new wxBoxSizer(
			wxVERTICAL
		);
		$wxhbox_page_name = new wxBoxSizer(
			wxHORIZONTAL
		);
		$wxhbox_template_name = new wxBoxSizer(
			wxHORIZONTAL
		);
		$wxhbox_cancelok_buttons = new wxBoxSizer(
			wxHORIZONTAL
		);
		$wxhbox_page_name->Add(
			$wxstatictext_page_name
		);
		$wxhbox_page_name->Add(
			$wxtextctrl_page_name
		);
		$wxhbox_template_name->Add(
			$wxstatictext_template_name
		);
		$wxhbox_template_name->Add(
			$wxlistbox_template_name
		);
		$wxhbox_cancelok_buttons->Add(
			$wxbutton_canceldialog
		);
		$wxhbox_cancelok_buttons->Add(
			$wxbutton_okdialog
		);
		$wxvbox->Add(
			$wxhbox_page_name
		);
		$wxvbox->Add(
			$wxhbox_template_name
		);
		$wxvbox->Add(
			$wxhbox_cancelok_buttons		
		);
        /* Distribute / layout the controls in the mainframe so its' all nicely
        alligned. */
        $wxpanel->SetSizer( $wxvbox );
	    $wxvbox->SetSizeHints( $wxdialog );
		
		// Now show the created wxDialog to the user.
		while( $wxdialog->ShowModal() == wxID_OK )
		{
			/* Make sure that the user has typed in a valid page name and
			that he has selected a template. */
			$user_input_pagename = $wxtextctrl_page_name->GetValue();
			if( strlen( trim( $user_input_pagename ) ) == 0 )
			{
				break;
			}
						
			if( $wxlistbox_template_name->GetSelection() != wxNOT_FOUND )
			{
				$user_input_template = $wxlistbox_template_name->GetString(
					$wxlistbox_template_name->GetSelection()
				);
			}

			// A page with the same name must not yet exist in the current branch.
			if( $this->pagename_exists( $siblings, $user_input_pagename ) &&
				!isset( $user_input_template ) )
			{
				/* Display a wxDialog to the user which states that a page
				with the chosen name already exists in the current page.  It
				should urge the user to choose another name. */
				$wxdialog_error = new wxMessageDialog(
					$wxdialog,
					sprintf(
						"A page with the name '%s' already exists in the current" .
						"branch.  Please choose another name.",
						$user_input_pagename
					),
					DILL2_TEXT_DIALOG_ERROR_CAPTION
				);
				$wxdialog_error->ShowModal();
				continue;
			}
			
			// Rename the page and optionally change the template.
			if( isset( $user_input_template ))
			{
				// First, we need to get the ID of the template.
				$tmp_template = $this->website_project->db_select(
					"template",
				 	array(
				 		"id"
				 	),
					array(
						"name",
						"=",
						$user_input_template,
						SQLITE3_TEXT
					)
				);
				$this->website_project->db_update(
					"page",
					array(
						"name",
						"template_id"
					),
					array(
						$user_input_pagename,
						$tmp_template[0]["id"]
					),
					array(
						SQLITE3_TEXT,
						SQLITE3_INTEGER
					),
					array(
						"id",
						"=",
						$selected_item_id,
						SQLITE3_INTEGER
					)
				);
				/* The last task is to refresh the website structure in the
				dialog, so that the user can see the effects immediately. */
				$this->refresh_related_controls();
				break;				
			}
			else
			{
				$this->website_project->db_update(
					"page",
					array(
						"name"
					),
					array(
						$user_input_pagename
					),
					array(
						SQLITE3_TEXT
					),
					array(
						"id",
						"=",
						$selected_item_id,
						SQLITE3_INTEGER
					)
				);
				/* The last task is to refresh the website structure in the
				dialog, so that the user can see the effects immediately. */
				$this->refresh_related_controls();
				break;
			}
		}
	}
	
	
	private function pagename_exists( $pages, $name )
	{
		// A page with the same name must not yet exist in the current branch.
		foreach( $pages as $page )
		{
			if( $page["name"] == $name )
			{
				return TRUE;
			}
		}
		return FALSE;
	}
	
	
	public function on_click_wxbutton_delete_item()
	{
		/* This function deletes the record which the selected element in the
		"wxDataViewTreeCtrl" represents and, of course, its' children records.
		
		*/
		$selected_page_name = NULL;
		$selected_item_data = NULL;
		
		$selected_item_data = $this->wxtreectrl_website_structure->GetItemData(
			$this->wxtreectrl_website_structure->GetSelection()
		);
		$selected_page_name = $this->wxtreectrl_website_structure->GetItemText(
			$this->wxtreectrl_website_structure->GetSelection()
		);
		
		$this->website_project->db_delete(
			"page",
			array(
				"name",
				"=",
				$selected_page_name,
				SQLITE3_TEXT
			)
		);
		
		/* An element has been deleted.  If the current child branch, where the
		element was contained, contains any more items then assign a new sort id
		to each of them. */
		if( $selected_item_data->element_parent_id == NULL )
		{
			$this->website_project->website_structure_branch_assign_new_sort_ids();
		}
		else
		{
			$this->website_project->website_structure_branch_assign_new_sort_ids( $selected_item_data->element_sort_id );
		}
		
		/* The last task is to refresh the website structure in the
		dialog, so that the user can see the effects immediately. */
		$this->refresh_related_controls();
	}
	
	
	public function on_click_wxdataviewtreectrl_managewebsitestructuredialog( $wxdataviewevent )
	{
		/* This function is called if the user selects or unselects an item
		inside the wxDataViewTreeCtrl widget.
		It acitvates or deactivates certain buttons, depending on which element
		inside the wxWidget has been selected.
		
		*/
		// The ID of the element that has been selected.
		$selected_element_id = NULL;
		$selected_element_client_data = NULL;		
		
		
		$wxdataviewitem = $wxdataviewevent->GetItem();
		if( !$wxdataviewitem->IsOk())
		{
			return;
		}

		$selected_element_client_data = $this->wxdataviewtreectrl_website_structure->GetItemData(
			$wxdataviewitem
		);
		
		$selected_element_id = $selected_element_client_data->element_id;
		
		
		if( $selected_element_id == $this->wxdataviewtreectrl_selected_element_id )
		{
			/* The user has right-clicked on a previously selected element.
			This means to unselected the current element, so that the user has
			the possiblity to add an element in the top-level of the website
			structure hierarchy.
			*/
			$this->wxdataviewtreectrl_website_structure->Unselect(
				$wxdataviewitem
			);
			$this->wxdataviewtreectrl_selected_element_id = NULL;
			return;
		}
		else
		{
			/* The user has right-clicked on an element, which has not been
			selected previously. */
			$this->wxdataviewtreectrl_selected_element_id = $selected_element_id;
		}
	}
	
	
	public function on_left_click_wxtreectrl_managewebsitestructuredialog( $wxtreeevent )
	{
		// The user is about to move a selected element into another element.
		if( $this->mode_move_element_tree )
		{
			return;
		}
			
		// The ID of the element that has been selected.
		$selected_element_id = NULL;
		$selected_element_client_data = NULL;		
		
		
		$wxtreeitemid = $wxtreeevent->GetItem();
		if( !$wxtreeitemid->IsOk())
		{
			return;
		}

		$selected_element_client_data = $this->wxtreectrl_website_structure->GetItemData(
			$wxtreeitemid
		);
		
		$selected_element_id = $selected_element_client_data->element_id;
		$this->wxtreectrl_selected_element_id = $selected_element_id;
		
		
		// The user can't change the root item in any way.
		if( $this->wxtreectrl_selected_element_id == -1 )
		{
			// The user is only able to add an element to the end of the parent branch.
			$this->wxbutton_addelement->Enable( TRUE );
			$this->wxbutton_renameelement->Enable( FALSE );
			$this->wxbutton_deleteelement->Enable( FALSE );
			$this->wxbutton_moveelementup->Enable( FALSE );
			$this->wxbutton_moveelementdown->Enable( FALSE );
			$this->wxbutton_moveelementtree->Enable( FALSE );
			return;
		}
		else
		{
			/* The user has selected an element.  This means that the element
			can be renamed, deleted and moved into another branch. */
			$this->wxbutton_renameelement->Enable( TRUE );
			$this->wxbutton_deleteelement->Enable( TRUE );
			$this->wxbutton_moveelementtree->Enable( TRUE );
			
			/* If the element is the only element in the current branch, it can't be
			moved up or down. */
			if( $selected_element_client_data->element_parent_id == NULL )
			{
				$elements_in_current_branch = $this->website_project->db_select(
					"page",
					"*",
					array(
						"parent_id",
						"IS",
						NULL,
						SQLITE3_INTEGER
					)
				);			
			}
			else
			{
				$elements_in_current_branch = $this->website_project->db_select(
					"page",
					"*",
					array(
						"parent_id",
						"=",
						$selected_element_client_data->element_parent_id,
						SQLITE3_INTEGER
					)
				);			
			}

			if( count( $elements_in_current_branch ) > 1 )
			{
				// We now know that the item can be moved.
				if( $selected_element_client_data->element_sort_id < count( $elements_in_current_branch ) )
				{
					// The item can be moved down.
					$this->wxbutton_moveelementdown->Enable( TRUE );
				}
				else
				{
					$this->wxbutton_moveelementdown->Enable( FALSE );
				}
				if( $selected_element_client_data->element_sort_id > 1 )
				{
					// The item can be moved up.
					$this->wxbutton_moveelementup->Enable( TRUE );
				}
				else
				{
					$this->wxbutton_moveelementup->Enable( FALSE );
				}
			}
		}
	}
	
	
	public function on_click_wxbutton_move_item_up()
	{
		/* This function sets a new sort id on the currently selected element
		( sort_id - 1) which means that the selected item will be moved up in
		the current branch.
		
		*/
		
		// Move the element above the selected element down:
		// Let's get hold of the element first.
		
		// Move the selected element up:
		$selected_element_client_data = $this->wxtreectrl_website_structure->GetItemData(
			$this->wxtreectrl_website_structure->GetSelection()
		);
					
		$prev_sibling = $this->wxtreectrl_website_structure->GetPrevSibling(
			$this->wxtreectrl_website_structure->GetSelection()
		);

		if( $prev_sibling->IsOk())
		{
			$prev_sibling_client_data = $this->wxtreectrl_website_structure->GetItemData(
				$prev_sibling
			);
			// Now move the previous sibling down.
			$this->website_project->db_update(
				"page",
				array(
					"sort_id"
				),
				array(
					$prev_sibling_client_data->element_sort_id + 1
				),
				array(
					SQLITE3_INTEGER
				),
				array(
					"id",
					"=",
					$prev_sibling_client_data->element_id,
					SQLITE3_INTEGER
				)
			);
					
						
			$this->website_project->db_update(
				"page",
				array(
					"sort_id"
				),
				array(
					$selected_element_client_data->element_sort_id - 1
				),
				array(
					SQLITE3_INTEGER
				),
				array(
					"id",
					"=",
					$selected_element_client_data->element_id,
					SQLITE3_INTEGER
				)
			);

			/* The last task is to refresh the website structure in the
			dialog, so that the user can see the effects immediately. */
			$this->refresh_related_controls();
			
			/* And of course to disable almost all buttons since the root-element
			will be selected by default again. */
			// The user is only able to add an element to the end of the parent branch.
			$this->wxbutton_addelement->Enable( TRUE );
			$this->wxbutton_renameelement->Enable( FALSE );
			$this->wxbutton_deleteelement->Enable( FALSE );
			$this->wxbutton_moveelementup->Enable( FALSE );
			$this->wxbutton_moveelementdown->Enable( FALSE );
			$this->wxbutton_moveelementtree->Enable( FALSE );			
		}
	}
	
	
	public function on_click_wxbutton_move_item_down()
	{
		/* This function sets a new sort id on the currently selected element
		( sort_id + 1) which means that the selected item will be moved down in
		the current branch.
		
		*/
		
		// Move the element below the selected element up:
		// Let's get hold of the element first.
		$next_sibling = $this->wxtreectrl_website_structure->GetNextSibling(
			$this->wxtreectrl_website_structure->GetSelection()
		);
		if( $next_sibling->IsOk())
		{
			$next_sibling_client_data = $this->wxtreectrl_website_structure->GetItemData(
				$next_sibling
			);
			
			// Now move the previous sibling up.
			$this->website_project->db_update(
				"page",
				array(
					"sort_id"
				),
				array(
					$next_sibling_client_data->element_sort_id - 1
				),
				array(
					SQLITE3_INTEGER
				),
				array(
					"id",
					"=",
					$next_sibling_client_data->element_id,
					SQLITE3_INTEGER
				)
			);
		
			// Move the selected element down:
			$selected_element_client_data = $this->wxtreectrl_website_structure->GetItemData(
				$this->wxtreectrl_website_structure->GetSelection()
			);
						
			$this->website_project->db_update(
				"page",
				array(
					"sort_id"
				),
				array(
					$selected_element_client_data->element_sort_id + 1
				),
				array(
					SQLITE3_INTEGER
				),
				array(
					"id",
					"=",
					$this->wxtreectrl_selected_element_id,
					SQLITE3_INTEGER
				)
			);

			/* The last task is to refresh the website structure in the
			dialog, so that the user can see the effects immediately. */
			$this->refresh_related_controls();
			
			/* And of course to disable almost all buttons since the root-element
			will be selected by default again. */
			// The user is only able to add an element to the end of the parent branch.
			$this->wxbutton_addelement->Enable( TRUE );
			$this->wxbutton_renameelement->Enable( FALSE );
			$this->wxbutton_deleteelement->Enable( FALSE );
			$this->wxbutton_moveelementup->Enable( FALSE );
			$this->wxbutton_moveelementdown->Enable( FALSE );
			$this->wxbutton_moveelementtree->Enable( FALSE );			
		}
	}
	
	
	public function on_click_wxbutton_move_element_tree_start()
	{
		/* This function refreshes the website structure, but without the entire
		branch which the currently selected item contains, including the item
		itself.
		
		*/
		$selected_item_array = $this->website_project->db_select(
			"page",
			"*",
			array(
				"id",
				"=",
				$this->wxtreectrl_selected_element_id,
				SQLITE3_INTEGER
			)
		);
		$selected_element = $selected_item_array[0];
				
		$this->mode_move_element_tree = TRUE;
		$this->refresh_website_structure(
			$selected_element["id"]
		);

		// Deactivate all buttons until the user has finally relocated the selected item.		
		$this->wxbutton_addelement->Enable( FALSE );
		$this->wxbutton_renameelement->Enable( FALSE );
		$this->wxbutton_deleteelement->Enable( FALSE );
		$this->wxbutton_moveelementup->Enable( FALSE );
		$this->wxbutton_moveelementdown->Enable( FALSE );
		$this->wxbutton_moveelementtree->Enable( FALSE );		
	
	}
	
	
	public function on_click_wxbutton_move_element_tree_finish( $wxtreeevent )
	{
		/* This function moves the previously selected item into the item that
		has been selected in this second step.
		
		*/
		
		$db_selected_item_array = $this->website_project->db_select(
			"page",
			array(
				"parent_id"
			),
			array(
				"id",
				"=",
				$this->wxtreectrl_selected_element_id,
				SQLITE3_INTEGER
			)
		);
				
						
		$wxtreeitem = $wxtreeevent->GetItem();
		$wxtreeitemdata = $this->wxtreectrl_website_structure->GetItemData(
			$wxtreeitem
		);
		
		// Where to move the selected branch into?
		if( $wxtreeitemdata->element_id == -1 )
		{
			// Append it to the end of the root-branch and give it a new sort-id.
			// By using the root item, we can determine the new sort-id.
			$root_item = $this->wxtreectrl_website_structure->GetRootItem();
			$last_item = $this->wxtreectrl_website_structure->GetLastChild(
				$root_item
			);

			if( $last_item->IsOk())
			{
				$last_item_data = $this->wxtreectrl_website_structure->GetItemData(
					$last_item
				);

				// Now append it:
				$this->website_project->db_update(
					"page",
					array(
						"parent_id",
						"sort_id"
					),
					array(
						NULL,
						$last_item_data->element_sort_id + 1
					),
					array(
						SQLITE3_INTEGER,
						SQLITE3_INTEGER
					),
					array(
						"id",
						"=",
						$this->wxtreectrl_selected_element_id,
						SQLITE3_INTEGER
					)
				);
				
				/* We have to refresh the sort-ids of the old branch where the
				selected element belonged to. */
				$this->website_project->website_structure_branch_assign_new_sort_ids(
					$db_selected_item_array[0]["parent_id"]
				);
			}
		}
		else
		{
			// Append it to an existing child branch and give it a new sort-id.
			/* Count how many items there are in the branch where the new item
			should be appended to. */			
			$num_of_items = $this->wxtreectrl_website_structure->GetChildrenCount(
				$wxtreeitem,
				FALSE
			);
			
							
			$db_selected_item_array = $this->website_project->db_update(
				"page",
				array(
					"parent_id",
					"sort_id"
				),
				array(
					$wxtreeitemdata->element_id,
					$num_of_items + 1
				),
				array(
					SQLITE3_INTEGER,
					SQLITE3_INTEGER
				),
				array(
					"id",
					"=",
					$this->wxtreectrl_selected_element_id,
					SQLITE3_INTEGER
				)
			);
			/* We have to refresh the sort-ids of the old branch where the
			selected element belonged to. */
			$this->website_project->website_structure_branch_assign_new_sort_ids(
				$db_selected_item_array[0]["parent_id"]
			);
		}
		
		
		$this->mode_move_element_tree = FALSE;	
		$this->refresh_related_controls();
		
		/* The user can only add an element to the main branch when the root
		element is selected. */
		$this->wxbutton_addelement->Enable( TRUE );
		$this->wxbutton_renameelement->Enable( FALSE );
		$this->wxbutton_deleteelement->Enable( FALSE );
		$this->wxbutton_moveelementup->Enable( FALSE );
		$this->wxbutton_moveelementdown->Enable( FALSE );
		$this->wxbutton_moveelementtree->Enable( FALSE );			
	}
	
	
	public function refresh_related_controls()
	{
		$this->refresh_website_structure();
		$this->parent->refresh_website_structure();
	}
}
?>
