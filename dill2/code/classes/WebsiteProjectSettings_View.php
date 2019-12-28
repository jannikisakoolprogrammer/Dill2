<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxManageWebsiteProjectSettingsDialog.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the wxDialog for managing settings for a website
	project.

*******************************************************************************/


// Include required .php project files.
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"WebsiteProjectSettings_wxphp_ids.php"
);
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"WebsiteProjectSettings_lang_en.php"
);


class WebsiteProjectSettings_View extends wxFrame
{
	public function __construct(
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
		
		// Panel
		$this->wxpanel = new wxPanel($this, DILL2_WXID_WXPANEL);

		$this->wxboxsizer_vertical = new wxBoxSizer(wxVERTICAL);
		
		$this->wxboxsizer_horizontal_wxnotebook = new wxBoxSizer(wxHORIZONTAL);
		$this->wxboxsizer_horizontal_wxbuttons = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_vertical->Add(
			$this->wxboxsizer_horizontal_wxnotebook,
			1,
			wxALL | wxEXPAND,
			1);
			
		$this->wxboxsizer_vertical->Add(
			$this->wxboxsizer_horizontal_wxbuttons,
			0,
			wxALL,
			1);		
			
		
		// Place notebook inside panel now.		
		$this->wxnotebook_parent = new wxNotebook(
			$this->wxpanel,
			DILL2_WXID_WXNOTEBOOK_PARENT);
		
		// Pages
		$this->createwxNotebookPageGeneralOptions();
		$this->createwxNotebookPageSFTPOptions();
		$this->createwxNotebookPageFTPSOptions();
		
		// Now add the parent notebook to the first horizontal sizer.
		$this->wxboxsizer_horizontal_wxnotebook->Add(
			$this->wxnotebook_parent,
			1,
			wxALL,
			1);

		// Place buttons inside panel.		
		// Cancel button.
		$this->wxbutton_cancel = new wxButton(
			$this->wxpanel,
			wxID_CANCEL,
			DILL2_LABEL_WXBUTTON_CANCEL
		);
		
		// Ok button.
		$this->wxbutton_ok = new wxButton(
			$this->wxpanel,
			wxID_OK,
			DILL2_LABEL_WXBUTTON_OK
		);
		
		// Now add buttons to row.
		$this->wxboxsizer_horizontal_wxbuttons->Add(
			$this->wxbutton_ok,
			1,
			wxALIGN_LEFT
		);	
		$this->wxboxsizer_horizontal_wxbuttons->Add(
			$this->wxbutton_cancel,
			1,
			wxALIGN_LEFT
		);
		
			
		// Fit all controls into the dialog so each of them is visible and aligned.					
		$this->wxpanel->SetSizer($this->wxboxsizer_vertical);
		$this->wxboxsizer_vertical->SetSizeHints($this->wxpanel);
		$this->SetSize(
				700,
				290);		
		
	}
	
	
	protected function createwxNotebookPageGeneralOptions()
	{
		// Panel contains our widgets.
		$this->wxpanel_general_settings = new wxPanel(
			$this->wxnotebook_parent,
			DILL2_WXID_WXPANEL_GENERAL_SETTINGS);
		
		//
		// Option "Website title".
		//		
		// Label
		$this->wxstatictext_websitetitle = new wxStaticText(
			$this->wxpanel_general_settings,
			DILL2_WXID_WXSTATICTEXT_WEBSITETITLE,
			DILL2_LABEL_WXSTATICTEXT_WEBSITETITLE
		);
		
		// Textbox
		$this->wxtextctrl_websitetitle = new wxTextCtrl(
			$this->wxpanel_general_settings,
			DILL2_WXID_WXTEXTCTRL_WEBSITETITLE,
			"settings_array[0]['website_project_title']"
		);		
		
		
		//
		// Option "Website testserver address"
		//		
		// Label
		$this->wxstatictext_websitetestserver_address = new wxStaticText(
			$this->wxpanel_general_settings,
			DILL2_WXID_WXSTATICTEXT_WEBSITETESTSERVER_ADDRESS,
			DILL2_LABEL_WXSTATICTEXT_WEBSITETESTSERVER_ADDRESS
		);

		// Textbox
		$this->wxtextctrl_websitetestserver_address = new wxTextCtrl(
			$this->wxpanel_general_settings,
			DILL2_WXID_WXTEXTCTRL_WEBSITETESTSERVER_ADDRESS,
			"settings_array[0]['testserver_address']"
		);
				
				
		//
		// Option "Website testserver port"
		//		
		// Label
		$this->wxstatictext_websitetestserver_port = new wxStaticText(
			$this->wxpanel_general_settings,
			DILL2_WXID_WXSTATICTEXT_WEBSITETESTSERVER_PORT,
			DILL2_LABEL_WXSTATICTEXT_WEBSITETESTSERVER_PORT
		);
		
		// Textbox
		$this->wxtextctrl_websitetestserver_port = new wxTextCtrl(
			$this->wxpanel_general_settings,
			DILL2_WXID_WXTEXTCTRL_WEBSITETESTSERVER_PORT,
			"settings_array[0]['testserver_port']"
		);
		
		
		// Now it is time to layout the controls.
		
		//
		// Layout website title
		//
		$this->wxboxsizer_horizontal_website_title = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_horizontal_website_title->Add(
			$this->wxstatictext_websitetitle,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_website_title->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_website_title->Add(
			$this->wxtextctrl_websitetitle,
			1,
			wxEXPAND | wxALIGN_CENTRE_VERTICAL
		);
		


		//
		// Layout website test server address
		//
		$this->wxboxsizer_horizontal_website_testserver_address = new wxBoxSizer(wxHORIZONTAL);		
		
		$this->wxboxsizer_horizontal_website_testserver_address->Add(
			$this->wxstatictext_websitetestserver_address,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_website_testserver_address->Add(
			5,
			0);		
		
		$this->wxboxsizer_horizontal_website_testserver_address->Add(
			$this->wxtextctrl_websitetestserver_address,
			1,
			wxEXPAND | wxALIGN_CENTRE_VERTICAL
		);			
		
		//
		// Layout website test server port
		//
		$this->wxboxsizer_horizontal_website_testserver_port = new wxBoxSizer(wxHORIZONTAL);
		
		$this->wxboxsizer_horizontal_website_testserver_port->Add(
			$this->wxstatictext_websitetestserver_port,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_website_testserver_port->Add(
			5,
			0);			
		
		$this->wxboxsizer_horizontal_website_testserver_port->Add(
			$this->wxtextctrl_websitetestserver_port,
			1,
			wxEXPAND | wxALIGN_CENTRE_VERTICAL
		);		
		
		
		// Now put all rows in the vertical sizer.
		$this->wxboxsizer_vertical_general_settings = new wxBoxSizer(wxVERTICAL);		

		// On the first row is the setting for the website title.
		$this->wxboxsizer_vertical_general_settings->Add(
			$this->wxboxsizer_horizontal_website_title,
			0,
			wxALL | wxEXPAND,
			1);

		// On the second row is the setting for the website test server address.
		$this->wxboxsizer_vertical_general_settings->Add(
			$this->wxboxsizer_horizontal_website_testserver_address,
			0,
			wxALL | wxEXPAND,
			1);
			
		// On the third row is the setting for the website test server port.
		$this->wxboxsizer_vertical_general_settings->Add(
			$this->wxboxsizer_horizontal_website_testserver_port,
			0,
			wxALL | wxEXPAND,
			1);		
		
		
		// Now add this page to the notebook.
		$this->wxnotebook_parent->InsertPage(
			0,
			$this->wxpanel_general_settings,
			DILL2_LABEL_WXPANEL_GENERAL_SETTINGS,
			TRUE);	


		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel_general_settings->SetSizer( $this->wxboxsizer_vertical_general_settings );
		$this->wxboxsizer_vertical_general_settings->SetSizeHints( $this->wxpanel_general_settings );
	}
	
	
	protected function createwxNotebookPageSFTPOptions()
	{
		// Panel contains our widgets.
		$this->wxpanel_sftp_settings = new wxPanel(
			$this->wxnotebook_parent,
			DILL2_WXID_WXPANEL_SFTP_SETTINGS);
			
		//
		// Option "SFTP Webserver path"
		//
		// Label
		$this->wxstatictext_sftp_path_on_webserver = new wxStaticText(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXSTATICTEXT_SFTP_PATH_ON_WEBSERVER,
			DILL2_LABEL_WXSTATICTEXT_SFTP_PATH_ON_WEBSERVER
		);

		// Textbox
		$this->wxtextctrl_sftp_path_on_webserver = new wxTextCtrl(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXTEXTCTRL_SFTP_PATH_ON_WEBSERVER
		);
		
		//
		// Option "SFTP Webserver IP address"
		//
		// Label
		$this->wxstatictext_sftp_webserver_ip_address = new wxStaticText(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXSTATICTEXT_SFTP_WEBSERVER_IP_ADDRESS,
			DILL2_LABEL_WXSTATICTEXT_SFTP_WEBSERVER_IP_ADDRESS
		);

		// Textbox
		$this->wxtextctrl_sftp_webserver_ip_address = new wxTextCtrl(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXTEXTCTRL_SFTP_WEBSERVER_IP_ADDRESS
		);
		
		//
		// Option "username"
		//
		// Label
		$this->wxstatictext_sftp_username = new wxStaticText(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXSTATICTEXT_SFTP_USERNAME,
			DILL2_LABEL_WXSTATICTEXT_SFTP_USERNAME);
		
		// Textbox
		$this->wxtextctrl_sftp_username = new wxTextCtrl(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXTEXTCTRL_SFTP_USERNAME);
		
		
		//
		// Option "ssh password"
		// 
		// Label
		$this->wxstatictext_sftp_password = new wxStaticText(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXSTATICTEXT_SFTP_PASSWORD,
			DILL2_LABEL_WXSTATICTEXT_SFTP_PASSWORD);
		
		// Textbox
		$this->wxfilepickerctrl_sftp_password = new wxFilePickerCtrl(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXFILEPICKERCTRL_SFTP_PASSWORD);
		
		
		//
		// Option "Private key"
		//
		// Label
		$this->wxstatictext_sftp_privatekey = new wxStaticText(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXSTATICTEXT_SFTP_PRIVATEKEY,
			DILL2_LABEL_WXSTATICTEXT_SFTP_PRIVATEKEY);
		
		// Textbox
		$this->wxfilepickerctrl_sftp_privatekey = new wxFilePickerCtrl(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXFILEPICKERCTRL_SFTP_PRIVATEKEY);
			
			
		//
		// Option "Private key passphrase"
		//
		// Label
		$this->wxstatictext_sftp_privatekey_passphrase = new wxStaticText(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXSTATICTEXT_SFTP_PRIVATEKEY_PASSPHRASE,
			DILL2_LABEL_WXSTATICTEXT_SFTP_PRIVATEKEY_PASSPHRASE);
		
		// Textbox
		$this->wxfilepickerctrl_sftp_privatekey_passphrase = new wxFilePickerCtrl(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXFILEPICKERCTRL_SFTP_PRIVATEKEY_PASSPHRASE);						
		
		
		//
		// Option authentication method
		//
		// Label
		$this->wxstatictext_sftp_authentication_method = new wxStaticText(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXSTATICTEXT_SFTP_AUTHENTICATION_METHOD,
			DILL2_LABEL_WXSTATICTEXT_SFTP_AUTHENTICATION_METHOD);
		
		// choice control.
		$this->wxchoice_sftp_authentication_method = new wxChoice(
			$this->wxpanel_sftp_settings,
			DILL2_WXID_WXCHOICE_SFTP_AUTHENTICATION_METHOD,
			wxDefaultPosition,
			wxDefaultSize,
			[]);
		

		// Layout wxcontrols.
		// sftp webserver path.
		$this->wxboxsizer_horizontal_sftp_path_on_webserver = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_horizontal_sftp_path_on_webserver->Add(
			$this->wxstatictext_sftp_path_on_webserver,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_sftp_path_on_webserver->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_sftp_path_on_webserver->Add(
			$this->wxtextctrl_sftp_path_on_webserver,
			1,
			wxEXPAND | wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		
		// sftp webserver ip address.
		$this->wxboxsizer_horizontal_sftp_webserver_ip_address = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_horizontal_sftp_webserver_ip_address->Add(
			$this->wxstatictext_sftp_webserver_ip_address,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_sftp_webserver_ip_address->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_sftp_webserver_ip_address->Add(
			$this->wxtextctrl_sftp_webserver_ip_address,
			1,
			wxEXPAND | wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		
		// sftp ssh username.
		$this->wxboxsizer_horizontal_sftp_username = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_horizontal_sftp_username->Add(
			$this->wxstatictext_sftp_username,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_sftp_username->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_sftp_username->Add(
			$this->wxtextctrl_sftp_username,
			1,
			wxEXPAND | wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);		
		
		
		// sftp ssh password.
		$this->wxboxsizer_horizontal_sftp_password = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_horizontal_sftp_password->Add(
			$this->wxstatictext_sftp_password,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_sftp_password->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_sftp_password->Add(
			$this->wxfilepickerctrl_sftp_password,
			1,
			wxEXPAND | wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		
		// sftp ssh private key.
		$this->wxboxsizer_horizontal_sftp_privatekey = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_horizontal_sftp_privatekey->Add(
			$this->wxstatictext_sftp_privatekey,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_sftp_privatekey->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_sftp_privatekey->Add(
			$this->wxfilepickerctrl_sftp_privatekey,
			1,
			wxEXPAND | wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		
		// sftp ssh private key passphrase.
		$this->wxboxsizer_horizontal_sftp_privatekey_passphrase = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_horizontal_sftp_privatekey_passphrase->Add(
			$this->wxstatictext_sftp_privatekey_passphrase,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_sftp_privatekey_passphrase->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_sftp_privatekey_passphrase->Add(
			$this->wxfilepickerctrl_sftp_privatekey_passphrase,
			1,
			wxEXPAND | wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);		
		
		
		// sftp authentication method.
		$this->wxboxsizer_horizontal_sftp_authentication_method = new wxBoxSizer(wxHORIZONTAL);

		$this->wxboxsizer_horizontal_sftp_authentication_method->Add(
			$this->wxstatictext_sftp_authentication_method,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);
		
		// Put some horizontal space.
		$this->wxboxsizer_horizontal_sftp_authentication_method->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_sftp_authentication_method->Add(
			$this->wxchoice_sftp_authentication_method,
			1,
			wxEXPAND | wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL
		);	
		
		
		//
		// Now add all horizontal sizers to a vertical sizer.
		//
		$this->wxboxsizer_vertical_sftp_settings = new wxBoxSizer(wxVERTICAL);
		
		$this->wxboxsizer_vertical_sftp_settings->Add(
			$this->wxboxsizer_horizontal_sftp_path_on_webserver,
			0,
			wxEXPAND | wxALL,
			1);
			
		$this->wxboxsizer_vertical_sftp_settings->Add(
			$this->wxboxsizer_horizontal_sftp_webserver_ip_address,
			0,
			wxEXPAND | wxALL,
			1);
		
		$this->wxboxsizer_vertical_sftp_settings->Add(
			$this->wxboxsizer_horizontal_sftp_username,
			0,
			wxEXPAND | wxALL,
			1);
			
		$this->wxboxsizer_vertical_sftp_settings->Add(
			$this->wxboxsizer_horizontal_sftp_password,
			0,
			wxEXPAND | wxALL,
			1);			
		
		$this->wxboxsizer_vertical_sftp_settings->Add(
			$this->wxboxsizer_horizontal_sftp_privatekey,
			0,
			wxEXPAND |wxALL,
			1);
			
		$this->wxboxsizer_vertical_sftp_settings->Add(
			$this->wxboxsizer_horizontal_sftp_privatekey_passphrase,
			0,
			wxEXPAND |wxALL,
			1);		
			
		$this->wxboxsizer_vertical_sftp_settings->Add(
			$this->wxboxsizer_horizontal_sftp_authentication_method,
			0,
			wxEXPAND | wxALL,
			1);
			
		
		// Now add this page to the notebook.
		$this->wxnotebook_parent->InsertPage(
			1,
			$this->wxpanel_sftp_settings,
			DILL2_LABEL_WXPANEL_SFTP_SETTINGS,
			FALSE);	


		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel_sftp_settings->SetSizer($this->wxboxsizer_vertical_sftp_settings);
		$this->wxboxsizer_vertical_sftp_settings->SetSizeHints($this->wxpanel_sftp_settings);
	}
	
	protected function createwxNotebookPageFTPSOptions()
	{
		// Panel contains our widgets.
		$this->wxpanel_ftps_settings = new wxPanel(
			$this->wxnotebook_parent,
			DILL2_WXID_WXPANEL_FTPS_SETTINGS);
		
		$this->wxboxsizer_vertical_ftps_settings = new wxBoxSizer(wxVERTICAL);
			
		//
		// webserver path to upload to.
		//
		// Label
		$this->wxstatictext_ftps_path_on_webserver = new wxStaticText(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXSTATICTEXT_FTPS_PATH_ON_WEBSERVER,
			DILL2_LABEL_WXSTATICTEXT_FTPS_PATH_ON_WEBSERVER);
		
		// Textbox
		$this->wxtextctrl_ftps_path_on_webserver = new wxTextCtrl(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXTEXTCTRL_FTPS_PATH_ON_WEBSERVER);
			
		
		// Add control to row.
		$this->wxboxsizer_horizontal_ftps_path_on_webserver = new wxBoxSizer(wxHORIZONTAL);
		
		$this->wxboxsizer_horizontal_ftps_path_on_webserver->Add(
			$this->wxstatictext_ftps_path_on_webserver,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL);
			
		// Spacing.
		$this->wxboxsizer_horizontal_ftps_path_on_webserver->Add(
			5,
			0);
			
		$this->wxboxsizer_horizontal_ftps_path_on_webserver->Add(
			$this->wxtextctrl_ftps_path_on_webserver,
			1,
			wxEXPAND | wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL);
			
		
		// Add row to vertical row
		$this->wxboxsizer_vertical_ftps_settings->Add(
			$this->wxboxsizer_horizontal_ftps_path_on_webserver,
			0,
			wxEXPAND | wxALL,
			1);
			
			
		///
		/// FTPS webserver ip address.
		///
		$this->wxstatictext_ftps_webserver_ip_address = new wxStaticText(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXSTATICTEXT_FTPS_WEBSERVER_IP_ADDRESS,
			DILL2_LABEL_WXSTATICTEXT_FTPS_WEBSERVER_IP_ADDRESS);
			
		$this->wxtextctrl_ftps_webserver_ip_address = new wxTextCtrl(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXTEXTCTRL_FTPS_WEBSERVER_IP_ADDRESS);
		
		
		$this->wxboxsizer_horizontal_ftps_webserver_ip_address = new wxBoxSizer(wxHORIZONTAL);
		
		$this->wxboxsizer_horizontal_ftps_webserver_ip_address->Add(
			$this->wxstatictext_ftps_webserver_ip_address,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL);
		
		// Spacing
		$this->wxboxsizer_horizontal_ftps_webserver_ip_address->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_ftps_webserver_ip_address->Add(
			$this->wxtextctrl_ftps_webserver_ip_address,
			1,
			wxEXPAND | wxALIGN_LEFT |wxALIGN_CENTRE_VERTICAL);
			
		// Add to vertical row.
		$this->wxboxsizer_vertical_ftps_settings->Add(
			$this->wxboxsizer_horizontal_ftps_webserver_ip_address,
			0,
			wxEXPAND | wxALL,
			1);
			
		//
		// FTPS username.
		//
		// Label
		$this->wxstatictext_ftps_username = new wxStaticText(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXSTATICTEXT_FTPS_USERNAME,
			DILL2_LABEL_WXSTATICTEXT_FTPS_USERNAME);
		
		// Textbox
		$this->wxtextctrl_ftps_username = new wxTextCtrl(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXTEXTCTRL_FTPS_USERNAME);
		
		
		$this->wxboxsizer_horizontal_ftps_username = new wxBoxSizer(wxHORIZONTAL);
		
		$this->wxboxsizer_horizontal_ftps_username->Add(
			$this->wxstatictext_ftps_username,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL);
		
		// Spacing
		$this->wxboxsizer_horizontal_ftps_username->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_ftps_username->Add(
			$this->wxtextctrl_ftps_username,
			1,
			wxEXPAND | wxALIGN_LEFT |wxALIGN_CENTRE_VERTICAL);
			
		// Add to vertical row.
		$this->wxboxsizer_vertical_ftps_settings->Add(
			$this->wxboxsizer_horizontal_ftps_username,
			0,
			wxEXPAND | wxALL,
			1);
		
		
		//
		// FTPS password.
		//
		// Label
		$this->wxstatictext_ftps_password = new wxStaticText(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXSTATICTEXT_FTPS_PASSWORD,
			DILL2_LABEL_WXSTATICTEXT_FTPS_PASSWORD);
		
		// Textbox
		$this->wxfilepickerctrl_ftps_password = new wxFilePickerCtrl(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXFILEPICKERCTRL_FTPS_PASSWORD);
		
		
		$this->wxboxsizer_horizontal_ftps_password = new wxBoxSizer(wxHORIZONTAL);
		
		$this->wxboxsizer_horizontal_ftps_password->Add(
			$this->wxstatictext_ftps_password,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL);
		
		// Spacing
		$this->wxboxsizer_horizontal_ftps_password->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_ftps_password->Add(
			$this->wxfilepickerctrl_ftps_password,
			1,
			wxEXPAND | wxALIGN_LEFT |wxALIGN_CENTRE_VERTICAL);
			
		// Add to vertical row.
		$this->wxboxsizer_vertical_ftps_settings->Add(
			$this->wxboxsizer_horizontal_ftps_password,
			0,
			wxEXPAND | wxALL,
			1);
		
		
		//
		// Use FTP instead
		//
		// Label
		$this->wxstatictext_ftps_use_ftp = new wxStaticText(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXSTATICTEXT_FTPS_USE_FTP,
			DILL2_LABEL_WXSTATICTEXT_FTPS_USE_FTP
		);
		
		// Checkbox
		$this->wxcheckbox_ftps_use_ftp = new wxCheckBox(
			$this->wxpanel_ftps_settings,
			DILL2_WXID_WXCHECKBOX_FTPS_USE_FTP,
			"");
			
		$this->wxcheckbox_ftps_use_ftp->SetValue(
			boolval(
				0
			)
		);
		
		
		$this->wxboxsizer_horizontal_use_ftp = new wxBoxSizer(wxHORIZONTAL);
		
		$this->wxboxsizer_horizontal_use_ftp->Add(
			$this->wxstatictext_ftps_use_ftp,
			0,
			wxALIGN_LEFT | wxALIGN_CENTRE_VERTICAL);
		
		// Spacing
		$this->wxboxsizer_horizontal_use_ftp->Add(
			5,
			0);
		
		$this->wxboxsizer_horizontal_use_ftp->Add(
			$this->wxcheckbox_ftps_use_ftp,
			1,
			wxEXPAND | wxALIGN_LEFT |wxALIGN_CENTRE_VERTICAL);
			
		// Add to vertical row.
		$this->wxboxsizer_vertical_ftps_settings->Add(
			$this->wxboxsizer_horizontal_use_ftp,
			0,
			wxEXPAND | wxALL,
			1);		
		
			
		// Now add this page to the notebook.
		$this->wxnotebook_parent->InsertPage(
			2,
			$this->wxpanel_ftps_settings,
			DILL2_LABEL_WXPANEL_FTPS_SETTINGS,
			FALSE);	
			
		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel_ftps_settings->SetSizer($this->wxboxsizer_vertical_ftps_settings);
		$this->wxboxsizer_vertical_ftps_settings->SetSizeHints($this->wxpanel_ftps_settings);		
	}
}
?>
