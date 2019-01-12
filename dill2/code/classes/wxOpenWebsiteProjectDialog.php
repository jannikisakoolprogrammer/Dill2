<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxOpenWebsiteProjectDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  Represents the dialog which is used to select an existing website project
	to open.

*******************************************************************************/

// Project .php files to include.
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
	"classes" . DIRECTORY_SEPARATOR .
	"Dill2WebsiteProject.php"
);



class wxOpenWebsiteProjectDialog extends wxSingleChoiceDialog
{
	public function __construct(
		$website_project,
		$parent,
		$message,
		$caption
	)
	{
		// Set some important properties.
		$this->parent = $parent;
		
		// Create an array containing all website project names.
		$choices =	array_diff(
						scandir(
							".." .
							DIRECTORY_SEPARATOR .
							DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH .
							DIRECTORY_SEPARATOR
						),
						array(
							".",
							".."
						)
						
					);
		$n_choices = count( $choices );
					
		parent::__construct(
			$parent,
			$message,
			$caption,
			$n_choices,
			array_values( $choices )
		);
	}
	
	
	public function run()
	{
		
		$result = $this->ShowModal();		
		if ( $result == wxID_OK )
		{
			// Open the selected website project.
			$this->parent->website_project = new Dill2WebsiteProject(
				$this->GetStringSelection(),
				FALSE
			);	
			// Disable and enable certain controls in the main frame.
			$this->parent->on_existing_website_project_opened();
		}
	}
}
?>
