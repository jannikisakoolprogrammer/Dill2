<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/main.php

Author:  Jannik Haberbosch

Year: 2014

Info:  The main file for the tool.  This is where the magic happens.
	Every variable that defines a wxType has to start with the the name of the
	widget that it defines without any SPACES NOR camelCases, such as
		wxdataviewtreectrl_.
		Additionally is has to end with an underscore as you can see.
		It should be followed by something that it can be identified with.
		
		For example a wxDataViewTreeCtrl widget for our website structure containers:
			wxdataviewtreectrl_website_structure_containers

*******************************************************************************/

// 3rd party modules.
// Load the wxPHP module.
if(!extension_loaded('wxwidgets')) 
{ 
    dl('wxwidgets.' . PHP_SHLIB_SUFFIX); 
}

// Load the php_ssh2.dll lib extension.
dl('php_ssh2.' . PHP_SHLIB_SUFFIX );


// Include project related .php files.
require_once(
	"classes" . DIRECTORY_SEPARATOR .
	"Dill2.php"
);
    
$dill2 = new Dill2; 
wxApp::SetInstance( $dill2 ); 
wxEntry(); 
    
?> 
