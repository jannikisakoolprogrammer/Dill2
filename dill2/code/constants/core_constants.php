<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/constants/core_constants.php

Author:  Jannik Haberbosch

Year: 2014

Info:  Provides core constants for the tool.  Every constant begins
	with a 'DILL2_CORE_CONSTANT_' prefix.


*******************************************************************************/

// General information about the tool.
define( "DILL2_CORE_CONSTANT_TOOL_NAME", "Dill2" );
define( "DILL2_CORE_CONSTANT_TOOL_CAPTION", "A tool for creating and maintaining websites." );
define( "DILL2_CORE_CONSTANT_TOOL_AUTHOR", "Jannik Haberbosch" );
define( "DILL2_CORE_CONSTANT_TOOL_CURRENT_VERSION", "20201220" );
define( "DILL2_CORE_CONSTANT_TOOL_CURRENT_VERSION_RELEASE", "" );
define( "DILL2_CORE_CONSTANT_TOOL_LICENSE", "GNU General Public License 3.0" );
define(
	"DILL2_CORE_CONSTANT_TOOL_TITLE",
	DILL2_CORE_CONSTANT_TOOL_NAME .
	' v' .
	DILL2_CORE_CONSTANT_TOOL_CURRENT_VERSION
);

define( "DILL2_CORE_CONSTANT_MAINFRAME_EDITOR_LINENUMBER_WIDTH", 50 );


define( "DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH",
	"website_projects"
);

// The directory where all website projects are stored:
// TODO

// Dill2WebsiteProject.php

// This is the directory where the entire generated website is stored.
define( "DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH",
	"website"
);

// This is the directory where the live-site is generated.
define(
	"DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH_LIVE",
	"live");

// This is the directory where the preview-site is generated.
define(
	"DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH_PREVIEW",
	"preview");

// This is where CSS files are stored.
define( "DILL2_CORE_WEBSITE_PROJECT_CSS_PATH",
	DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH . DIRECTORY_SEPARATOR .
	"css"
);
// This is where JS files are stored.
define( "DILL2_CORE_WEBSITE_PROJECT_JS_PATH",
	DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH . DIRECTORY_SEPARATOR .
	"js"
);

// This is where PHP files are stored.
define(
	"DILL2_CORE_WEBSITE_PROJECT_PHP_PATH",
	DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH .
	DIRECTORY_SEPARATOR .
	"php");



// This is where media files are stored.
define( "DILL2_CORE_WEBSITE_PROJECT_MEDIA_PATH",
	DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH . DIRECTORY_SEPARATOR .
	"media"
);
// This is where download files are stored.
define( "DILL2_CORE_WEBSITE_PROJECT_DOWNLOAD_PATH",
	DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH . DIRECTORY_SEPARATOR .
	"download"
);
// The database is stored in the root directory.  Careful:  This points to a file!!!
define( "DILL2_CORE_WEBSITE_PROJECT_DATABASE_PATH",
	"database"
);


// SQLite statements to create tables for a website project.
define( "DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_NAME", "template" );
define( "DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_COLUMN_TEMPLATENAME", "name" );
define( "DILL2_CORE_CONSTANT_DB_TABLE_PAGE_NAME", "page" );
// Templates are stored in this table.
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_TEMPLATE",
	"CREATE TABLE 'template'
	(
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		name TEXT,
		content TEXT
	);
	"
);
// Pages are stored in this table.
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_PAGE",
	"CREATE TABLE 'page'
	(
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		name TEXT NOT NULL,
		content TEXT,
		sort_id INTEGER,
		parent_id INTEGER,
		template_id INTEGER,
		type TEXT,
		FOREIGN KEY(parent_id) REFERENCES page(id) ON DELETE CASCADE
	);
	"
);

// Sync information for FTPS is stored in this table. For files!
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_GENERATE",
	"CREATE TABLE IF NOT EXISTS 'sync_file_generate'
	(
		filepath TEXT,
		modified_date TEXT
	);");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_UPLOAD_SFTP",
	"CREATE TABLE IF NOT EXISTS 'sync_file_upload_sftp'
	(
		filepath TEXT,
		checked INTEGER,
		uploaded_date TEXT
	);");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_UPLOAD_FTPS",
	"CREATE TABLE IF NOT EXISTS 'sync_file_upload_ftps'
	(
		filepath TEXT,
		checked INTEGER,
		uploaded_date TEXT
	);");
	


// Sync information for FTPS is stored in this table. For pages!
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_GENERATE",
	"CREATE TABLE IF NOT EXISTS 'sync_page_generate'
	(
		generated_content TEXT,		
		filepath TEXT,
		generated INTEGER,
		modified_date TEXT
	);");

define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_UPLOAD_SFTP",
	"CREATE TABLE IF NOT EXISTS 'sync_page_upload_sftp'
	(
		filepath TEXT,
		checked INTEGER,
		uploaded_date TEXT
	);");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_UPLOAD_FTPS",
	"CREATE TABLE IF NOT EXISTS 'sync_page_upload_ftps'
	(
		filepath TEXT,
		checked INTEGER,
		uploaded_date TEXT
	);");	


// CSS files are stored in this table.
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_CSS",
	"CREATE TABLE 'css'
	(
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		name TEXT NOT NULL,
		content TEXT
	);
	"
);
// JS files are stored in this table.
define(
	"DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_JS",
	"CREATE TABLE 'js'
	(
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		name TEXT NOT NULL,
		content TEXT
	);
	"
);
// None, one or more css files can be assigned to none, one or more templates:
define(
	"DILL2_CORE_WEBSITE_PROJECT_CREATETABLE_TEMPLATES_CSS",
	"CREATE TABLE 'templates_css'
	(
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		id_template INTEGER,
		id_css INTEGER,
		FOREIGN KEY(id_template) REFERENCES template(id) ON DELETE CASCADE,
		FOREIGN KEY(id_css) REFERENCES css(id) ON DELETE CASCADE
	);
	"
);
// None, one ore more js files can be assigned to none, one or more templates:
define(
	"DILL2_CORE_WEBSITE_PROJECT_CREATETABLE_TEMPLATES_JS",
	"CREATE TABLE 'templates_js'
	(
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		id_template INTEGER,
		id_js INTEGER,
		FOREIGN KEY(id_template) REFERENCES template(id) ON DELETE CASCADE,
		FOREIGN KEY(id_js) REFERENCES js(id) ON DELETE CASCADE
	);
	"
);
// Settings table for a project.
define(
	"DILL2_CORE_WEBSITE_PROJECT_CREATETABLE_WEBSITE_PROJECT_SETTINGS",
	"CREATE TABLE 'website_project_settings'
	(
		id INTEGER PRIMARY KEY AUTOINCREMENT,
		website_project_title TEXT,
		testserver_address TEXT,
		testserver_port INTEGER,
		sftp_webserver_path TEXT,
		sftp_webserver_ip_address TEXT,
		sftp_username TEXT,
		sftp_password TEXT,
		sftp_privatekey TEXT,
		sftp_privatekey_passphrase TEXT,
		sftp_authentication_method TEXT,
		ftps_webserver_path TEXT,
		ftps_webserver_ip_address TEXT,
		ftps_username TEXT,
		ftps_password TEXT,
		ftps_use_ftp INTEGER,
		ftps_mode_passive INTEGER,
		generate_live INTEGER,
		generate_preview INTEGER,
		upload_live_preview TEXT
	);
	"
);


// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
/* Alteration of the table 'website_project_settings' for existing website projects.
This piece of code adds three new fields to the table:
	- username
	- password
	- auto_upload
These are used to help user upload the website project to an SSH server.
'username' is the username of the SSH login and 'password' is the password, respectively.
'auto_upload' is integer which can be either 1 or 0.  If if is set to 1, the website
is automatically uploaded as soon as the user clicks on "Upload". */
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_USERNAME",
	"ALTER TABLE 'website_project_settings' ADD COLUMN username TEXT;"
);

define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_PUBLICKEY",
	"ALTER TABLE 'website_project_settings' ADD COLUMN publickey TEXT;"
);

define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_PRIVATEKEY",
	"ALTER TABLE 'website_project_settings' ADD COLUMN privatekey TEXT;"
);

define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_AUTO_UPLOAD",
	"ALTER TABLE 'website_project_settings' ADD COLUMN auto_upload INTEGER;"
);
// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)

// Alter table 'page' if it already exists.
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_PAGE_ADD_COL_type",
	"ALTER TABLE 'page' ADD COLUMN type TEXT;"
);

// SFTP
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_WEBSERVER_PATH",
	"ALTER TABLE 'website_project_settings' ADD COLUMN sftp_webserver_path TEXT;");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_WEBSERVER_IP_ADDRESS",
	"ALTER TABLE 'website_project_settings' ADD COLUMN sftp_webserver_ip_address TEXT;");

define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_USERNAME",
	"ALTER TABLE 'website_project_settings' ADD COLUMN sftp_username TEXT;");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_PASSWORD",
	"ALTER TABLE 'website_project_settings' ADD COLUMN sftp_password TEXT;");	

define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_PRIVATEKEY",
	"ALTER TABLE 'website_project_settings' ADD COLUMN sftp_privatekey TEXT;");
	
define(	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_AUTHENTICATION_METHOD",
	"ALTER TABLE 'website_project_settings' ADD COLUMN sftp_authentication_method TEXT;");

define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_PUBLICKEY",
	"ALTER TABLE 'website_project_settings' ADD COLUMN sftp_publickey TEXT;");

define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_PRIVATEKEY_PASSPRHASE",
	"ALTER TABLE 'website_project_settings' ADD COLUMN sftp_privatekey_passphrase;");
	
	
// FTPS
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_WEBSERVER_PATH",
	"ALTER TABLE 'website_project_settings' ADD COLUMN ftps_webserver_path TEXT;");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_WEBSERVER_IP_ADDRESS",
	"ALTER TABLE 'website_project_settings' ADD COLUMN ftps_webserver_ip_address TEXT;");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_USERNAME",
	"ALTER TABLE 'website_project_settings' ADD COLUMN ftps_username TEXT;");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_PASSWORD",
	"ALTER TABLE 'website_project_settings' ADD COLUMN ftps_password TEXT;");

define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_USE_FTP",
	"ALTER TABLE 'website_project_settings' ADD COLUMN ftps_use_ftp INTEGER;");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_MODE_PASSIVE",
	"ALTER TABLE 'website_project_settings' ADD COLUMN ftps_mode_passive INTEGER;");	


////////////////////////////////////////////////////////////////////////////////
// Core constants for the media and download path representing directories.
////////////////////////////////////////////////////////////////////////////////

define(
	"DILL2_CORE_WEBSITE_PROJECT_MEDIA_DIR",
	"media"
);

define(
	"DILL2_CORE_WEBSITE_PROJECT_DOWNLOAD_DIR",
	"download"
);

define(
	"DILL2_CORE_CONSTANT_FORWARD_SLASH",
	"/"
);


//
// Core constants for the preview image wxPanel.
//

define(
	"DILL2_CORE_CONSTANT_PREVIEW_IMAGE_WIDTH",
	160
);

define(
	"DILL2_CORE_CONSTANT_PREVIEW_IMAGE_HEIGHT",
	120
);


// Constants for page type
define(
	"DILL2_CORE_CONSTANT_PAGE_TYPE_HTML",
	"HTML");

define(
	"DILL2_CORE_CONSTANT_PAGE_TYPE_PHP",
	"PHP");

define(
	"DILL2_CORE_CONSTANT_PAGE_TYPE_DEFAULT",
	"HTML");
	
define(
	"DILL2_CORE_WEBSITE_PROJECT_TABLE_PAGE_SET_TYPE_TO_HTML",
	"UPDATE 'page' SET
		type = 'html';");

define(
	"DEFAULT_FONT_SIZE",
	12);
?>
