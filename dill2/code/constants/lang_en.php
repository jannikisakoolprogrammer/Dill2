<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/constants/wxphp_ids.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains language (text) constants that are used throughout
	the entire tool.  Each constant has to begin with the following prefix:
		DILL2_TEXT_
	
	Next it is best to follow it with a part of the related ID if there is any.
	See 'wxphp_ids.php'.
	
	This is the English language file.

Example:
	DILL2_WXID_WXFRAME_MAINFRAME for the mainframe of the application.

*******************************************************************************/

// Globally used captions.
define( "DILL2_TEXT_DIALOG_ERROR_CAPTION", "Error" );
define( "DILL2_CAPTION_CONFIRM", "Confirm" );

define( "DILL2_TEXT_WXSTATICTEXT_VERTICAL_LEFT_WEBSITE_STRUCTURE", "Website structure" );
define( "DILL2_TEXT_MAINFRAME_WXSTATICTEXT_TEMPLATES", "Templates" );
define( "DILL2_TEXT_MAINFRAME_WXSTATICTEXT_CSSFILES", "CSS files" );
define( "DILL2_TEXT_MAINFRAME_WXSTATICTEXT_JAVASCRIPTFILES", "JavaScript files" );
define( "DILL2_TEXT_MAINFRAME_WXSTATICTEXT_EDITOR", "Editor" );
define( "DILL2_TEXT_MAINFRAME_WXBUTTON_EDITORSAVE", "Save" );
define( "DILL2_TEXT_MAINFRAME_WXSTATICTEXT_PICTURES", "Pictures" );
define( "DILL2_TEXT_MAINFRAME_WXSTATICTEXT_MEDIA", "Media" );
define( "DILL2_TEXT_MAINFRAME_WXSTATICTEXT_DOWNLOADS", "Downloads" );
define( "DILL2_TEXT_MAINFRAME_WXMENU_FILE", "&File" );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT", "&Project" );
define( "DILL2_TEXT_MAINFRAME_WXMENU_OPTIONS", "&Options" );
define( "DILL2_TEXT_MAINFRAME_WXMENU_HELP", "&Help" );
define( "DILL2_TEXT_MAINFRAME_WXMENU_FILE_NEW_PROJECT", "&New project" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_FILE_NEW_PROJECT", "Creates a new website project." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_FILE_OPEN_PROJECT", "&Open project" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_FILE_OPEN_PROJECT", "Opens an existing website project." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_FILE_CLOSE_DILL2", "&Exit" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_FILE_CLOSE_DILL2", "Closes Dill 2." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE", "Manage website &structure" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_WEBSITE_STRUCTURE", "Click here to manage the structure of the website." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES", "Manage &templates" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_TEMPLATES", "Click here to manage the templates of the website." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES", "Manage &CSS files" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_CSSFILES", "Click here to manage CSS files of the website." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES", "Manage &JS files" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_JSFILES", "Click here to manage JS files of the website." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_MEDIAFILES", "Manage &picture files" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_MEDIAFILES", "Click here to manage picture files of the website." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_DOWNLOADFILES", "Manage &download files" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_MANAGE_DOWNLOADFILES", "Click here to manage download files of the website." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_GENERATE_WEBSITE", "&Generate website" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_GENERATE_WEBSITE", "Click here to generate the website." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER", "Star&t testserver" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_START_TESTSERVER", "Click here to start the testserver and view the website in a webbrowser." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_STOP_TESTSERVER", "Stop testser&ver" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMEU_PROJECT_STOP_TESTSERVER", "Click here to stop the testserver." );
define( "DILL2_HELP_MAINFRAME_WXMENU_OPTIONS_SETTINGS", "&Settings" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_OPTIONS_SETTINGS", "Click here to change settings." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_HELP_HELP", "&Help" );
define( "DILL2_HELPTEXT_TEST_MAINFRAME_WXMENU_HELP_HELP", "Click here to get help." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_HELP_ABOUT", "&About" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_HELP_ABOUT", "Click here to get more information about dill2." );
define( "DILL2_TEXT_WXDIALOG_NEWPROJECT_CAPTION", "New website project" );
define( "DILL2_TEXT_WXDIALOG_OPENPROJECT_MESSAGE", "Choose a website project to open:" );
define( "DILL2_TEXT_WXDIALOG_OPENPROJECT_CAPTION", "Open an existing website project." );
define( "DILL2_TEXT_WXDIALOG_MANAGEWEBSITESTRUCTURE_TITLE", "Manage website structure" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_TITLE", "Manage website templates" );
define( "DILL2_TEXT_WXMANAGECSSFILESDIALOG_TITLE", "Manage CSS files" );
define( "DILL2_TEXT_WXMANAGEJSFILESDIALOG_TITLE", "Manage JS files" );
define( "DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_TITLE", "Manage picture files" );
define( "DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_TITLE", "Manage download files" );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_WEBSITE_PROJECT_SETTINGS", "Website project sett&ings" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_WEBSITE_PROJECT_SETTINGS", "Click here to set website project settings for the current website project." );
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_TITLE", "Manage website project settings" );
define( "DILL2_TEXT_WXMANAGEDILL2SETTINGSDIALOG_TITLE", "Manage Dill 2 settings" );
define( "DILL2_TEXT_WXHELPDIALOG_TITLE", "Dill 2 help" );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_CREATE_BACKUP", "Create backup" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_CREATE_BACKUP", "Click here to create a backup (.tar backup) of the current website project." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_FILE_IMPORT_BACKUP", "Import a backup" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_FILE_IMPORT_BACKUP", "Click here to import a backup and create a project out of it." );
define( "DILL2_TEXT_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE", "Upload website (SFTP over SSH)" );
define( "DILL2_HELPTEXT_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE", "Uploads the entire generated website to a webserver (SFTP OVER SSH)" );

// Label and help text for the context menu to upload a website to an ftps server.
define(
	"DILL2_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE_FTPS_LABEL",
	"Upload website (FTPS)");
define(
	"DILL2_MAINFRAME_WXMENU_PROJECT_UPLOAD_WEBSITE_FTPS_HELP",
	"Uploads the entire generated website to a webserver (FTPS)");

define( "DILL2_TEXT_MAINFRAME_WXSTATICTEXT_PICTURE_PREVIEW", "Picture preview" );

define( "DILL2_USERS_MANUAL_LABEL", "Dill 2 - Users manual" );
define( "DILL2_USERS_MANUAL_LINK", "" );
define( "DILL2_WEBSITE_LABEL", "Dill2 - Official project website" );
define( "DILL2_WEBSITE_LINK", "http://www.merelyajourneytowardslivingoutside.info/COMPUTER_PROGRAMMING/TOOLS/DILL_2/" );


// wxNewWebsiteProjectDialog.php
define( "DILL2_TEXT_WXNEWWEBSITEPROJECTDIALOG_WXSTATICTEXT_MESSAGE", "Please type in the name of the new website project:" );
define( "DILL2_TEXT_WXNEWWEBSITEPROJECTDIALOG_ERROR_NO_DUPLICATE_MESSAGE", "A website project '%s' already exists.  Please choose another name." );
define( "DILL2_TEXT_WXNEWWEBSITEPROJECTDIALOG_ERROR_NO_DUPLICATE_CAPTION", "Error: Website project already exists." );

// wxManageWebsiteStructureDialog.php
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT", "Add page" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_RENAMEELEMENT", "Modify page" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_DELETEELEMENT", "Delete page" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTUP", "Move page up" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTDOWN", "Move page down" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_MOVEELEMENTTREE", "Move page tree" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT_WXDIALOG_MESSAGE", "Type in a name for the new page:" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT_WXDIALOG_CAPTION", "Add a new page" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_ADDELEMENT_WXDIALOG_LABEL_TEMPLATE", "Please choose a template for the page:" );
define( "DILL2_TEXT_MANAGEWEBSITESTRUCTUREDIALOG_WXBUTTON_RENAMEELEMENT_WXDIALOG_CAPTION", "Modify a page" );

// wxManageTemplatesDialog.php
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_ADDTEMPLATE", "Add template" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE", "Rename template" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_DELETETEMPLATE", "Remove template" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_ADDTEMPLATE_DIALOG_MESSAGE", "Type in a new name for the template you wish to create:" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_ADDTEMPLATE_DIALOG_CAPTION", "Add a new template" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE_DIALOG_MESSAGE", "Type in a new name for the template you wish to rename:" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE_DIALOG_CAPTION", "Rename an existing template" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE_DIALOG_LABEL_RENAMETEMPLATE", "Type in a new name:" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_RENAMETEMPLATE_DIALOG_LABEL_CHOOSETEMPLATE", "Choose a new template for the page:" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_ASSIGNCSSFILES", "Assign CSS files" );
define( "DILL2_TEXT_WXMANAGETEMPLATESDIALOG_WXBUTTON_ASSIGNJSFILES", "Assign JS files" );

// wxManageCSSFilesDialog.php
define( "DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_ADDCSSFILE", "Add CSS file" );
define( "DILL2_TEXT_WXMANAGEFILESDIALOG_WXBUTTON_RENAMECSSFILE", "Rename CSS file" );
define( "DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_DELETECSSFILE", "Remove CSS file" );
define( "DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_ADDCSSFILE_WXDIALOG_MESSAGE", "Please type in a valid filename, including the '.css' extension.");
define( "DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_ADDCSSFILE_WXDIALOG_CAPTION", "Create and add a new CSS file" );
define( "DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_RENAMECSSFILE_WXDIALOG_MESSAGE", "Please type in a new valid filename to rename the selected file." );
define( "DILL2_TEXT_WXMANAGECSSFILESDIALOG_WXBUTTON_RENAMECSSFILE_WXDIALOG_CAPTION", "Rename an existing CSS file." );

// wxManageJSFilesDialog.php
define( "DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_ADDJSFILE", "Add JS file" );
define( "DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_RENAMEJSFILE", "Rename JS file" );
define( "DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_DELETEJSFILE", "Remove JS file" );
define( "DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_ADDJSFILE_WXDIALOG_MESSAGE", "Please type in a valid filename, including the .'js' extension." );
define( "DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_ADDJSFILE_WXDIALOG_CAPTION", "Create and add a new JS file" );
define( "DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_RENAMEJSFILE_WXDIALOG_MESSAGE", "Please type in a new valid filename to rename the selected file." );
define( "DILL2_TEXT_WXMANAGEJSFILESDIALOG_WXBUTTON_RENAMEJSFILE_WXDIALOG_CAPTION", "Rename an existing JS file." );

// wxManageMediaFilesDialog.php
define( "DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_ADDMEDIAFILE", "Add picture file" );
define( "DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_RENAMEMEDIAFILE", "Rename picture file" );
define( "DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_DELETEMEDIAFILE", "Remove picture file" );
define( "DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_ADDMEDIAFILE_WXDIALOG_MESSAGE", "Copy a picture file into the current project." );
define( "DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_RENAMEMEDIAFILE_WXDIALOG_MESSAGE", "Please type in a new valid filename to rename the selected file." );
define( "DILL2_TEXT_WXMANAGEMEDIAFILESDIALOG_WXBUTTON_RENAMEMEDIAFILE_WXDIALOG_CAPTION", "Rename an existing picture file." );

// wxManageDownloadFilesDialog.php
define( "DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_ADDDOWNLOADFILE", "Add download file" );
define( "DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_RENAMEDOWNLOADFILE", "Rename download file" );
define( "DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_DELETEDOWNLOADFILE", "Remove download file" );
define( "DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_ADDDOWNLOADFILE_WXDIALOG_MESSAGE", "Copy a download file into the current project." );
define( "DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_RENAMEDOWNLOADFILE_WXDIALOG_MESSAGE", "Please type in a new valid filename to rename the selected file" );
define( "DILL2_TEXT_WXMANAGEDOWNLOADFILESDIALOG_WXBUTTON_RENAMEDOWNLOADFILE_WXDIALOG_CAPTION", "Rename an existing download file." );

// wxManageWebsiteProjectSettingsDialog.php
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETITLE", "Website project title:" );
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETESTSERVER_ADDRESS", "Website project test server address:" );
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETESTSERVER_PORT", "Website project test server port:" );
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_UPLOAD_WEBSITE", "Upload to webserver absolute path:" );
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSERVER_IP_ADDRESS", "The IP address of the webserver:" );
// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_USERNAME", "SSH username:" );
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_PUBLICKEY", "SSH public key:" );
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_PRIVATEKEY", "SSH private key:" );
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_AUTO_UPLOAD", "Auto upload:" );
// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)


// wxCreateBackupDialog
define( "DILL2_TEXT_WXMANAGEWEBSITEPROJECTCREATEBACKUP_WXSTATICTEXT_MESSAGE", "Choose a location to create a .tar backup of the website project." );

// Import backup as project dialog.
define( "DILL2_TEXT_WXDIALOGIMPORTBACKUP_MESSAGE", "Choose the archive to import" );
define( "DILL2_TEXT_WXDIALOGIMPORTBACKUP_WXMESSAGEDIALOG_MESSAGE", "Can't import the project '%s'.  A project '%s' already exists." );

// wx dialog confirm changes ?
define( "DILL2_MESSAGE_WXMESSAGEDIALOG_CONFIRM_CHANGES", "Changes have been made to the old 'file'.  Save them before opening the new 'file' ?" );

// wxDialog for uploading the website.
define( "DILL2_TEXT_WXDIALOG_UPLOADWEBSITE_TITLE", "Upload the website to a webserver" );
define( "DILL2_TEXT_WXCREDENTIALSDIALOG_WXSTATICTEXT_USERNAME", "Username:" );
define( "DILL2_TEXT_WXCREDENTIALSDIALOG_WXSTATICTEXT_PASSWORD", "Password:" );
?>
