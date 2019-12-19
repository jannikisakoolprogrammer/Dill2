<?php

require_once("ModelBase.php");
require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"constants" . DIRECTORY_SEPARATOR .
	"WebsiteProjectSettings_constants.php"
);

class WebsiteProjectSettings_Model extends ModelBase
{
	protected $settings_array = NULL;
	
	protected $website_project_title = "";
	protected $testserver_address = "";
	protected $testserver_port = 0;
	
	protected $sftp_webserver_path = "";
	protected $sftp_webserver_ip_address = "";
	protected $sftp_username = "";
	protected $sftp_password = "";
	protected $sftp_privatekey = "";
	protected $sftp_authentication_method = "";
	
	protected $ftps_webserver_path = "";
	protected $ftps_webserver_ip_address = "";
	protected $ftps_username = "";
	protected $ftps_password = "";
	protected $ftps_use_ftp = FALSE;
	
	/// Fetches data from database table 'website_project_settings'.
	/// If no row is returned, create a row with default values and
	/// fetch data again.
	public function load_settings()
	{
		// Fetch row containing website project settings.
		$this->fetch_row();
		
		// Check length of array to see if an existing record was found.
		if ($this->have_row() == FALSE)
		{
			// Create row in table since no record was found.
			$this->create_default_row();
			
			// Fetch the just inserted row again.
			$this->fetch_row();
			
			// I presume that a record has been found.  Otherwise I would
			// have to throw an error message here and quit the program.
		}
		
		// init fields.
		$this->init_fields();
	}
	
	
	protected function fetch_row()
	{
		$this->settings_array = $this->website_project->db_select(
			"website_project_settings");
	}
	
	
	protected function have_row()
	{
		return count($this->settings_array) == 1 ? TRUE : FALSE;
	}
	
	
	/*
	Initialises member fields with default data and then inserts this data
	into the table "website_project_settings".
	*/
	protected function create_default_row()
	{
		$this->init_fields_default();
		$this->insert_row_default();
	}
	
	
	protected function init_fields_default()
	{
		$this->set_website_project_title("");
		$this->set_testserver_address("localhost");
		$this->set_testserver_port(8080);
		
		$this->set_sftp_webserver_path("/var/www");
		$this->set_sftp_webserver_ip_address("");
		$this->set_sftp_username("");
		$this->set_sftp_password("");
		$this->set_sftp_privatekey("");
		$this->set_sftp_authentication_method(DILL2_SFTP_AUTHENTICATION_METHOD_SSH); // Default is SSH
		
		$this->set_ftps_webserver_path("/var/www");
		$this->set_ftps_webserver_ip_address("");
		$this->set_ftps_username("");
		$this->set_ftps_password("");
		$this->set_ftps_use_ftp(FALSE);
	}
	
	
	protected function insert_row_default()
	{
		$this->website_project->db_insert(
			"website_project_settings",
			array(
				"website_project_title",
				"testserver_address",
				"testserver_port",
				"sftp_webserver_path",
				"sftp_webserver_ip_address",
				"sftp_username",
				"sftp_password",
				"sftp_privatekey",
				"sftp_authentication_method",
				"ftps_webserver_path",
				"ftps_webserver_ip_address",
				"ftps_username",
				"ftps_password",
				"ftps_use_ftp"
			),
			array(
				$this->get_website_project_title(),
				$this->get_testserver_address(),
				$this->get_testserver_port(),
				$this->get_sftp_webserver_path(),
				$this->get_sftp_webserver_ip_address(),
				$this->get_sftp_username(),
				$this->get_sftp_password(),
				$this->get_sftp_privatekey(),
				$this->get_sftp_authentication_method(),
				$this->get_ftps_webserver_path(),
				$this->get_ftps_webserver_ip_address(),
				$this->get_ftps_username(),
				$this->get_ftps_password(),
				$this->get_ftps_use_ftp()
			),
			array(
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_INTEGER,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_INTEGER
			)
		);		
	}
	
	
	protected function init_fields()
	{
		$this->set_website_project_title($this->settings_array[0]["website_project_title"]);
		$this->set_testserver_address($this->settings_array[0]["testserver_address"]);
		$this->set_testserver_port($this->settings_array[0]["testserver_port"]);
		
		$this->set_sftp_webserver_path($this->settings_array[0]["sftp_webserver_path"]);
		$this->set_sftp_webserver_ip_address($this->settings_array[0]["sftp_webserver_ip_address"]);
		$this->set_sftp_username($this->settings_array[0]["sftp_username"]);
		$this->set_sftp_password($this->settings_array[0]["sftp_password"]);
		$this->set_sftp_privatekey($this->settings_array[0]["sftp_privatekey"]);
		if ($this->settings_array[0]["sftp_authentication_method"] == '')		
		{
			$this->set_sftp_authentication_method(DILL2_SFTP_AUTHENTICATION_METHOD_SSH); // Default is SSH.
		}
		else
		{
			$this->set_sftp_authentication_method($this->settings_array[0]["sftp_authentication_method"]);
		}
		
		$this->set_ftps_webserver_path($this->settings_array[0]["ftps_webserver_path"]);
		$this->set_ftps_webserver_ip_address($this->settings_array[0]["ftps_webserver_ip_address"]);
		$this->set_ftps_username($this->settings_array[0]["ftps_username"]);
		$this->set_ftps_password($this->settings_array[0]["ftps_password"]);
		$this->set_ftps_use_ftp($this->settings_array[0]["ftps_use_ftp"]);
	}
	
	
	public function set_website_project_title($_website_project_title)
	{
		$this->website_project_title = $_website_project_title;
	}
	
	
	public function get_website_project_title()
	{
		return $this->website_project_title;
	}	
	
	
	public function set_testserver_address($_testserver_address)
	{
		$this->testserver_address = $_testserver_address;
	}
	
	
	public function get_testserver_address()
	{
		return $this->testserver_address;
	}	
	
	
	public function set_testserver_port($_testserver_port)
	{
		$this->testserver_port = $_testserver_port;
	}
	
	
	public function get_testserver_port()
	{
		return $this->testserver_port;
	}
	
	
	public function set_sftp_webserver_path($_sftp_webserver_path)
	{
		$this->sftp_webserver_path = $_sftp_webserver_path;
	}
	
	
	public function get_sftp_webserver_path()
	{
		return $this->sftp_webserver_path;
	}
	
	
	public function set_sftp_webserver_ip_address($_sftp_webserver_ip_address)
	{
		$this->sftp_webserver_ip_address = $_sftp_webserver_ip_address;
	}
	
	
	public function get_sftp_webserver_ip_address()
	{
		return $this->sftp_webserver_ip_address;
	}
	
	
	// Sets the sftp username
	public function set_sftp_username($_sftp_username)
	{
		$this->sftp_username = $_sftp_username;
	}
	
	// Gets the sftp username
	public function get_sftp_username()
	{
		return $this->sftp_username;
	}
	
	
	// Sets the sftp password
	public function set_sftp_password($_sftp_password)
	{
		$this->sftp_password = $_sftp_password;
	}
	
	
	// Gets the sftp password
	public function get_sftp_password()
	{
		return $this->sftp_password;
	}
	
	
	// Sets the sftp privatekey
	public function set_sftp_privatekey($_sftp_privatekey)
	{
		$this->sftp_privatekey = $_sftp_privatekey;
	}
	
	
	// Returns the sftp privatekey
	public function get_sftp_privatekey()
	{
		return $this->sftp_privatekey;
	}
	
	
	// Sets the sftp authentication method
	public function set_sftp_authentication_method($_sftp_authentication_method)
	{
		$this->sftp_authentication_method = $_sftp_authentication_method;
	}
	
	
	// Returns the sftp authentication method
	public function get_sftp_authentication_method()
	{
		return $this->sftp_authentication_method;
	}
	
	
	// Sets the ftps webserver path
	public function set_ftps_webserver_path($_webserver_path)
	{
		$this->ftps_webserver_path = $_webserver_path;
	}
	
	
	// Returns the ftps webserver path
	public function get_ftps_webserver_path()
	{
		return $this->ftps_webserver_path;
	}
	
	
	// Set the ftps ip address
	public function set_ftps_webserver_ip_address($_ftps_webserver_ip_address)
	{
		$this->ftps_webserver_ip_address = $_ftps_webserver_ip_address;
	}
	
	
	// Returns the ftps ip address
	public function get_ftps_webserver_ip_address()
	{
		return $this->ftps_webserver_ip_address;
	}
	
	
	// Set the ftps username
	public function set_ftps_username($_ftps_username)
	{
		$this->ftps_username = $_ftps_username;
	}
	
	
	// Returns the ftps username
	public function get_ftps_username()
	{
		return $this->ftps_username;
	}
	
	
	// Set the ftps password
	public function set_ftps_password($_ftps_password)
	{
		$this->ftps_password = $_ftps_password;
	}
	
	
	// Returns the ftps password
	public function get_ftps_password()
	{
		return $this->ftps_password;
	}
	
	
	// Sets whether to use ftp instead of ftps
	public function set_ftps_use_ftp($_ftps_use_ftp)
	{
		$this->ftps_use_ftp = $_ftps_use_ftp;
	}
	
	
	// Returns whether to use ftp instead of ftps
	public function get_ftps_use_ftp()
	{
		return $this->ftps_use_ftp;
	}
	
	
	public function save_settings()
	{
		$this->website_project->db_update(
			"website_project_settings",
			array(
				"website_project_title",
				"testserver_address",
				"testserver_port",
				"sftp_webserver_path",
				"sftp_webserver_ip_address",
				"sftp_username",
				"sftp_password",
				"sftp_privatekey",
				"sftp_authentication_method",
				"ftps_webserver_path",
				"ftps_webserver_ip_address",
				"ftps_username",
				"ftps_password",
				"ftps_use_ftp"
			),
			array(
				$this->get_website_project_title(),
				$this->get_testserver_address(),
				$this->get_testserver_port(),
				$this->get_sftp_webserver_path(),
				$this->get_sftp_webserver_ip_address(),
				$this->get_sftp_username(),
				$this->get_sftp_password(),
				$this->get_sftp_privatekey(),
				$this->get_sftp_authentication_method(),
				$this->get_ftps_webserver_path(),
				$this->get_ftps_webserver_ip_address(),
				$this->get_ftps_username(),
				$this->get_ftps_password(),
				$this->get_ftps_use_ftp()
			),
			array(
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_INTEGER,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_INTEGER
			),
			array(
				"id",
				"=",
				1,
				SQLITE3_INTEGER));
	}
}

?>