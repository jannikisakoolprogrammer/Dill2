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


class wxManageWebsiteProjectSettingsDialog extends wxDialog
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
		
		$this->website_project = $website_project;
		
		$settings_array = $this->website_project->db_select(
			"website_project_settings"
		);
		if( count( $settings_array ) == 0 )
		{
			$this->website_project->db_insert(
				"website_project_settings",
				array(
					"website_project_title",
					"testserver_address",
					"testserver_port",
					"webserver_path",
					"webserver_ip_address",
					// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
					"username",
					"publickey",
					"privatekey",
					"auto_upload"
					// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
				),
				array(
					"",
					"localhost",
					8080,
					"/var/www",
					"",
					// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
					"",
					"",
					"",
					0
					// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
				),
				array(
					SQLITE3_TEXT,
					SQLITE3_TEXT,
					SQLITE3_INTEGER,
					SQLITE3_TEXT,
					SQLITE3_TEXT,
					// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
					SQLITE3_TEXT,
					SQLITE3_TEXT,
					SQLITE3_TEXT,
					SQLITE3_INTEGER
					// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
				)
			);
			
			$settings_array = $this->website_project->db_select(
				"website_project_settings"
			);
		}
		
		
		// Controls.
		$this->wxpanel = new wxPanel(
			$this,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXPANEL
		);
		
		$this->wxboxsizer_vertical_parent = new wxBoxSizer(
			wxVERTICAL
		);
		$this->wxboxsizer_horizontal_row1 = new wxBoxSizer(
			wxHORIZONTAL
		);
		$this->wxboxsizer_horizontal_row2 = new wxBoxSizer(
			wxHORIZONTAL
		);
		$this->wxboxsizer_horizontal_row3 = new wxBoxSizer(
			wxHORIZONTAL
		);
		$this->wxboxsizer_horizontal_row4 = new wxBoxSizer(
			wxHORIZONTAL
		);
		$this->wxboxsizer_horizontal_row5 = new wxBoxSizer(
			wxHORIZONTAL
		);
		$this->wxboxsizer_horizontal_row6 = new wxBoxSizer(
			wxHORIZONTAL
		);
		
		// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
		// For the username.
		$this->wxboxsizer_horizontal_row7 = new wxBoxSizer(
			wxHORIZONTAL
		);
		
		// For the public key.
		$this->wxboxsizer_horizontal_row8 = new wxBoxSizer(
			wxHORIZONTAL
		);
		
		// For the private key.
		$this->wxboxsizer_horizontal_row9 = new wxBoxSizer(
			wxHORIZONTAL
		);
				
		// For the 'auto_upload' checkbox.
		$this->wxboxsizer_horizontal_row10 = new wxBoxSizer(
			wxHORIZONTAL
		);
		// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
		
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row1,
			1,
			wxEXPAND | wxALL,
			10
		);
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row2,
			1,
			wxEXPAND | wxALL,
			10
		);
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row3,
			1,
			wxEXPAND | wxALL,
			10
		);
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row5,
			1,
			wxEXPAND | wxALL,
			10
		);
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row6,
			1,
			wxEXPAND | wxALL,
			10
		);
		
		// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row7,
			1,
			wxEXPAND | wxALL,
			10
		);
		
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row8,
			1,
			wxEXPAND | wxALL,
			10
		);
		
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row9,
			1,
			wxEXPAND | wxALL,
			10
		);
		
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row10,
			1,
			wxEXPAND | wxALL,
			10
		);		
		// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
		
		$this->wxboxsizer_vertical_parent->Add(
			$this->wxboxsizer_horizontal_row4,
			1,
			wxEXPAND | wxALL,
			10
		);
		
		
		// Option "Website title".
		$this->wxstatictext_websitetitle = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETITLE,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETITLE
		);
		$this->wxboxsizer_horizontal_row1->Add(
			$this->wxstatictext_websitetitle,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		$this->wxtextctrl_websitetitle = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXTEXTCTRL_WEBSITETITLE,
			$settings_array[0]["website_project_title"]
		);
		$this->wxboxsizer_horizontal_row1->Add(
			$this->wxtextctrl_websitetitle,
			1,
			wxEXPAND | wxALIGN_TOP
		);
		
		// Option "Website testserver address"
		$this->wxstatictext_websitetestserver_address = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETESTSERVER_ADDRESS,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETESTSERVER_ADDRESS
		);
		$this->wxboxsizer_horizontal_row2->Add(
			$this->wxstatictext_websitetestserver_address,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		$this->wxtextctrl_websitetestserver_address = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXTEXTCTRL_WEBSITETESTSERVER_ADDRESS,
			$settings_array[0]["testserver_address"]
		);
		$this->wxboxsizer_horizontal_row2->Add(
			$this->wxtextctrl_websitetestserver_address,
			1,
			wxEXPAND | wxALIGN_TOP
		);
		
		// Option "Website testserver port"
		$this->wxstatictext_websitetestserver_port = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETESTSERVER_PORT,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSITETESTSERVER_PORT
		);
		$this->wxboxsizer_horizontal_row3->Add(
			$this->wxstatictext_websitetestserver_port,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		$this->wxtextctrl_websitetestserver_port = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXTEXTCTRL_WEBSITETESTSERVER_PORT,
			$settings_array[0]["testserver_port"]
		);
		$this->wxboxsizer_horizontal_row3->Add(
			$this->wxtextctrl_websitetestserver_port,
			1,
			wxEXPAND | wxALIGN_TOP
		);

		// Option "Webserver path"
		$this->wxstatictext_upload_website = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_UPLOAD_WEBSITE,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_UPLOAD_WEBSITE
		);
		$this->wxboxsizer_horizontal_row5->Add(
			$this->wxstatictext_upload_website,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		$this->wxtextctrl_upload_website = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXTEXTCTRL_UPLOAD_WEBSITE,
			$settings_array[0]["webserver_path"]
		);
		$this->wxboxsizer_horizontal_row5->Add(
			$this->wxtextctrl_upload_website,
			1,
			wxEXPAND | wxALIGN_TOP
		);

		// Option "Webserver IP address
		$this->wxstatictext_webserver_ip_address = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSERVER_IP_ADDRESS,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_WEBSERVER_IP_ADDRESS
		);
		$this->wxboxsizer_horizontal_row6->Add(
			$this->wxstatictext_webserver_ip_address,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		$this->wxtextctrl_webserver_ip_address = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXTEXTCTRL_WEBSERVER_IP_ADDRESS,
			$settings_array[0]["webserver_ip_address"]
		);
		$this->wxboxsizer_horizontal_row6->Add(
			$this->wxtextctrl_webserver_ip_address,
			1,
			wxEXPAND | wxALIGN_TOP
		);
		
		// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
		// Option "username"
		// The static text.
		$this->wxstatictext_username = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_USERNAME,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_USERNAME
		);
		$this->wxboxsizer_horizontal_row7->Add(
			$this->wxstatictext_username,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		
		// The text box.
		$this->wxtextctrl_username = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXTEXTCTRL_USERNAME,
			$settings_array[0]["username"]
		);
		$this->wxboxsizer_horizontal_row7->Add(
			$this->wxtextctrl_username,
			1,
			wxEXPAND | wxALIGN_TOP
		);
		
		
		// Option "Public key"
		// The static text widget.
		$this->wxstatictext_publickey = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_PUBLICKEY,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_PUBLICKEY
		);
		$this->wxboxsizer_horizontal_row8->Add(
			$this->wxstatictext_publickey,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		
		// The text ctrl widget.
		$this->wxtextctrl_publickey = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXTEXTCTRL_PUBLICKEY,
			$settings_array[0]["publickey"]
		);
		$this->wxboxsizer_horizontal_row8->Add(
			$this->wxtextctrl_publickey,
			1,
			wxEXPAND | wxALIGN_TOP
		);
		
		
		// Option "Private key"
		// The static text widget.
		$this->wxstatictext_privatekey = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_PRIVATEKEY,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_PRIVATEKEY
		);
		$this->wxboxsizer_horizontal_row9->Add(
			$this->wxstatictext_privatekey,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		
		// The text ctrl widget.
		$this->wxtextctrl_privatekey = new wxTextCtrl(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXTEXTCTRL_PRIVATEKEY,
			$settings_array[0]["privatekey"]
		);
		$this->wxboxsizer_horizontal_row9->Add(
			$this->wxtextctrl_privatekey,
			1,
			wxEXPAND | wxALIGN_TOP
		);
		
		
		// Option "Auto upload"
		// The static text widget.
		$this->wxstatictext_auto_upload = new wxStaticText(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_AUTO_UPLOAD,
			DILL2_TEXT_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXSTATICTEXT_AUTO_UPLOAD
		);
		$this->wxboxsizer_horizontal_row10->Add(
			$this->wxstatictext_auto_upload,
			1,
			wxEXPAND | wxALIGN_BOTTOM | wxRIGHT,
			5
		);
		
		// The checkbox widget.
		$this->wxcheckbox_auto_upload = new wxCheckBox(
			$this->wxpanel,
			DILL2_WXID_WXMANAGEWEBSITEPROJECTSETTINGSDIALOG_WXCHECKBOX_AUTO_UPLOAD,
			""
		);
		$this->wxcheckbox_auto_upload->SetValue(
			boolval(
				$settings_array[0]["auto_upload"]
			)
		);
		
		$this->wxboxsizer_horizontal_row10->Add(
			$this->wxcheckbox_auto_upload,
			1,
			wxEXPAND | wxALIGN_TOP
		);
		// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
		
		// Cancel button.
		$this->wxbutton_cancel = new wxButton(
			$this->wxpanel,
			wxID_CANCEL,
			"Cancel"
		);
		
		// Ok button.
		$this->wxbutton_ok = new wxButton(
			$this->wxpanel,
			wxID_OK,
			"Ok"
		);
		
		$this->wxboxsizer_horizontal_row4->Add(
			$this->wxbutton_cancel,
			1,
			wxALIGN_RIGHT
		);
		$this->wxboxsizer_horizontal_row4->Add(
			$this->wxbutton_ok,
			1,
			wxALIGN_RIGHT
		);
		
		// Fit all controls into the dialog so each of them is visible and aligned.
		$this->wxpanel->SetSizer( $this->wxboxsizer_vertical_parent );
		$this->wxboxsizer_vertical_parent->SetSizeHints( $this );
	}
	
	
	public function run()
	{
		if( parent::ShowModal() == wxID_OK )
		{
			$this->website_project->db_update(
				"website_project_settings",
				array(
					"website_project_title",
					"testserver_address",
					"testserver_port",
					"webserver_path",
					"webserver_ip_address",
					// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
					"username",
					"publickey",
					"privatekey",
					"auto_upload"
					// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
				),
				array(
					$this->wxtextctrl_websitetitle->GetValue(),
					$this->wxtextctrl_websitetestserver_address->GetValue(),
					intval( $this->wxtextctrl_websitetestserver_port->GetValue(), 10 ),
					$this->wxtextctrl_upload_website->GetValue(),
					$this->wxtextctrl_webserver_ip_address->GetValue(),
					// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
					$this->wxtextctrl_username->GetValue(),
					$this->wxtextctrl_publickey->GetValue(),
					$this->wxtextctrl_privatekey->GetValue(),
					intval( $this->wxcheckbox_auto_upload->GetValue(), 10 )
					// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
				),
				array(
					SQLITE3_TEXT,
					SQLITE3_TEXT,
					SQLITE3_INTEGER,
					SQLITE3_TEXT,
					SQLITE3_TEXT,
					// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
					SQLITE3_TEXT,
					SQLITE3_TEXT,
					SQLITE3_TEXT,
					SQLITE3_INTEGER
					// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
				),
				array(
					"id",
					"=",
					1,
					SQLITE3_INTEGER
				)
			);
		}
	}
}
?>
