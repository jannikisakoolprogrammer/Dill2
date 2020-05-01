<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/clases/Dill2.php

Author:  Jannik Haberbosch

Year: 2014

Info:  Contains the application class to run the tool 'dill2'.

MAIN NAVIGATION CONTAINER: #mainnav#
SUB NAVIGATION CONTAINER: #subnav#
BREADCRUMB NAVIGATION CONTAINER: #breadnav#
CONTENT CONTAINER: #content#

*******************************************************************************/

// Include related .php project files.
require_once(
	"wxDill2Frame.php"
);
require_once(
	"Dill2WebsiteProject.php"
);

class Dill2 extends wxApp
{ 
	
    function __construct() 
    { 
        parent::__construct();
    } 
    
    function OnInit() 
    { 
		// Show the splash screen.
		wxInitAllImageHandlers();

		$wxbitmap_splashscreen = new wxBitmap(
			".." . 
			DIRECTORY_SEPARATOR .
			"graphics" .
			DIRECTORY_SEPARATOR .
			"dill2_logo.jpg",
			wxBITMAP_TYPE_ANY
		);
		
		$wxsplashscreen = new wxSplashScreen(
			$wxbitmap_splashscreen,
			wxSPLASH_TIMEOUT |
			wxSPLASH_CENTRE_ON_PARENT,
			4000,
			NULL,
			-1
		);
		
		$wxsplashscreen->Show();
				
        $this->wxdill2frame = new wxDill2Frame(
        	null
        );
        /* Certain controls are disabled because on program start, no project has
        yet been opened. */
        $this->wxdill2frame->on_start();
		
		// Set icon.
		$this->wxdill2frame->SetIcon(
			new wxIcon(
				".." . 
				DIRECTORY_SEPARATOR .
				"graphics" .
				DIRECTORY_SEPARATOR .				
				"dill2_logo_icon.ico",
				wxBITMAP_TYPE_ICO
			)
		);
        
        $this->wxdill2frame->Show(); 
        return 0; 
    } 
    
    function OnExit() 
    { 
        return 0; 
    } 
}

?>
