<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxDill2Frame.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file defines the main frame of the tool 'dill2'.

*******************************************************************************/

# Include project related .php files.
// Include project related .php files.
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
	"wxphp_ids.php"
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
	"helpers" . DIRECTORY_SEPARATOR .
	"checksum_helper_functions.php"
);
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"helpers" . DIRECTORY_SEPARATOR .
	"gz_helper_functions.php"
);
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"helpers" . DIRECTORY_SEPARATOR .
	"backup_helper_functions.php"
);

require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"helpers" . DIRECTORY_SEPARATOR .
	"wxPHP_helper_functions.php");
	
require_once(
	"wxNewWebsiteProjectDialog.php"
);
require_once(
	"wxOpenWebsiteProjectDialog.php"
);
require_once(
	"wxManageWebsiteStructureDialog.php"
);
require_once(
	"wxManageTemplatesDialog.php"
);
require_once(
	"wxManageCSSFilesDialog.php"
);
require_once(
	"wxManageJSFilesDialog.php"
);
require_once(
	"wxManageMediaFilesDialog.php"
);
require_once(
	"wxManageDownloadFilesDialog.php"
);
require_once(
	"wxManageWebsiteProjectSettingsDialog.php"
);
require_once(
	"wxManageDill2SettingsDialog.php"
);
require_once(
	"wxHelpDialog.php"
);

require_once("WebsiteProjectSettings_Presenter.php");
require_once("WebsiteProjectSettings_View.php");
require_once("WebsiteProjectSettings_Model.php");

require_once("UploadWebsiteSFTP_Presenter.php");
require_once("UploadWebsiteSFTP_View.php");
require_once("UploadWebsiteSFTP_Model.php");
require_once("UploadWebsiteSFTP_Logic.php");

require_once("UploadWebsiteFTPS_Presenter.php");
require_once("UploadWebsiteFTPS_View.php");
require_once("UploadWebsiteFTPS_Model.php");
require_once("UploadWebsiteFTPS_Logic.php");

require_once("GenerateWebsite_View.php");


class wxDill2Frame extends wxFrame
{
	/* This is base class for our tool.  All controls, events, functions and
	methods are placed into this class.
	
	The class extends wxFrame.  It defines the main window of the tool.  It has
	a vertical three-column layout.
	The left column contains controls for selecting items of which the source
	should be changed.  This includes page containers, navigation containers,
	templates, CSS files aswell as JS files.
	The middle column contains the editor where the source-code can be changed
	aswell as a button to save the content.
	The right column contains two additional controls that show the user one the
	one hand the media files that exist or have been copied into a project aswell
	as the download files that exist or have been copied into a project.
	Clicking or choosing one of these items (by pressing Enter) should copy the
	relative path so that it may be used within the editor.
	
	The menu bar consists of four menu items.
	The first menu item of cource is called 'File'.  This menu item consist of
	several items that handle opening a project, closing a project and exiting
	dill2.
	The second menu contains menu items that are vital for a website project.
	A 'Manage website structure' menu item is used for opening another dialog
	which enables the user to alter the structure of the website.  There are
	similar menu items for templates, CSS files, JS files and generating the
	website.
	A third sub-menu is used for displaying menu items through which the user
	can change certain options of dill2.
	A fourth menu is used for getting info and help.
	
	*/
	
	// Represents the website structure of a website.
	public $wxdataviewtreectrl_mainframe_vertical_left_website_structure;
	// Represents a list of templates:
	public $wxlistbox_mainframe_templates;
	// A wxListBox representing all css files of a website project.
	public $wxlistbox_mainframe_cssfiles;
	// A wxListBox representing all JavaScript files of a website project.
	public $wxlistbox_mainframe_javascriptfiles;
	// A wxStyledTextCtrl which represents the editor.
	public $wxstyledtextctrl_mainframe_editor;
	// A wxButton which represents a button through which the user can save the
	// content of the editor to a file.
	public $wxbutton_mainframe_editorsave;
	// A wxListBox which represents a list of all media files of a website project.
	public $wxlistbox_mainframe_media;
	// A wxListBox which represents a list of all download files of a website project.
	public $wxlistbox_mainframe_downloads;
	/* A wxMenu widgets through which the user can control certain aspects of a
	website project. */
	public $wxmenu_mainframe_project;
	// Represents the entire menu bar:
	public $wxmenubar_mainframe_mainmenu;
	
	// This object represents a single website project.
	public $website_project;
	
	
    function __construct( $parent=null ) 
    {
    	// The constructor creates our base window.
        parent::__construct(
        	$parent,
        	DILL2_WXID_WXFRAME_MAINFRAME,
        	DILL2_CORE_CONSTANT_TOOL_TITLE,
        	wxDefaultPosition,
        	wxDefaultSize
        );
        
        $this->website_project = NULL;
        
        // Maximize the main frame.
        $this->maximize();
        
        // Set the icon.
        switch( php_uname('s'))
        {
        	case "Windows NT":
        	{        	
				// For Windows:
				$this->wxicon_main = new wxIcon( "../icon.bmp", wxBITMAP_TYPE_BMP );
				$this->SetIcon( $this->wxicon_main );  
				break;      	
        	}
        	default:
        	{
				// For Linux:
				$this->wxicon_main = new wxIcon( "../icon.png", wxBITMAP_TYPE_PNG );
				$this->SetIcon( $this->wxicon_main );        	
        	}
        }           
        
        /* Now follows the entire process of creating and placing controls into
        the window (Dill2) of our tool.  The menu (topbar) follows after this.
        */
        
        /* First a 'wxPanel' instance is required.  A 'wxPanel' is a window on
        which controls are placed.  It supports tabbing.
        */
        $wxpanel_mainframe = new wxPanel(
        	$this,
        	DILL2_WXID_WXPANEL_MAINFRAME
        );
        
        /* Next a few 'wxBoxSizer' instances are needed.  These control the
        layout of our controls on the main window 'Dill2'.  A total of four
        of these are needed.  One of them contains three other 'wxBoxSizer'
        instances and packs them.
        Since a three-column layout is required, three of these have to be aligned
        vertically and the other one horizontally, because they are placed next
        to each other. */
        $wxboxsizer_mainframe_horizontal = new wxBoxSizer(
        	wxHORIZONTAL
        );
        $wxboxsizer_mainframe_vertical_left = new wxBoxSizer(
        	wxVERTICAL
        );
        $wxboxsizer_mainframe_vertical_middle = new wxBoxSizer(
        	wxVERTICAL
        );
        $wxboxsizer_mainframe_vertical_right = new wxBoxSizer(
        	wxVERTICAL
        );
        
        /* Place the three wxBoxSizers that need to be aligned vertically next
        to each other into the other 'wxBoxSizer'. */
        $wxboxsizer_mainframe_horizontal->Add(
        	$wxboxsizer_mainframe_vertical_left,
        	2,
        	wxEXPAND | wxALL,
        	5
        );
        $wxboxsizer_mainframe_horizontal->Add(
        	$wxboxsizer_mainframe_vertical_middle,
        	8,
        	wxEXPAND | wxRIGHT | wxTOP | wxBOTTOM,
        	5
        );
        $wxboxsizer_mainframe_horizontal->Add(
        	$wxboxsizer_mainframe_vertical_right,
        	2,
        	wxEXPAND | wxRIGHT | wxTOP | wxBOTTOM,
        	5
        );
        
        
        
        // LEFT COLUMN.
        // Website structure.
        // 'wxStaticText'.
        $wxstatictext_mainframe_vertical_left_website_structure = new wxStaticText(
        	$wxpanel_mainframe,
        	DILL2_WXID_WXSTATICTEXT_VERTICAL_LEFT_WEBSITE_STRUCTURE,
        	DILL2_TEXT_WXSTATICTEXT_VERTICAL_LEFT_WEBSITE_STRUCTURE
        );
        $wxstatictext_mainframe_vertical_left_website_structure->SetLabelMarkup(
        	sprintf(
        		"<u>%s</u>",
        		$wxstatictext_mainframe_vertical_left_website_structure->GetLabel()
        	)
        );
        $wxboxsizer_mainframe_vertical_left->Add(
        	$wxstatictext_mainframe_vertical_left_website_structure,
        	0,
        	wxEXPAND
        );
        
        // 'wxTreeCtrl'.
        $this->wxdataviewtreectrl_mainframe_vertical_left_website_structure = new wxTreeCtrl(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXDATAVIEWTREECTRL_WEBSITE_STRUCTURE
        );
        $wxboxsizer_mainframe_vertical_left->Add(
        	$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure,
        	4,
        	wxEXPAND
        );
        
        
        // Templates.
        // Label.
        $wxstatictext_mainframe_templates = new wxStaticText(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXSTATICTEXT_TEMPLATES,
        	DILL2_TEXT_MAINFRAME_WXSTATICTEXT_TEMPLATES
        );
        $wxstatictext_mainframe_templates->SetLabelMarkup(
        	sprintf(
        		"<u>%s</u>",
        		$wxstatictext_mainframe_templates->GetLabel()
        	)
        );
        $wxboxsizer_mainframe_vertical_left->Add(
        	$wxstatictext_mainframe_templates,
        	0,
        	wxEXPAND | wxTOP,
        	10
        );
        
        
        // 'wxListBox'
        $this->wxlistbox_mainframe_templates = new wxListBox(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXLISTBOX_TEMPLATES,
        	new wxPoint(),
        	wxDefaultSize,
        	[],
        	wxLB_HSCROLL
        );
        $wxboxsizer_mainframe_vertical_left->Add(
        	$this->wxlistbox_mainframe_templates,
        	1,
        	wxEXPAND
        );
        
        
        // CSS files.
        // Label.
        $wxstatictext_mainframe_cssfiles = new wxStaticText(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXSTATICTEXT_CSSFILES,
        	DILL2_TEXT_MAINFRAME_WXSTATICTEXT_CSSFILES
        );
        $wxstatictext_mainframe_cssfiles->SetLabelMarkup(
        	sprintf(
        		"<u>%s</u>",
        		$wxstatictext_mainframe_cssfiles->GetLabel()
        	)
        );
        $wxboxsizer_mainframe_vertical_left->Add(
        	$wxstatictext_mainframe_cssfiles,
        	0,
        	wxEXPAND | wxTOP,
        	10
        );
        	
        // 'wxListBox'.
        $this->wxlistbox_mainframe_cssfiles = new wxListBox(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXLISTBOX_CSSFILES,
        	new wxPoint(),
        	wxDefaultSize,
        	[],
        	wxLB_HSCROLL        	
        );
        $wxboxsizer_mainframe_vertical_left->Add(
        	$this->wxlistbox_mainframe_cssfiles,
        	1,
        	wxEXPAND
        );
        
        
        
        // JavaScript files.
        // Label.
        $wxstatictext_mainframe_javascriptfiles = new wxStaticText(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXSTATICTEXT_JAVASCRIPTFILES,
        	DILL2_TEXT_MAINFRAME_WXSTATICTEXT_JAVASCRIPTFILES
        );
        $wxstatictext_mainframe_javascriptfiles->SetLabelMarkup(
        	sprintf(
        		"<u>%s</u>",
        		$wxstatictext_mainframe_javascriptfiles->GetLabel()
        	)
        );
        $wxboxsizer_mainframe_vertical_left->Add(
        	$wxstatictext_mainframe_javascriptfiles,
        	0,
        	wxEXPAND | wxTOP,
        	10
        );
        
        // 'wxListBox'.
        $this->wxlistbox_mainframe_javascriptfiles = new wxListBox(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXLISTBOX_JAVASCRIPTFILES,
        	new wxPoint(),
        	wxDefaultSize,
        	[],
        	wxLB_HSCROLL        	
        );
        $wxboxsizer_mainframe_vertical_left->Add(
        	$this->wxlistbox_mainframe_javascriptfiles,
        	1,
        	wxEXPAND
        );
        
        
        
        // MIDDLE COLUMN
        // Label.
        $wxstatictext_mainframe_editor = new wxStaticText(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXSTATICTEXT_EDITOR,
        	DILL2_TEXT_MAINFRAME_WXSTATICTEXT_EDITOR
        );
        $wxstatictext_mainframe_editor->SetLabelMarkup(
        	sprintf(
        		"<u>%s</u>",
        		$wxstatictext_mainframe_editor->GetLabel()
        	)
        );
        $wxboxsizer_mainframe_vertical_middle->Add(
        	$wxstatictext_mainframe_editor,
        	0,
        	wxEXPAND
        );
        
        // Editor.
        $this->wxstyledtextctrl_mainframe_editor = new wxStyledTextCtrl(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXSTYLEDTEXTCTRL_EDITOR
        );
        // --> Enable line numbers.
        $this->wxstyledtextctrl_mainframe_editor->SetMarginWidth(
        	0,
        	DILL2_CORE_CONSTANT_MAINFRAME_EDITOR_LINENUMBER_WIDTH
        );
        $this->wxstyledtextctrl_mainframe_editor->SetMarginType(
        	0,
        	wxSTC_MARGIN_NUMBER
        );
        // <--
		// Set the font to a proportional one.
		$this->wxstyledtextctrl_mainframe_editor->StyleSetFont(
			wxSTC_STYLE_DEFAULT,
			new wxFont(
				10,
				wxFONTFAMILY_TELETYPE,
				wxFONTSTYLE_NORMAL,
				wxFONTWEIGHT_NORMAL
			)
		);
        $wxboxsizer_mainframe_vertical_middle->Add(
        	$this->wxstyledtextctrl_mainframe_editor,
        	1,
        	wxEXPAND
        );
        
        // Save button.
        $this->wxbutton_mainframe_editorsave = new wxButton(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXBUTTON_EDITORSAVE,
        	DILL2_TEXT_MAINFRAME_WXBUTTON_EDITORSAVE
        );
        $this->wxbutton_mainframe_editorsave->SetLabelMarkup(
        	sprintf(
        		"<b>%s</b>",
        		$this->wxbutton_mainframe_editorsave->GetLabel()
        	)
        );
        $wxboxsizer_mainframe_vertical_middle->Add(
        	$this->wxbutton_mainframe_editorsave,
        	0,
        	wxTOP,
        	10
        );

		// Enable wrapping long lines.
        $this->wxstyledtextctrl_mainframe_editor->SetWrapMode( TRUE );

        // 4 spaces is the default tab size for Dill2.
        $this->wxstyledtextctrl_mainframe_editor->SetTabWidth( 4 );
        
        
        
        // RIGHT COLUMN
        // Label.
        $wxstatictext_mainframe_media = new wxStaticText(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXSTATICTEXT_MEDIA,
        	DILL2_TEXT_MAINFRAME_WXSTATICTEXT_PICTURES
        );
        $wxstatictext_mainframe_media->SetLabelMarkup(
        	sprintf(
        		"<u>%s</u>",
        		$wxstatictext_mainframe_media->GetLabel()
        	)
        );
        $wxboxsizer_mainframe_vertical_right->Add(
        	$wxstatictext_mainframe_media,
        	0,
        	wxEXPAND
        );
        
        // 'wxListBox' media.
        $this->wxlistbox_mainframe_media = new wxListBox(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXLISTBOX_MEDIA,
        	new wxPoint(),
        	wxDefaultSize,
        	[],
        	wxLB_HSCROLL
        );
        $wxboxsizer_mainframe_vertical_right->Add(
        	$this->wxlistbox_mainframe_media,
        	2,
        	wxEXPAND
        );
		
		
		// Preview image area for a selected image.
		// Label
		$wxstatictext_mainframe_picture_preview = new wxStaticText(
			$wxpanel_mainframe,
			DILL2_WXID_MAINFRAME_WXSTATICTEXT_PICTURE_PREVIEW,
			DILL2_TEXT_MAINFRAME_WXSTATICTEXT_PICTURE_PREVIEW
		);
		$wxstatictext_mainframe_picture_preview->SetLabelMarkup(
			sprintf(
				"<u>%s</u>",
				$wxstatictext_mainframe_picture_preview->GetLabel()
			)
		);
		$wxboxsizer_mainframe_vertical_right->Add(
			$wxstatictext_mainframe_picture_preview,
			0,
			wxEXPAND | wxTOP,
			10
		);
		
		
		// Image
		$this->wxpanel_picture_preview = new wxPanel(
			$wxpanel_mainframe
		);
		
		$this->wxstaticbitmap_picture_preview = new wxStaticBitmap(
			$this->wxpanel_picture_preview,
			wxID_ANY,
			new wxBitmap()
		);

		$wxboxsizer_mainframe_vertical_right->Add(
			$this->wxpanel_picture_preview,
			1,
			wxEXPAND
		);		

        
        // Label.
        $wxstatictext_mainframe_downloads = new wxStaticText(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXSTATICTEXT_DOWNLOADS,
        	DILL2_TEXT_MAINFRAME_WXSTATICTEXT_DOWNLOADS
        );
        $wxstatictext_mainframe_downloads->SetLabelMarkup(
        	sprintf(
        		"<u>%s</u>",
        		$wxstatictext_mainframe_downloads->GetLabel()
        	)
        );
        $wxboxsizer_mainframe_vertical_right->Add(
        	$wxstatictext_mainframe_downloads,
        	0,
        	wxEXPAND | wxTOP,
        	10
        );
        
        // 'wxListBox' downloads.
        $this->wxlistbox_mainframe_downloads = new wxListBox(
        	$wxpanel_mainframe,
        	DILL2_WXID_MAINFRAME_WXLISTBOX_DOWNLOADS,
        	new wxPoint(),
        	wxDefaultSize,
        	[],
        	wxLB_HSCROLL        	
        );
        $wxboxsizer_mainframe_vertical_right->Add(
        	$this->wxlistbox_mainframe_downloads,
        	2,
        	wxEXPAND
        );
        
        
        
        /* SETUP OF THE MENU BAR. */
        // Menubar.
        $this->wxmenubar_mainframe_mainmenu = new wxMenuBar();
        
        // Menues.
        // File menu.
        $wxmenu_mainframe_file = new wxMenu();
        // New project menu item.
        $wxmenu_mainframe_file->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_FILE_NEW_PROJECT,
        	DILL2_TEXT_MAINFRAME_WXMENU_FILE_NEW_PROJECT,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_FILE_NEW_PROJECT
        );
        // Open project menu item.
        $wxmenu_mainframe_file->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_FILE_OPEN_PROJECT,
        	DILL2_TEXT_MAINFRAME_WXMENU_FILE_OPEN_PROJECT,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_FILE_OPEN_PROJECT
        );
        // Import backup.
        $wxmenu_mainframe_file->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_FILE_IMPORT_BACKUP,
        	DILL2_TEXT_MAINFRAME_WXMENU_FILE_IMPORT_BACKUP,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_FILE_IMPORT_BACKUP
        );
        // Close dill2 menu item.
        $wxmenu_mainframe_file->Append(
        	wxID_EXIT,
        	DILL2_TEXT_MAINFRAME_WXMENU_FILE_CLOSE_DILL2,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_FILE_CLOSE_DILL2
        );
        
        // Project menu.
        $this->wxmenu_mainframe_project = new wxMenu();
        // Manage website structure menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE
        );
        // Manage templates menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES
        );
        // Manage CSS files menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES
        );
        // Manage JS files menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES
        );
        // Manage media files menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_MEDIAFILES,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_MEDIAFILES,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_MEDIAFILES
        );
        // Manage download files menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_DOWNLOADFILES,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_DOWNLOADFILES,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_DOWNLOADFILES
        );
        // Separator.
        $this->wxmenu_mainframe_project->AppendSeparator();
        // Generate website menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_GENERATE_WEBSITE,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_GENERATE_WEBSITE,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_GENERATE_WEBSITE
        );
        $this->wxmenu_mainframe_project->AppendSeparator();
        // Start testserver and view website in browser menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER
        );
        // Stop testserver menu item.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_STOP_TESTSERVER,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_STOP_TESTSERVER,
        	DILL2_HELPTEXT_MAINFRAME_WXMEU_PROJECT_STOP_TESTSERVER
        );
		$this->wxmenu_mainframe_project->Enable(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_STOP_TESTSERVER,
			FALSE
		);

		// Separator.
		$this->wxmenu_mainframe_project->AppendSeparator();
		// Upload website to a web server over SFTP
		$this->wxmenu_mainframe_project->Append(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE,
			DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE,
			DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE
		);
		
		// Upload website to a web server over FTPS
		$this->wxmenu_mainframe_project->Append(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE_FTPS,
			DILL2_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE_FTPS_LABEL,
			DILL2_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE_FTPS_HELP);
		
		// Separator.
		$this->wxmenu_mainframe_project->AppendSeparator();
		// Create backup.
		$this->wxmenu_mainframe_project->Append(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_CREATE_BACKUP,
			DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_CREATE_BACKUP,
			DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_CREATE_BACKUP
		);
		
        // Separator.
        $this->wxmenu_mainframe_project->AppendSeparator();
        // Website project settings.
        $this->wxmenu_mainframe_project->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_WEBSITE_PROJECT_SETTINGS,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_WEBSITE_PROJECT_SETTINGS,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_WEBSITE_PROJECT_SETTINGS
        );

        
        $wxmenu_mainframe_help = new wxMenu();
        // Help menu item.
        $wxmenu_mainframe_help->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_HELP_HELP,
        	DILL2_TEXT_MAINFRAME_WXMENU_HELP_HELP,
        	DILL2_HELPTEXT_TEST_MAINFRAME_WXMENU_HELP_HELP
        );
        // About menu item.
        $wxmenu_mainframe_help->Append(
        	DILL2_WXID_MAINFRAME_WXMENU_HELP_ABOUT,
        	DILL2_TEXT_MAINFRAME_WXMENU_HELP_ABOUT,
        	DILL2_HELPTEXT_MAINFRAME_WXMENU_HELP_ABOUT
        );
        
        // Append the menues.
        $this->wxmenubar_mainframe_mainmenu->Append(
        	$wxmenu_mainframe_file,
        	DILL2_TEXT_MAINFRAME_WXMENU_FILE
        );
        $this->wxmenubar_mainframe_mainmenu->Append(
        	$this->wxmenu_mainframe_project,
        	DILL2_TEXT_MAINFRAME_WXMENU_PROJECT
        );
        /*
        $this->wxmenubar_mainframe_mainmenu->Append(
        	$wxmenu_mainframe_options,
        	DILL2_TEXT_MAINFRAME_WXMENU_OPTIONS
        );
        */
        $this->wxmenubar_mainframe_mainmenu->Append(
        	$wxmenu_mainframe_help,
        	DILL2_TEXT_MAINFRAME_WXMENU_HELP
        );
        
        // Attach the menu to the mainframe.
        $this->SetMenuBar(
        	$this->wxmenubar_mainframe_mainmenu
        );
		
		
		////////////////////////////////////////////////////////////////////////
		// CLIPBOARD
		////////////////////////////////////////////////////////////////////////
		
		// This is for copying file paths to the clipboard.		
		$this->wxclipboard_path	= new wxClipBoard();
		
		
		////////////////////////////////////////////////////////////////////////
		// Object for a preview image.
		////////////////////////////////////////////////////////////////////////
		$this->wximage_preview = new wxImage();
        
        
        // STATUS BAR.
        /*
        $wxstatusbar_mainframe = new wxStatusBar(
        	$this,
        	wxID_ANY
        );
        */
        

        
        //
        // Child dialogs.
        //       	
      	$wxmanagedill2settingsdialog = new wxManageDill2SettingsDialog(
       		$this->website_project,
       		$this,
       		DILL2_WXID_WXMANAGEDILL2SETTINGSDIALOG,
       		DILL2_TEXT_WXMANAGEDILL2SETTINGSDIALOG_TITLE
       	);
       	
       	// WxHelpDialog.
       	$wxhelpdialog = new wxHelpDialog(
       		$this->website_project,
       		$this,
       		DILL2_WXID_WXHELPDIALOG,
       		DILL2_TEXT_WXHELPDIALOG_TITLE
       	);
        

        
        //
        // Events
        //
		// Event that occurs if the window size is changed.
		$this->connect(
			DILL2_WXID_MAINFRAME_WINDOWSIZE_CHANGED,
			wxEVT_SIZE,
			array(
				$this,
				"on_dill2_wxid_mainframe_windowsize_changed"
			)
		);
		
        $this->connect(
	        DILL2_WXID_MAINFRAME_WXMENU_FILE_NEW_PROJECT,
	        wxEVT_COMMAND_MENU_SELECTED,
	        array(
	        	$this,
	        	"on_dill2_wxid_mainframe_wxmenu_file_new_project_clicked"
	        )
	    );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_FILE_OPEN_PROJECT,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_wxid_mainframe_wxmenu_file_open_project_clicked"
        	)
        );
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXMENU_FILE_IMPORT_BACKUP,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_wxid_mainframe_wxmenu_file_import_backup_clicked"
        	)
        );
        $this->connect(
        	wxID_EXIT,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_close"
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_project_manage_website_structure_clicked"
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_project_manage_templates_clicked"
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_project_manage_cssfiles_clicked"
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_project_manage_jsfiles_clicked",
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_MEDIAFILES,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_project_manage_mediafiles_clicked"
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_DOWNLOADFILES,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_project_manage_downloadfiles_clicked"
        	)
        );
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_GENERATE_WEBSITE,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_project_generate_website_clicked"
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_WEBSITE_PROJECT_SETTINGS,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_project_settings_clicked"
        	)
        );
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_start_testserver_clicked"
        	)
        );
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_STOP_TESTSERVER,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_stop_testserver_clicked"
        	)
        );
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_upload_website_sftp"
        	)
        );
		
		/* Connect event handler that triggers opening the required
		form to upload a website over FTPS. */
		$this->Connect(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE_FTPS,
			wxEVT_COMMAND_MENU_SELECTED,
			array(
				$this,
				"on_dill2_wxid_mainframe_wxmenu_project_upload_website_ftps_selected"));

		
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_HELP_ABOUT,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_about"
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_OPTIONS_SETTINGS,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$wxmanagedill2settingsdialog,
        		"run"
        	)
        );
        $this->connect(
        	DILL2_WXID_MAINFRAME_WXMENU_HELP_HELP,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$wxhelpdialog,
        		"run"
        	)
        );
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXMENU_PROJECT_CREATE_BACKUP,
        	wxEVT_COMMAND_MENU_SELECTED,
        	array(
        		$this,
        		"on_dill2_mainframe_wxmenu_create_backup_clicked"
        	)
        );
        
        
        // Events for when an item has been selected in a wxListBox.
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXLISTBOX_CSSFILES,
        	wxEVT_LISTBOX,
        	array(
        		$this,
				"set_new_item_to_edit"
			)
		);
		
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXLISTBOX_JAVASCRIPTFILES,
        	wxEVT_LISTBOX,
        	array(
        		$this,
				"set_new_item_to_edit"
			)
		);
		
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXDATAVIEWTREECTRL_WEBSITE_STRUCTURE,
        	wxEVT_TREE_SEL_CHANGED,
        	array(
        		$this,
				"set_new_item_to_edit"
			)
		);
		
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXLISTBOX_TEMPLATES,
        	wxEVT_LISTBOX,
        	array(
        		$this,
				"set_new_item_to_edit"
			)
		);
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXLISTBOX_DOWNLOADS,
        	wxEVT_LISTBOX,
        	array(
        		$this,
				"set_new_item_to_edit"
			)
		);
        $this->Connect(
        	DILL2_WXID_MAINFRAME_WXLISTBOX_MEDIA,
        	wxEVT_LISTBOX,
        	array(
        		$this,
				"set_new_item_to_edit"
			)
		);				
		
		$this->Connect(
			DILL2_WXID_MAINFRAME_WXBUTTON_EDITORSAVE,
			wxEVT_BUTTON,
			array(
				$this,
				"update_selected_item_content"
			)
		);
		
		$this->wxstyledtextctrl_mainframe_editor->Connect(
			wxEVT_CHAR,
			array(
				$this,
				"on_wxstyledtextctrl_mainframe_editor_keydown"
			)
		);
		
		$this->wxstyledtextctrl_mainframe_editor->Connect(
			wxEVT_KEY_UP,
			array(
				$this,
				"on_wxstyledtextctrl_mainframe_editor_keydown_other"
			)
		);	
		
        
        
        // Dstribute / layout the controls in the mainframe.
        $wxpanel_mainframe->SetSizer( $wxboxsizer_mainframe_horizontal );
	    $wxboxsizer_mainframe_horizontal->SetSizeHints( $this );
    }
    
    
    public function on_wxstyledtextctrl_mainframe_editor_keydown( $wxkeyevent )
    {
    	if( $wxkeyevent->GetKeyCode() == 19 )
    	{
    		$this->update_selected_item_content();
    	}
    	else
    	{
    		$this->mark_item_as_edited();
    		$wxkeyevent->Skip();
    	}
    }


    public function on_wxstyledtextctrl_mainframe_editor_keydown_other( $wxkeyevent )
    {
    	
    	$current_line = $this->wxstyledtextctrl_mainframe_editor->GetCurrentLine();	

    	if( ( $wxkeyevent->GetKeyCode() == 13 ) && ( $current_line > 0 ) )
    	{

			$line_indent = $this->wxstyledtextctrl_mainframe_editor->GetLineIndentation(
				$current_line - 1
			);
			
    		if( $line_indent != 0 )
    		{
				$this->wxstyledtextctrl_mainframe_editor->SetLineIndentation(
					$current_line, $line_indent
				);

				if( $this->wxstyledtextctrl_mainframe_editor->GetLineCount() > ( $current_line + 1 ) )
				{
					$pos_from_line = $this->wxstyledtextctrl_mainframe_editor->PositionFromLine( $current_line );
					if( $line_indent > 4 )
					{
						$pos_from_line -= $line_indent / 4 + $line_indent / 4;
					}
					else
					{
						$pos_from_line -= $line_indent / 4 + 1;
					}
					
					$pos_from_line = $pos_from_line - ( $line_indent / 4 );
					$this->wxstyledtextctrl_mainframe_editor->GoToPos(
						$pos_from_line + $line_indent
					);				
				}
				else
				{					
					$this->wxstyledtextctrl_mainframe_editor->GoToPos(
						$this->wxstyledtextctrl_mainframe_editor->PositionFromLine( $current_line ) + $line_indent
					);
				}
    		}
    	}
    	
		if( $wxkeyevent->GetKeyCode() == 8 || $wxkeyevent->GetKeyCode() == 13 )
		{
			// Backspace has been pressed.
			$this->mark_item_as_edited();
		}
		$wxkeyevent->Skip();
    }    
    
    
    public function on_about( $wxcommandevent )
    {
    	/* Displays the About dialog with relevant information about the project.
    	*/
    	$wxaboutdialoginfo = new wxAboutDialogInfo();
    	$wxaboutdialoginfo->AddArtist( DILL2_CORE_CONSTANT_TOOL_AUTHOR );
    	$wxaboutdialoginfo->AddDeveloper( DILL2_CORE_CONSTANT_TOOL_AUTHOR );
    	$wxaboutdialoginfo->AddDocWriter( DILL2_CORE_CONSTANT_TOOL_AUTHOR );
    	$wxaboutdialoginfo->AddTranslator( DILL2_CORE_CONSTANT_TOOL_AUTHOR );
    	$wxaboutdialoginfo->SetLicense( DILL2_CORE_CONSTANT_TOOL_LICENSE );
    	$wxaboutdialoginfo->SetName( DILL2_CORE_CONSTANT_TOOL_NAME );
    	
    	wxAboutBox( $wxaboutdialoginfo );
    }
    
    
    public function on_start()
    {
    	/* Defines which controls need to be disabled as soon as the user has
    	started dill2.
    	
    	*/
    	$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->Enable( FALSE );
    	$this->wxlistbox_mainframe_templates->Enable( FALSE );
		$this->wxlistbox_mainframe_cssfiles->Enable( FALSE );
		$this->wxlistbox_mainframe_javascriptfiles->Enable( FALSE );
		$this->wxstyledtextctrl_mainframe_editor->Enable( FALSE );
		$this->wxbutton_mainframe_editorsave->Enable( FALSE );
		$this->wxlistbox_mainframe_media->Enable( FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_MEDIAFILES, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_DOWNLOADFILES, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_GENERATE_WEBSITE, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_STOP_TESTSERVER, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_WEBSITE_PROJECT_SETTINGS, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_CREATE_BACKUP, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE, FALSE );
		
		wxphp_disable_menu_item(
			$this->wxmenubar_mainframe_mainmenu,
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE_FTPS);
		
		// The 'Open' menu item is disabled if there are no website projects.
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
		
		if( $n_choices == 0 )
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_FILE_OPEN_PROJECT, FALSE );
		}
    }
    
    
    public function on_new_website_project_created()
    {
    	/* This method is called as soon as a new website project is created.  It
    	enables certain controls while disabling other controls.
    	
    	*/
    	$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->Enable( TRUE );
    	$this->wxlistbox_mainframe_templates->Enable( TRUE );
		$this->wxlistbox_mainframe_cssfiles->Enable( TRUE );
		$this->wxlistbox_mainframe_javascriptfiles->Enable( TRUE );
		//$this->wxstyledtextctrl_mainframe_editor->Enable( TRUE );
		//$this->wxbutton_mainframe_editorsave->Enable( TRUE );
		$this->wxlistbox_mainframe_media->Enable( TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_MEDIAFILES, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_DOWNLOADFILES, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_GENERATE_WEBSITE, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_WEBSITE_PROJECT_SETTINGS, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_FILE_NEW_PROJECT, FALSE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_CREATE_BACKUP, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_FILE_OPEN_PROJECT, TRUE );
		$this->wxmenubar_mainframe_mainmenu->Enable(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE, TRUE );
			
		wxphp_enable_menu_item(
			$this->wxmenubar_mainframe_mainmenu,
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE_FTPS);
		
		$this->root2 = NULL;
		
		$this->on_dill2_mainframe_wxmenu_stop_testserver_clicked();
    }
    
    
    public function on_existing_website_project_opened()
    {
    	/* This method is called as soon as an existing website project is opened.
    	It enables certain controls while disabling other controls.
    	
    	*/
    	$this->on_new_website_project_created();
    	
    	// Refresh the content of certain controls (wxWidgets).
    	$this->refresh_website_structure();
    	$this->wxlistbox_mainframe_templates_reload(
    		$this->website_project->get_template_names()
    	);
    	$this->wxlistbox_mainframe_cssfiles_reload();
    	$this->wxlistbox_mainframe_javascript_files_reload();
    	$this->wxlistbox_mainframe_media_files_reload();
		$this->wxlistbox_mainframe_download_files_reload();
    }
    
    
    public function on_close()
    {
    	/* If any website project has been opened, closes the website project
    	and quits the program. */
    	$this->on_dill2_mainframe_wxmenu_stop_testserver_clicked();
    	if( $this->website_project != NULL )
    	{
    		$this->website_project = NULL;
    	}
    	$this->destroy();
    }
    
    
    public function wxlistbox_mainframe_templates_reload( $templates )
    {
    	/* Reloads templates.
    	
    	*/
    	$this->wxlistbox_mainframe_templates->Clear();
    	$this->wxlistbox_mainframe_templates->Set( $templates );
    }
    
    
    public function wxlistbox_mainframe_cssfiles_reload()
    {
    	/* Reloads css files. */
    	$this->wxlistbox_mainframe_cssfiles->Clear();
    	$this->wxlistbox_mainframe_cssfiles->Set(
    		$this->website_project->get_file_names( "css" )
    	);
    }
    
    
    public function wxlistbox_mainframe_javascript_files_reload()
    {
    	/* Reloads JavaScript files. */
    	$this->wxlistbox_mainframe_javascriptfiles->Clear();
    	$this->wxlistbox_mainframe_javascriptfiles->Set(
    		$this->website_project->get_file_names( "js" )
    	);
    }
    
    
    public function wxlistbox_mainframe_media_files_reload()
    {
    	/* Reloads media files. */
    	$this->wxlistbox_mainframe_media->Clear();
    	$this->wxlistbox_mainframe_media->Set(
    		$this->website_project->get_file_names( "media" )
    	);
    }
    
    
    public function wxlistbox_mainframe_download_files_reload()
    {
    	/* Reloads download files. */
    	$this->wxlistbox_mainframe_downloads->Clear();
    	$this->wxlistbox_mainframe_downloads->Set(
    		$this->website_project->get_file_names( "download" )
    	);
    }
    
    
    public function on_dill2_wxid_mainframe_wxmenu_file_new_project_clicked()
    {
        $wxnewwebsiteprojectdialog = new wxNewWebsiteProjectDialog(
        	$this->website_project,
        	$this,
        	DILL2_WXID_WXDIALOG_NEWPROJECT,
        	DILL2_TEXT_WXDIALOG_NEWPROJECT_CAPTION
        );
        $wxnewwebsiteprojectdialog->run();
        
        $this->set_unset_state_main_window();
    }
    
    
    public function on_dill2_wxid_mainframe_wxmenu_file_open_project_clicked()
    {
        $wxopenwebsiteprojectdialog = new wxOpenWebsiteProjectDialog(
        	$this->website_project,
        	$this,
        	DILL2_TEXT_WXDIALOG_OPENPROJECT_MESSAGE,
        	DILL2_TEXT_WXDIALOG_OPENPROJECT_CAPTION
        );
        $wxopenwebsiteprojectdialog->run();
        
        $this->set_unset_state_main_window();
    }
    
    
    
    public function on_dill2_mainframe_wxmenu_project_manage_templates_clicked()
    {
        $wxmanagetemplatesdialog = new wxManageTemplatesDialog(
        	$this->website_project,
        	$this,
        	DILL2_WXID_WXMANAGETEMPLATESDIALOG,
        	DILL2_TEXT_WXMANAGETEMPLATESDIALOG_TITLE
        );
        $wxmanagetemplatesdialog->run();
        
        $this->set_unset_state_main_window();
    }
    
    
    public function on_dill2_mainframe_wxmenu_project_manage_website_structure_clicked()
    {
        $wxmanagewebsitestructuredialog = new wxManageWebsiteStructureDialog(
        	$this->website_project,
        	$this,
        	DILL2_WXID_WXDIALOG_MANAGEWEBSITESTRUCTURE,
        	DILL2_TEXT_WXDIALOG_MANAGEWEBSITESTRUCTURE_TITLE
        );
        $wxmanagewebsitestructuredialog->run();
        
        $this->set_unset_state_main_window();
    }
    
    
    public function on_dill2_mainframe_wxmenu_project_manage_cssfiles_clicked()
    {
        $dialog = new wxManageCSSFilesDialog(
        	$this->website_project,
        	$this,
        	DILL2_WXID_WXMANAGECSSFILESDIALOG,
        	DILL2_TEXT_WXMANAGECSSFILESDIALOG_TITLE
        );
        $dialog->run();
        
        $this->set_unset_state_main_window();
    }
    
    
    public function on_dill2_mainframe_wxmenu_project_manage_jsfiles_clicked()
    {
        $dialog = new wxManageJSFilesDialog(
        	$this->website_project,
        	$this,
        	DILL2_WXID_WXMANAGEJSFILESDIALOG,
        	DILL2_TEXT_WXMANAGEJSFILESDIALOG_TITLE
       	);
       	$dialog->run();
       	
       	$this->set_unset_state_main_window();
    }
    
    
    public function on_dill2_mainframe_wxmenu_project_manage_mediafiles_clicked()
    {
       	$dialog = new wxManageMediaFilesDialog(
       		$this->website_project,
       		$this,
       		DILL2_WXID_WXMANAGEMEDIAFILESDIALOG,
       		DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_TITLE
       	);
       	$dialog->run();
       	
       	$this->set_unset_state_main_window();
    }
    
    
    public function on_dill2_mainframe_wxmenu_project_manage_downloadfiles_clicked()
    {
       	$dialog = new wxManageDownloadFilesDialog(
       		$this->website_project,
       		$this,
       		DILL2_WXID_WXMANAGEDOWNLOADFILESDIALOG,
       		DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_TITLE
       	);
       	$dialog->run();
       	
       	$this->set_unset_state_main_window();
    }
    
    
    public function on_dill2_mainframe_wxmenu_project_settings_clicked()
    {
		$view = new WebsiteProjectSettings_View(
       		$this,
       		DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_TITLE
		);
		
		$model = new WebsiteProjectSettings_Model($this->website_project);
		
		$presenter = new WebsiteProjectSettings_Presenter(
			$view,
			$model);
		
		$presenter->run();

		/*
       	$dialog = new wxManageWebsiteProjectSettingsDialog(
       		$this->website_project,
       		$this,
       		DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG,
       		DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_TITLE
       	);
       	$dialog->run();
       	
       	$this->set_unset_state_main_window();
		*/
    }
    
    
	public function refresh_website_structure( $branch_to_ignore = NULL )
	{
		/* Calling this function refreshes the website structure that is shown
		to the user.
		
		 */
		$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->DeleteAllItems();
		$wxtreeitemdata = new wxTreeItemData();
		$wxtreeitemdata->element_id = -1;
		$this->root2 = $this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->AddRoot(
			"ROOT",
			-1,
			-1,
			$wxtreeitemdata
		);
		$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->SelectItem(
			$this->root2
		);	
		$this->update_website_structure(
			$this->website_project->get_website_structure(),
			NULL,
			TRUE,
			$branch_to_ignore
		);
		$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->Expand(
			$this->root2
		);
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
			$wxtreeitemdata->element_state = $value["self"]["state"];
			
			// Will only be called in the lowest "level" (no recursion).
			if( $is_root )
			{
				// Append a root-element.
				$wxtreeitemid = $this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->AppendItem(
					$this->root2,
					$value["self"]["name"],
					-1,
					-1,
					$wxtreeitemdata
				);
			}
			else
			{
				// Append a child-element.  Called only during recursion.
				$wxtreeitemid = $this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->AppendItem(
					$root,
					$value["self"]["name"],
					-1,
					-1,
					$wxtreeitemdata
				);
			}
			
			if ($wxtreeitemdata->element_state == DILL2_WXID_MANAGEWEBSITESTRUCTUREDIALOG_WXCOMBOBOX_STATE_PREVIEW)
			{
				$item_colour = new wxColour(
					255,
					125,
					0);
			}
			else
			{
				$item_colour = new wxColour(
					0,
					0,
					0);
			}
			
			$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->SetItemTextColour(
				$wxtreeitemid,
				$item_colour);			
			
			
			// Go deeper in the tree structure if necessary.
			$this->update_website_structure( $value["children"], $wxtreeitemid, FALSE, $branch_to_ignore_id );
			$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->Expand( $wxtreeitemid );
		}
	}
	
	
	public function set_new_item_to_edit( $wxcommandevent )
	{
		/* If the user has changed a file and is about to change to another file,
		then ask him whether he wants to save the changes to the current file
		before changing to the new file.

		*/
		$new_text = utf8_encode( $this->wxstyledtextctrl_mainframe_editor->GetText() );
		
		if( ( strlen( $new_text ) > 0 ) || $this->website_project->file_to_edit_original != NULL )
		{
			if( strcmp( $this->website_project->file_to_edit_original, $new_text ) != 0 )
			{
				if( $this->website_project->exists_files_buffer(
					$this->website_project->file_to_edit["name"],
					$this->website_project->file_to_edit["type"] ) )
				{
					$this->website_project->write_file_contents_to_files_buffer(
						$this->website_project->file_to_edit["name"],
						$this->website_project->file_to_edit["type"],
						$new_text
					);
					$this->website_project->file_to_edit_original = NULL;					
				}
				/*	
				$wxdialog_savecontents = new wxMessageDialog(
					$this,
					DILL2_MESSAGE_WXMESSAGEDIALOG_CONFIRM_CHANGES,
					DILL2_CAPTION_CONFIRM,
					wxYES_NO
				);
				if( $wxdialog_savecontents->ShowModal() == wxID_YES )
				{
					$this->update_selected_item_content();
				}		
				*/
			}
		}
		
		
		$this->website_project->file_to_edit["type"] = NULL;
		$this->website_project->file_to_edit["name"] = NULL;
		$this->website_project->file_to_edit["id"] = NULL;
		
		$eventid = $wxcommandevent->GetId();
		$selection_string = $wxcommandevent->GetString();

		if( substr( $selection_string, 0, 1 ) == "*" )
		{
			$selection_string = substr( $selection_string, 1 );
		}
		
		// Enable the editor and the save button if it's not already enabled.
		if( !$this->wxbutton_mainframe_editorsave->IsEnabled() )
		{
			$this->wxbutton_mainframe_editorsave->Enable( TRUE );
		};
		if( !$this->wxstyledtextctrl_mainframe_editor->IsEnabled() )
		{
			$this->wxstyledtextctrl_mainframe_editor->Enable( TRUE );
		}
		
		switch( $eventid )
		{
			case DILL2_WXID_MAINFRAME_WXLISTBOX_CSSFILES:
			{
				// Set the syntax highlighting of the editor to "CSS":
				$this->wxstyledtextctrl_mainframe_editor->StyleClearAll();
				$this->wxstyledtextctrl_mainframe_editor->SetLexer( wxSTC_LEX_CSS );
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_ATTRIBUTE, new wxColour( 255, 20, 147 ) ); // DeepPink
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_COMMENT, new wxColour( 220, 20, 60 ) ); // Crimson
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_DEFAULT, new wxColour( 0, 0, 0 ) ); // Black
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_DIRECTIVE, new wxColour( 139, 0, 0 ) ); // DarkRed
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_DOUBLESTRING, new wxColour( 255, 69, 0 ) ); // OrangeRed
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_EXTENDED_IDENTIFIER, new wxColour( 255, 99, 71 ) ); // Tomato
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_EXTENDED_PSEUDOCLASS, new wxColour( 255, 215, 0 ) ); // Gold
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_EXTENDED_PSEUDOELEMENT, new wxColour( 189, 183, 107 ) ); // DarkKhaki
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_ID, new wxColour( 188, 14, 143 ) ); // RosyBrown
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_IDENTIFIER, new wxColour( 184, 134, 11 ) ); // DarkGoldenrod
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_IDENTIFIER2, new wxColour( 139, 69, 19 ) ); // SaddleBrown
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_IDENTIFIER3, new wxColour( 85, 107, 47 ) ); // DarkOliveGreen
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_IMPORTANT, new wxColour( 50, 205, 50 ) ); // LimeGreen
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_MEDIA, new wxColour( 0, 255, 127 ) ); // SpringGreen
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_OPERATOR, new wxColour( 143, 188, 143 ) ); // DarkSeaGreen
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_PSEUDOCLASS, new wxColour( 34, 139, 34 ) ); // ForestGreen
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_PSEUDOELEMENT, new wxColour( 0, 128, 128 ) ); // Teal
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_SINGLESTRING, new wxColour( 0, 0, 255 ) ); // Blue
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_TAG, new wxColour( 25, 25, 112 ) ); // MidnightBlue
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_UNKNOWN_IDENTIFIER, new wxColour( 128, 0, 128 ) ); // Purple
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_UNKNOWN_PSEUDOCLASS, new wxColour( 47, 79, 79 ) ); // DarkSlateGray
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_VALUE, new wxColour( 105, 105, 105 ) ); // DimGray
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_CSS_VARIABLE, new wxColour( 238, 130, 238 ) ); // Violet
				
				
				$this->website_project->file_to_edit["type"] = "CSS";
				$this->website_project->file_to_edit["name"] = $selection_string;

				$this->wxstyledtextctrl_mainframe_editor->ClearAll();

				/* TODO:  Here we have to read from the files-buffer to the editor!!! */
				/* TODO:  OR! Here we have to read from the harddisk to the files-buffer!!! */
				if( !$this->website_project->exists_files_buffer(
					$this->website_project->file_to_edit["name"],
					$this->website_project->file_to_edit["type"] ) )
				{
					// Read from the harddisk to the files-buffer for the first and only time!
					$this->website_project->read_files_buffer_content_from_harddisk();
				}
				
				// Read from the files-buffer.
				$new_content = $this->website_project->read_file_contents_from_files_buffer(
						$this->website_project->file_to_edit["name"],
						$this->website_project->file_to_edit["type"]
				);
				$this->wxstyledtextctrl_mainframe_editor->SetText(
					$new_content
					// $this->website_project->read_content()
				);

					
				$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->UnselectAll();
				$this->wxlistbox_mainframe_templates->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_javascriptfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_downloads->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_media->SetSelection( wxNOT_FOUND );
				$this->wxbutton_mainframe_editorsave->Enable( TRUE );
				$this->wxstyledtextctrl_mainframe_editor->Enable( TRUE );
				
				$this->website_project->file_to_edit_original = $new_content;
				break;
			}
			case DILL2_WXID_MAINFRAME_WXLISTBOX_JAVASCRIPTFILES:
			{
				// Set the syntax highlighting of the editor to "JS":
				$this->wxstyledtextctrl_mainframe_editor->StyleClearAll();
				$this->wxstyledtextctrl_mainframe_editor->SetLexer( wxSTC_LEX_ESCRIPT );
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_BRACE, new wxColour( 255, 20, 147 ) ); // DeepPink
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_COMMENT, new wxColour( 220, 20, 60 ) ); // Crimson
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_DEFAULT, new wxColour( 0, 0, 0 ) ); // Black
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_COMMENTLINE, new wxColour( 139, 0, 0 ) ); // DarkRed
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_COMMENTDOC, new wxColour( 255, 69, 0 ) ); // OrangeRed
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_IDENTIFIER, new wxColour( 255, 99, 71 ) ); // Tomato
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_NUMBER, new wxColour( 255, 215, 0 ) ); // Gold
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_OPERATOR, new wxColour( 189, 183, 107 ) ); // DarkKhaki
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_STRING, new wxColour( 188, 14, 143 ) ); // RosyBrown
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_WORD, new wxColour( 184, 134, 11 ) ); // DarkGoldenrod
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_WORD2, new wxColour( 139, 69, 19 ) ); // SaddleBrown
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_ESCRIPT_WORD3, new wxColour( 85, 107, 47 ) ); // DarkOliveGreen
				
							
				$this->website_project->file_to_edit["type"] = "JS";
				$this->website_project->file_to_edit["name"] = $selection_string;

				/* TODO:  Here we have to read from the files-buffer to the editor!!! */
				/* TODO:  OR! Here we have to read from the harddisk to the files-buffer!!! */
				if( !$this->website_project->exists_files_buffer(
					$this->website_project->file_to_edit["name"],
					$this->website_project->file_to_edit["type"] ) )
				{
					// Read from the harddisk to the files-buffer for the first and only time!
					$this->website_project->read_files_buffer_content_from_harddisk();
				}
				
				// Read from the files-buffer.
				$new_content = $this->website_project->read_file_contents_from_files_buffer(
						$this->website_project->file_to_edit["name"],
						$this->website_project->file_to_edit["type"]
				);
				$this->wxstyledtextctrl_mainframe_editor->SetText(
					$new_content
					// $this->website_project->read_content()
				);				

				$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->UnselectAll();
				$this->wxlistbox_mainframe_cssfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_templates->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_downloads->SetSelection( wxNOT_FOUND );	
				$this->wxlistbox_mainframe_media->SetSelection( wxNOT_FOUND );
				$this->wxbutton_mainframe_editorsave->Enable( TRUE );
				$this->wxstyledtextctrl_mainframe_editor->Enable( TRUE );
				
				$this->website_project->file_to_edit_original = $new_content;
				break;
			}
			case DILL2_WXID_MAINFRAME_WXDATAVIEWTREECTRL_WEBSITE_STRUCTURE:
			{
				// Set the syntax highlighting of the editor to "HTML":
				$this->wxstyledtextctrl_mainframe_editor->StyleClearAll();
				$this->wxstyledtextctrl_mainframe_editor->SetLexer( wxSTC_LEX_HTML );
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_DOUBLESTRING, new wxColour( 255, 20, 147 ) ); // DeepPink
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_SINGLESTRING, new wxColour( 220, 20, 60 ) ); // Crimson
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_DEFAULT, new wxColour( 0, 0, 0 ) ); // Black
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_ENTITY, new wxColour( 139, 0, 0 ) ); // DarkRed
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_TAG, new wxColour( 255, 69, 0 ) ); // OrangeRed
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_TAGUNKNOWN, new wxColour( 255, 99, 71 ) ); // Tomato
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_ATTRIBUTE, new wxColour( 255, 215, 0 ) ); // Gold
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_ATTRIBUTEUNKNOWN, new wxColour( 189, 183, 107 ) ); // DarkKhaki
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_COMMENT, new wxColour( 188, 14, 143 ) ); // RosyBrown
				
							
				$this->website_project->file_to_edit["type"] = "PAGE";
				$item = $wxcommandevent->GetItem();
				$itemdata = $this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->GetItemData(
					$item
				);
				
				// The ROOT element is ignored.
				if( $itemdata->element_id == -1 )
				{
					$this->website_project->file_to_edit["type"] = NULL;
					$this->website_project->file_to_edit["name"] = NULL;
					$this->website_project->file_to_edit["id"] = NULL;
					
					// Disable the editor and the save button.

					/* TODO:  Here we have to write from the editor to the files-buffer!!! */
					$this->wxstyledtextctrl_mainframe_editor->ClearAll();
					$this->wxbutton_mainframe_editorsave->Enable( FALSE );
					$this->wxstyledtextctrl_mainframe_editor->Enable( FALSE );
					$this->wxlistbox_mainframe_cssfiles->SetSelection( wxNOT_FOUND );
					$this->wxlistbox_mainframe_templates->SetSelection( wxNOT_FOUND );
					$this->wxlistbox_mainframe_javascriptfiles->SetSelection( wxNOT_FOUND );
					$this->wxlistbox_mainframe_downloads->SetSelection( wxNOT_FOUND );
					$this->wxlistbox_mainframe_media->SetSelection( wxNOT_FOUND );					
					
					$this->website_project->file_to_edit_original = NULL;
					return;
				}
				
				$this->website_project->file_to_edit["id"] = $itemdata->element_id;
				$this->website_project->file_to_edit["type"] = "PAGE";
				$this->website_project->file_to_edit["name"] = $itemdata->element_id;

				$this->wxstyledtextctrl_mainframe_editor->ClearAll();

				/* TODO:  Here we have to read from the files-buffer to the editor!!! */
				/* TODO:  OR! Here we have to read from the harddisk to the files-buffer!!! */
				if( !$this->website_project->exists_files_buffer(
					$this->website_project->file_to_edit["id"],
					$this->website_project->file_to_edit["type"] ) )
				{
					// Read from the harddisk to the files-buffer for the first and only time!
					$this->website_project->read_files_buffer_content_from_harddisk();
				}
				
				// Read from the files-buffer.
				$new_content = $this->website_project->read_file_contents_from_files_buffer(
						$this->website_project->file_to_edit["id"],
						$this->website_project->file_to_edit["type"]
				);
				$this->wxstyledtextctrl_mainframe_editor->SetText(
					$new_content
					// $this->website_project->read_content()
				);

								
				$this->wxlistbox_mainframe_cssfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_templates->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_javascriptfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_downloads->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_media->SetSelection( wxNOT_FOUND );
				$this->wxbutton_mainframe_editorsave->Enable( TRUE );
				$this->wxstyledtextctrl_mainframe_editor->Enable( TRUE );
				
				$this->website_project->file_to_edit_original = $new_content;
				break;
			}
			case DILL2_WXID_MAINFRAME_WXLISTBOX_TEMPLATES:
			{
				// Set the syntax highlighting of the editor to "HTML":
				$this->wxstyledtextctrl_mainframe_editor->StyleClearAll();
				$this->wxstyledtextctrl_mainframe_editor->SetLexer( wxSTC_LEX_HTML );
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_DOUBLESTRING, new wxColour( 255, 20, 147 ) ); // DeepPink
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_SINGLESTRING, new wxColour( 220, 20, 60 ) ); // Crimson
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_DEFAULT, new wxColour( 0, 0, 0 ) ); // Black
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_ENTITY, new wxColour( 139, 0, 0 ) ); // DarkRed
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_TAG, new wxColour( 255, 69, 0 ) ); // OrangeRed
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_TAGUNKNOWN, new wxColour( 255, 99, 71 ) ); // Tomato
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_ATTRIBUTE, new wxColour( 255, 215, 0 ) ); // Gold
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_ATTRIBUTEUNKNOWN, new wxColour( 189, 183, 107 ) ); // DarkKhaki
				$this->wxstyledtextctrl_mainframe_editor->StyleSetForeground( wxSTC_H_COMMENT, new wxColour( 188, 14, 143 ) ); // RosyBrown
				
										
				$this->website_project->file_to_edit["type"] = "TEMPLATE";
				$this->website_project->file_to_edit["name"] = $selection_string;

				$this->wxstyledtextctrl_mainframe_editor->ClearAll();

				/* TODO:  Here we have to read from the files-buffer to the editor!!! */
				/* TODO:  OR! Here we have to read from the harddisk to the files-buffer!!! */
				if( !$this->website_project->exists_files_buffer(
					$this->website_project->file_to_edit["name"],
					$this->website_project->file_to_edit["type"] ) )
				{
					// Read from the harddisk to the files-buffer for the first and only time!
					$this->website_project->read_files_buffer_content_from_harddisk();
				}
				
				// Read from the files-buffer.
				$new_content = $this->website_project->read_file_contents_from_files_buffer(
						$this->website_project->file_to_edit["name"],
						$this->website_project->file_to_edit["type"]
				);
				$this->wxstyledtextctrl_mainframe_editor->SetText(
					$new_content
					// $this->website_project->read_content()
				);
				
				$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->UnselectAll();
				$this->wxlistbox_mainframe_cssfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_javascriptfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_downloads->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_media->SetSelection( wxNOT_FOUND );
				$this->wxbutton_mainframe_editorsave->Enable( TRUE );
				$this->wxstyledtextctrl_mainframe_editor->Enable( TRUE );
				
				$this->website_project->file_to_edit_original = $new_content;
				break;
			}
			case DILL2_WXID_MAINFRAME_WXLISTBOX_MEDIA:
			{
				$this->wxlistbox_mainframe_downloads->SetSelection( wxNOT_FOUND );			
				$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->UnselectAll();
				$this->wxlistbox_mainframe_cssfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_javascriptfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_templates->SetSelection( wxNOT_FOUND );

				/* TODO:  Here we have to write from the editor to the files-buffer!!! */
				$this->wxstyledtextctrl_mainframe_editor->Disable();
				$this->wxbutton_mainframe_editorsave->Enable( FALSE );
				$this->wxstyledtextctrl_mainframe_editor->Enable( FALSE );
				
				$this->website_project->file_to_edit_original = NULL;
				
				// Set clipboard content.
				$this->set_clipboard_text( DILL2_CORE_WEBSITE_PROJECT_MEDIA_DIR );
				
				// Display the image in the preview image box.
				$this->prepare_preview_image(
					$this->wxlistbox_mainframe_media->GetString(
						$this->wxlistbox_mainframe_media->GetSelection()
					)
				);
				
				break;
			}
			case DILL2_WXID_MAINFRAME_WXLISTBOX_DOWNLOADS:
			{
				$this->wxlistbox_mainframe_media->SetSelection( wxNOT_FOUND );		
				$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->UnselectAll();
				$this->wxlistbox_mainframe_cssfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_javascriptfiles->SetSelection( wxNOT_FOUND );
				$this->wxlistbox_mainframe_templates->SetSelection( wxNOT_FOUND );

				/* TODO:  Here we have to write from the editor to the files-buffer!!! */
				$this->wxstyledtextctrl_mainframe_editor->Disable();
				$this->wxbutton_mainframe_editorsave->Enable( FALSE );
				$this->wxstyledtextctrl_mainframe_editor->Enable( FALSE );
				
				$this->website_project->file_to_edit_original = NULL;
				
				// Set clipboard content.
				$this->set_clipboard_text( DILL2_CORE_WEBSITE_PROJECT_DOWNLOAD_DIR );
				
				break;
			}
		}	
	} 
	
	
	public function update_selected_item_content()
	{		
		$this->website_project->write_content(
			$this->wxstyledtextctrl_mainframe_editor->GetText()
		);
		$this->website_project->file_to_edit_original = NULL;
		$this->mark_item_as_saved();
	}
	
	
	public function on_dill2_mainframe_wxmenu_project_generate_website_clicked()
	{
		// Create instance of generic progress bar.
		// And pass to it the website project instance "$this->website_project".
		// For fetch the amount of pages to generate.
		// And to register "$this->website_project" for the callback function.
		$generate_website_view = new GenerateWebsite_View(
			$this,
			$this->website_project);
		
		// Run generation.
		$this->website_project->generate_website();
		
		// Show a success message.
		$info_dialog = new wxMessageDialog(
			$this,
			"The website was generated successfully.",
			"Website creation successful");
			
		$info_dialog->ShowModal();
	}
	
	
	public function on_dill2_mainframe_wxmenu_start_testserver_clicked()
	{
		/* This function starts the testserver.
		
		*/
		// How can we access our website?
		$website_project_settings_array = $this->website_project->db_select(
			"website_project_settings"
		);

        switch( php_uname('s'))
        {
        	case "Windows NT":
        	{
				// For Windows:
				$this->process = proc_open(
					sprintf(
						"start /B php -S %s:%d",
						$website_project_settings_array[0]["testserver_address"],
						$website_project_settings_array[0]["testserver_port"]
					),
					array(
						0 => array( "pipe", "r" ),
						1 => array( "pipe", "w" )
					),
					$this->website_project->testserver_pipes,
					$this->website_project->abspath_websiteproject_website
				);
				break;   	
        	}
        	default:
        	{
				// For Linux:
				$this->process = proc_open(
					sprintf(
						"exec php -S %s:%d",
						$website_project_settings_array[0]["testserver_address"],
						$website_project_settings_array[0]["testserver_port"]
					),
					array(
						0 => array( "pipe", "r" ),
						1 => array( "pipe", "w" )
					),
					$this->website_project->testserver_pipes,
					$this->website_project->abspath_websiteproject_website
				);     	
        	}
        } 		
		if( is_resource( $this->process ) )
		{
			$this->wxmenu_mainframe_project->Enable(
				DILL2_WXID_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER,
				FALSE
			);

			$this->wxmenu_mainframe_project->Enable(
				DILL2_WXID_MAINFRAME_WXMENU_PROJECT_STOP_TESTSERVER,
				TRUE
			);		
		}
	}
	
	
	public function on_dill2_mainframe_wxmenu_stop_testserver_clicked()
	{
		/* This function stops the testserver.
		
		*/
        switch( php_uname('s'))
        {
        	// For Windows.
        	case "Windows NT":
        	{
				if( is_resource( $this->process ) )
				{        	
					$ppid = proc_get_status( $this->process )["pid"];
					$output = array_filter(
						explode(
							" ",
							shell_exec("wmic process get parentprocessid,processid | find \"$ppid\"")
						)
					);
					array_pop($output);
					$pid = end($output);

					exec( sprintf( "TASKKILL /F /PID %s", $pid ) );
					fclose( $this->website_project->testserver_pipes[0] );
					fclose( $this->website_project->testserver_pipes[1] );
					proc_close( $this->process );
				}      
				break;  	
        	}
        	// For linux.
        	default:
        	{
				if( is_resource( $this->process ) )
				{
					$pid = proc_get_status( $this->process )["pid"];
					exec( "kill -15 $pid" );
					fclose( $this->website_project->testserver_pipes[0] );
					fclose( $this->website_project->testserver_pipes[1] );
					proc_close( $this->process );
				}        	
        	}
        }
		$this->wxmenu_mainframe_project->Enable(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER,
			TRUE
		);		

		$this->wxmenu_mainframe_project->Enable(
			DILL2_WXID_MAINFRAME_WXMENU_PROJECT_STOP_TESTSERVER,
			FALSE
		);        		
	}
	

	public function on_dill2_mainframe_wxmenu_create_backup_clicked()
	{
		/* This function opens a dialog where the user can choose a location to
		save the current website project as a .tar file as a backup.
		
		*/
		$dialog = new wxDirDialog(
			$this,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTCREATEBACKUP_WXSTATICTEXT_MESSAGE
		);
		
		if( $dialog->ShowModal() == wxID_OK )
		{
			$chosen_directory = $dialog->GetPath();
			$archivename = sprintf(
				"%s_%s",
				date(
					"Y-m-d-H-i-s"
				),
				$this->website_project->project_name
			);
			
			chdir("..");
			
			$srcpath = getcwd() . DIRECTORY_SEPARATOR . "website_projects" . DIRECTORY_SEPARATOR . $this->website_project->project_name;
			$dstpath = $chosen_directory . DIRECTORY_SEPARATOR . $archivename;
			
			mkdir($dstpath);
			
			copy_entire_tree($srcpath,
						  $dstpath);
			chdir("bin");
		}
	}
	
	
	public function on_dill2_wxid_mainframe_wxmenu_file_import_backup_clicked()
	{
		/* This function opens a dialog to choose a backup to import and imports
		the backup as a project if a project with the same name does not yet exist.
		
		*/
		$dialog = new wxDirDialog(
			$this,
			DILL2_TEXT_WXDIALOGIMPORTBACKUP_MESSAGE
		);
		
		if( $dialog->ShowModal() == wxID_OK )
		{
			// Create an array containing all website project names.
			$website_project_names = array_diff(
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
		
		
			$srcpath = $dialog->GetPath();
			
			$archive_project_name = basename($srcpath);
			$archive_project_name = substr($archive_project_name,
									 strpos($archive_project_name, "_") + 1);
			
			if( !in_array( $archive_project_name, $website_project_names ) )
			{
				chdir("..");
				
				$dstpath = getcwd() . DIRECTORY_SEPARATOR . "website_projects" . DIRECTORY_SEPARATOR . $archive_project_name;
				echo $dstpath;
				mkdir($dstpath);
				
				copy_entire_tree($srcpath,
							  $dstpath);
							  
				chdir("bin");
			}
			else
			{
				$dialog = new wxMessageDialog(
					$this,
					sprintf(
						DILL2_TEXT_WXDIALOGIMPORTBACKUP_WXMESSAGEDIALOG_MESSAGE,
						$archive_project_name,
						$archive_project_name
					),
					DILL2_TEXT_DIALOG_ERROR_CAPTION,
					wxICON_ERROR | wxOK
				);
				$dialog->ShowModal();
			}
		}
	}
	
	
	public function set_unset_state_main_window()
	{
		/*
		$this->wxstyledtextctrl_mainframe_editor->ClearAll();
		$this->wxbutton_mainframe_editorsave->Enable( FALSE );
		$this->wxstyledtextctrl_mainframe_editor->Enable( FALSE );
		$this->wxlistbox_mainframe_cssfiles->SetSelection( wxNOT_FOUND );
		$this->wxlistbox_mainframe_templates->SetSelection( wxNOT_FOUND );
		$this->wxlistbox_mainframe_javascriptfiles->SetSelection( wxNOT_FOUND );
		$this->wxlistbox_mainframe_downloads->SetSelection( wxNOT_FOUND );
		$this->wxlistbox_mainframe_media->SetSelection( wxNOT_FOUND );
		$this->wxdataviewtreectrl_mainframe_vertical_left_website_structure->UnselectAll();
		
		$this->website_project->file_to_edit_original = NULL;		
		*/
	}
	
	// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)	
	private function auto_upload_website()
	{
		// 1. We need to establish a connection to the webserver.
		// Where to connect to?  Let's fetch the ip address from SQLite.
		$settings_array = $this->website_project->db_select(
				"website_project_settings"
		);
		$webserver_ip_address = $settings_array[0]["webserver_ip_address"];
		
		// We need the username.
		$username = $settings_array[0]["username"];
		
		// We also need the absolute paths to the public and private key files.
		$path_publickey = $settings_array[0]["publickey"];
		$path_privatekey = $settings_array[0]["privatekey"];

		// Let's try to establish the connection.
		$ssh_conn = ssh2_connect( $webserver_ip_address,
								  22,
								  array( 'hostkey' => 'ssh-rsa') );
		if( $ssh_conn != FALSE )
		{
			// A connection to the web-server has now been established.
			// Accecpt the fingerprint automatically.
			ssh2_fingerprint( $ssh_conn );
			
			// Authenticate using a public and private key file.	  
			if (ssh2_auth_pubkey_file( $ssh_conn,
								       $username,
									   $path_publickey,
									   $path_privatekey ))

			{
				// And now it is time to upload the website to the webserver.
				// We change the current directory on the local computer and
				// on the webserver aswell.

				// Local computer:
				chdir( ".." );					
				$abspath_website = getcwd() . DIRECTORY_SEPARATOR . "website_projects" . DIRECTORY_SEPARATOR . $this->website_project->project_name . DIRECTORY_SEPARATOR . "website";
				chdir( $abspath_website );

				// webserver:
				$webserver_abspath = $settings_array[0]["webserver_path"];

				// Now let's upload the website.
				$ssh2_sftp = ssh2_sftp( $ssh_conn );

				$this->Enable( FALSE );

				$this->upload_sync(
					$ssh_conn,
					$ssh2_sftp,
					$abspath_website,
					$webserver_abspath
				);

				chdir( "../../../bin" );

				// Upload finished, inform the user.
				$wxdialog_uploaddone = new wxMessageDialog(
					$this,
					"The website has been uploaded.",
					"Website upload finished",
					wxICON_INFORMATION
				);
				$this->Enable( TRUE );
				$wxdialog_uploaddone->ShowModal();				
						
				ssh2_exec( $ssh_conn, "exit" );
				unset( $ssh_conn );
			}
		}	
		else
		{
			ssh2_exec( $ssh_conn, "exit" );
			unset( $ssh_conn );			
		}
	}
	// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)	


	public function on_upload_website_sftp()
	{
		$view = new UploadWebsiteSFTP_View($this);			
		$model = new UploadWebsiteSFTP_Model($this->website_project);		
		$logic = new UploadWebsiteSFTP_Logic();			
		$presenter = new UploadWebsiteSFTP_Presenter(
			$view,
			$model,
			$logic);
		
		$presenter->run();
	}
	
	/*
	Event handler method that is called when the menu item to upload a website
	over FTPS has been clicked.
	*/
	public function on_dill2_wxid_mainframe_wxmenu_project_upload_website_ftps_selected()
	{
		$view = new UploadWebsiteFTPS_View($this);
		$model = new UploadWebsiteFTPS_Model($this->website_project);
		$logic = new UploadWebsiteFTPS_Logic();
		$presenter = new UploadWebsiteFTPS_Presenter(
			$view,
			$model,
			$logic);
		
		$presenter->run();
	}


	public function recursive_upload( $ssh2_conn, $ssh2_sftp, $localpath, $remotepath )
	{
		$dirit = new DirectoryIterator( $localpath );
		foreach( $dirit as $fileinfo )
		{
			if( $fileinfo->isDir() && !$fileinfo->isDot())
			{
				// Create the directory on the remote server.
				// Even if it does exist.
				$new_remote_dir_path = $remotepath . "/" . $fileinfo->getFilename();
				ssh2_sftp_mkdir( $ssh2_sftp, $new_remote_dir_path );
				$this->recursive_upload(
					$ssh2_conn,
					$ssh2_sftp,
					$localpath . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
					$new_remote_dir_path
				);
			}
			else if( $fileinfo->isFile())
			{
				// Upload the file using scp.
				ssh2_scp_send(
					$ssh2_conn,
					$localpath . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
					$remotepath . "/" . $fileinfo->getFilename()
				);
			}
		}
	}


	public function mark_item_as_edited()
	{
		// For the website structure
		$page_element = $this->wxdataviewtreectrl_mainframe_vertical_left_website_structure;
		$wxdataviewtreectrl_selection = $page_element->GetSelection();
		
		if( $wxdataviewtreectrl_selection->IsOk() )
		{
			$element_id = $page_element->GetItemData(
				$wxdataviewtreectrl_selection
			)->element_id;

			if( !$this->item_already_edited_check( $element_id, "PAGE" ) )
			{
				$this->item_already_edited_add( $element_id, "PAGE" );
				$page_element->SetItemText(
					$wxdataviewtreectrl_selection,
					"*" .
					$page_element->GetItemText(
						$wxdataviewtreectrl_selection
					)
				);
			}
		}

		// For the templates.
		// $this->wxlistbox_mainframe_templates
		$page_element = $this->wxlistbox_mainframe_templates;
		$wxlistbox_selection = $page_element->GetSelection();
		
		if( $wxlistbox_selection != wxNOT_FOUND )
		{
			$element_id = $page_element->GetString(
				$wxlistbox_selection
			);

			if( !$this->item_already_edited_check( $element_id, "TEMPLATE" ) )
			{
				$this->item_already_edited_add( "*" . $element_id, "TEMPLATE" );
				$page_element->SetString(
					$wxlistbox_selection,
					"*" . $element_id
				);
			}
		}
		
		// $this->wxlistbox_mainframe_cssfiles
		$page_element = $this->wxlistbox_mainframe_cssfiles;
		$wxlistbox_selection = $page_element->GetSelection();
		
		if( $wxlistbox_selection != wxNOT_FOUND )
		{
			$element_id = $page_element->GetString(
				$wxlistbox_selection
			);

			if( !$this->item_already_edited_check( $element_id, "CSS" ) )
			{
				$this->item_already_edited_add( "*" . $element_id, "CSS" );
				$page_element->SetString(
					$wxlistbox_selection,
					"*" . $element_id
				);
			}
		}
				
		// $this->wxlistbox_mainframe_javascriptfiles
		$page_element = $this->wxlistbox_mainframe_javascriptfiles;
		$wxlistbox_selection = $page_element->GetSelection();
		
		if( $wxlistbox_selection != wxNOT_FOUND )
		{
			$element_id = $page_element->GetString(
				$wxlistbox_selection
			);

			if( !$this->item_already_edited_check( $element_id, "JS" ) )
			{
				$this->item_already_edited_add( "*" . $element_id, "JS" );
				$page_element->SetString(
					$wxlistbox_selection,
					"*" . $element_id
				);
			}
		}


		$this->activate_deactive_wp_menu_item();			
	}


	public function item_already_edited_check( $id, $type )
	{
		for( $x = 0; $x < count( $this->website_project->item_already_edited ); $x++ )
		{
			if( $this->website_project->item_already_edited[$x]["id"] == $id &&
				$this->website_project->item_already_edited[$x]["type"] == $type )
			{
				return TRUE;
			}
		}
		return FALSE;
	}


	public function item_already_edited_add( $id, $type )
	{
		$this->website_project->item_already_edited[] = array(
			"id" => $id,
			"type" => $type
		);
	}	


	public function item_already_edited_remove( $id, $type )
	{
		$new_arr = array();
		for( $x = 0; $x < count( $this->website_project->item_already_edited ); $x++ )
		{
			if( $this->website_project->item_already_edited[$x]["id"] == $id &&
				$this->website_project->item_already_edited[$x]["type"] == $type )
			{
			}
			else
			{
				$new_arr[] = $this->website_project->item_already_edited[$x];
			}
		}

		$this->website_project->item_already_edited = $new_arr;
	}


	public function item_already_edited_rename( $id, $type, $id_new )
	{
		for( $x = 0; $x < count( $this->website_project->item_already_edited ); $x++ )
		{
			if( $this->website_project->item_already_edited[$x]["id"] == $id &&
				$this->website_project->item_already_edited[$x]["type"] == $type )
			{
				$this->website_project->item_already_edited[$x]["id"] = $id_new;
				return;
			}
		}
	}	


	public function mark_item_as_saved()
	{
		// For the website structure
		$page_element = $this->wxdataviewtreectrl_mainframe_vertical_left_website_structure;
		$wxdataviewtreectrl_selection = $page_element->GetSelection();
		if( $wxdataviewtreectrl_selection->IsOk() )
		{
			$element_id = $page_element->GetItemData(
				$wxdataviewtreectrl_selection
			)->element_id;


			if( $this->item_already_edited_check( $element_id, "PAGE" ) )
			{
				$this->item_already_edited_remove( $element_id, "PAGE" );
				$page_element->SetItemText(
					$wxdataviewtreectrl_selection,
					substr(
						$page_element->GetItemText(
							$wxdataviewtreectrl_selection
						),
						1
					)
				);
			}
		}

		// For the templates.
		// $this->wxlistbox_mainframe_templates
		$page_element = $this->wxlistbox_mainframe_templates;
		$wxlistbox_selection = $page_element->GetSelection();
		
		if( $wxlistbox_selection != wxNOT_FOUND )
		{
			$element_id = $page_element->GetString(
				$wxlistbox_selection
			);

			if( $this->item_already_edited_check( $element_id, "TEMPLATE" ) )
			{
				$this->item_already_edited_remove( $element_id, "TEMPLATE" );
				$page_element->SetString(
					$wxlistbox_selection,
					substr( $element_id, 1 )
				);
			}
		}

		// $this->wxlistbox_mainframe_cssfiles
		$page_element = $this->wxlistbox_mainframe_cssfiles;
		$wxlistbox_selection = $page_element->GetSelection();
		
		if( $wxlistbox_selection != wxNOT_FOUND )
		{
			$element_id = $page_element->GetString(
				$wxlistbox_selection
			);

			if( $this->item_already_edited_check( $element_id, "CSS" ) )
			{
				$this->item_already_edited_remove( $element_id, "CSS" );			
				$page_element->SetString(
					$wxlistbox_selection,
					substr( $element_id, 1 )
				);
			}
		}
				
		// $this->wxlistbox_mainframe_javascriptfiles
		$page_element = $this->wxlistbox_mainframe_javascriptfiles;
		$wxlistbox_selection = $page_element->GetSelection();
		
		if( $wxlistbox_selection != wxNOT_FOUND )
		{
			$element_id = $page_element->GetString(
				$wxlistbox_selection
			);

			if( $this->item_already_edited_check( $element_id, "JS" ) )
			{
				$this->item_already_edited_remove( $element_id, "JS" );			
				$page_element->SetString(
					$wxlistbox_selection,
					substr( $element_id, 1 )
				);
			}
		}


		$this->activate_deactive_wp_menu_item();
	}


	public function activate_deactive_wp_menu_item()
	{
		$n_css_files = $n_js_files = $n_ws_items = $n_template_files = 0;
		
		for( $x = 0; $x < count( $this->website_project->item_already_edited ); $x++ )
		{
			switch( $this->website_project->item_already_edited[$x]["type"] )
			{
				case "CSS":
					$n_css_files += 1;
					break;

				case "JS":
					$n_js_files += 1;
					break;

				case "TEMPLATE":
					$n_template_files += 1;
					break;

				case "PAGE":
					$n_ws_items += 1;
					break;
			}
		}

		
		if( $n_css_files > 0 )
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES, FALSE );		
		}
		else
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES, TRUE );		
		}

		if( $n_js_files > 0 )
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES, FALSE );		
		}
		else
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES, TRUE );		
		}

		if( $n_template_files > 0 )
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES, FALSE );		
		}
		else
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES, TRUE );		
		}

		if( $n_ws_items > 0 )
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE, FALSE );		
		}
		else
		{
			$this->wxmenubar_mainframe_mainmenu->Enable( DILL2_WXID_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE, TRUE );		
		}						
	}


	public function upload_sync( $ssh2_conn, $sftp_conn, $src, $dst )
	{
		$dirs_and_files_created_or_updated_or_existing = array();
	
		$dirit = new DirectoryIterator( $src );
		foreach( $dirit as $fileinfo )
		{
			if( $fileinfo->isDot())
			{
				continue;
			}
			if( $fileinfo->isDir())
			{
				$new_dir_path = $dst . "/" . $fileinfo->getFilename();
				ssh2_sftp_mkdir( $sftp_conn, $new_dir_path );
				$this->upload_sync(	
					$ssh2_conn,
					$sftp_conn,
					$src . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
					$new_dir_path
				);

				$dirs_and_files_created_or_updated_or_existing[] = $new_dir_path;
			
			}
			else if( $fileinfo->isFile())
			{
				// Compare checksums so we know if we need to copy / replace the file or not.
				$local_file_md5sum = calculate_md5_checksum($src . DIRECTORY_SEPARATOR . $fileinfo->getFilename());

				$remote_file_stream = ssh2_exec(
					$ssh2_conn,
					sprintf(
						"md5sum %s",
						$dst . "/" . $fileinfo->getFilename()
					)
				);
				stream_set_blocking( $remote_file_stream, TRUE );
				$remote_file_md5sum = explode(
					" ",
					stream_get_contents(
						$remote_file_stream
					)
				)[0];
				fclose( $remote_file_stream );

				if( $local_file_md5sum !== $remote_file_md5sum )
				{
					echo sprintf( "Sending %s\n", $src . DIRECTORY_SEPARATOR . $fileinfo->getFilename() ) . PHP_EOL;
					ssh2_scp_send(
						$ssh2_conn,
						$src . DIRECTORY_SEPARATOR . $fileinfo->getFilename(),
						$dst . "/" . $fileinfo->getFilename()
					);		
				}
			
				$dirs_and_files_created_or_updated_or_existing[] = $dst . "/" . $fileinfo->getFilename();
						
			}
		}
		//$dirs_and_files_created_or_updated_or_existing[] = $dst;
		//print_r( $dirs_and_files_created_or_updated_or_existing );

		// Delete directories and files inside the current directory.
		// Get an array of all remote directories and files of the current $dst.
		$stream1 = ssh2_exec(
			$ssh2_conn,
			sprintf(
				"dir -m %s",
				$dst
			)
		);
		stream_set_blocking( $stream1, TRUE );
		$remote_dirs_and_files_raw = explode(
			",",
			stream_get_contents(
				$stream1
			)
		);
		fclose( $stream1 );

		$remote_dirs_and_files = array();

		foreach( $remote_dirs_and_files_raw as $thing )
		{
			if( strlen( trim( $thing ) )  > 0 )
			{
				$remote_dirs_and_files[] = $dst . "/" . trim( $thing );
			}
		}

		//$remote_dirs_and_files[] = $dst;

		$remote_dirs_and_files_to_delete_recursively = array_diff(
			$remote_dirs_and_files,
			$dirs_and_files_created_or_updated_or_existing
		);

		foreach( $remote_dirs_and_files_to_delete_recursively as $d )
		{
			if( ssh2_exec(
				$ssh2_conn,
				sprintf(
					"rm -r %s",
					$d
				)
			) )
			{
				echo sprintf( "Directory/File %s has been deleted.", $d ) . PHP_EOL;	
			}	
		}	
	}
	
	
	/* This function sets the content of the clipboard to a relative path
	consisting of predefined strings (representing a directory) and a chosen
	wxListBox element string.
	
	Parameters:
		Type:  string
		Name:  $_relative_directory
	
	Returns 'TRUE' if successfully set.  Otherwise FALSE.
	*/
	public function set_clipboard_text( $_relative_directory )
	{
		switch( $_relative_directory )
		{
			case DILL2_CORE_WEBSITE_PROJECT_MEDIA_DIR:
				$filename = $this->wxlistbox_mainframe_media->GetString(
								$this->wxlistbox_mainframe_media->GetSelection()
							);
				break;
			
			case DILL2_CORE_WEBSITE_PROJECT_DOWNLOAD_DIR:
				$filename = $this->wxlistbox_mainframe_downloads->GetString(
								$this->wxlistbox_mainframe_downloads->GetSelection()
							);
				break;
		}
		
		// User only has to paste the relative path of the selected image.
		if( $this->wxclipboard_path->Open() )
		{
			// Copy the filepath to the clipboard.
			$this->wxclipboard_path->SetData(
				new wxTextDataObject(
					DILL2_CORE_CONSTANT_FORWARD_SLASH .
					$_relative_directory .
					DILL2_CORE_CONSTANT_FORWARD_SLASH .
					$filename
				)
			);
					
			// We are done copying data into the clipboard, close it now.
			$this->wxclipboard_path->Close();
			
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	
	/* Loads an image and prepares it to be shown in the window inside a panel
	on the right side, below the pictures wxListBox.
	
	Automtic scaling happens at the end of the function with a call to the
	function 'Refresh()'.
	
	Parameters:
		$_filename
		string
		Name of the image including the extension.
	
	Return values:
		boolean
		Returns 'TRUE' on success, otherwise an error is prolly thrown.
	*/
	public function prepare_preview_image( $_filename )
	{
		wxInitAllImageHandlers();
		
		$wxbitmap = new wxBitmap(
			$this->website_project->media_files_path .
			DIRECTORY_SEPARATOR .
			$_filename,
			wxBITMAP_TYPE_ANY
		);
		
		$w = $wxbitmap->GetWidth();
		$h = $wxbitmap->GetHeight();
		
		$wximage = $wxbitmap->ConvertToImage();
		
		if( $w > $h )
		{
			$wximage = $wximage->Scale(
				DILL2_CORE_CONSTANT_PREVIEW_IMAGE_WIDTH,
				DILL2_CORE_CONSTANT_PREVIEW_IMAGE_HEIGHT
			);
		}
		else if ( $w < $h )
		{
			$wximage = $wximage->Scale(
				DILL2_CORE_CONSTANT_PREVIEW_IMAGE_HEIGHT, // width in this case !
				DILL2_CORE_CONSTANT_PREVIEW_IMAGE_WIDTH // height in this case!
			);
		}
		else
		{
			$wximage = $wximage->Scale(
				DILL2_CORE_CONSTANT_PREVIEW_IMAGE_WIDTH,
				DILL2_CORE_CONSTANT_PREVIEW_IMAGE_WIDTH // Using same value.
			);
		}
		
		$wxbitmap = new wxBitmap(
			$wximage
		);		
		
		$this->wxstaticbitmap_picture_preview->SetBitmap(
			$wxbitmap
		);
		
		$this->wxpanel_picture_preview->Refresh();
		
		return TRUE;
	}
	
	
	/*  Event handler function for when the size of the main window changes.
	*/
	public function on_dill2_wxid_mainframe_windowsize_changed()
	{
		echo "Window size has changed";
	}
}
?>
