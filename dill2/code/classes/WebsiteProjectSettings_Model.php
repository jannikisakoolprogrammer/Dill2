<?php

require_once("ModelBase.php");

class WebsiteProjectSettings_Model extends ModelBase
{
	protected $settings_array = NULL;
	
	
	public function __construct($_website_project)
	{
		parent::__construct($_website_project);
		
		
		$this->settings_array = $this->website_project->db_select(
			"website_project_settings"
		);
		
		if( count( $this->settings_array ) == 0 )
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
			
			$this->settings_array = $this->website_project->db_select(
				"website_project_settings"
			);
		}		
	}
	
	
	public function save_settings()
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

?>