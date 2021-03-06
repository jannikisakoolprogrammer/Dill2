<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/Dill2WebsiteProject.php

Author:  Jannik Haberbosch

Year: 2014

Info:  This file contains the class Dill2WebsiteProject, which represents a
	single website project.

*******************************************************************************/

set_include_path(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"parsedown-master");

require_once(
	".." . DIRECTORY_SEPARATOR .
	"dill2" . DIRECTORY_SEPARATOR .
	"code" . DIRECTORY_SEPARATOR .
	"parsedown-master" . DIRECTORY_SEPARATOR . 
	"Parsedown.php");


class Dill2WebsiteProject
{
	public $project_name;
	
	public $css_files_path;
	public $js_files_path;
	public $php_files_path;
	public $media_files_path;
	public $download_files_path;
	public $db_path;
	public $relpath_websiteproject;
	public $abspath_websiteproject;
	public $abspath_websiteproject_website;
	
	public $file_to_edit;
	public $file_to_edit_original;
	
	public $testserver_pipes;
	public $test_server_handle;

	/* 2015-04-19 - JHA - START */
	public $files_buffer;
	public $item_already_edited;
	/* 2015-04-19 - JHA - END */
	
	public $observer_generate_website_view = NULL;
	
	
	function __construct( $_name, $_create_new = TRUE )
	{
		if( $_create_new )
		{
			////////////////////////////////
			// Create a new website project.
			////////////////////////////////
			
			// Create website project directories.
			$this->initialise_website_project_directory( $_name );
			
			// Create website project database.
			$this->initialise_website_project_database( $_name );
			
			// Set project variables.
			$this->project_name = $_name;
			$this->set_path_variables();
			
			// Establish a connection to the database.
			$this->sqlite3_conn = new SQLite3( $this->db_path );
			$this->sqlite3_conn->exec( "PRAGMA foreign_keys = ON;" );
		}
		else
		{
			////////////////////////////////////
			// Open an existing website project.
			////////////////////////////////////
			
			// Create missing directories.
			$this->initialise_website_project_directory( $_name );
			
			// --> Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)
			/* Update the sqlite3 table 'website_project_settings' if not yet
			done. */
			$this->update_website_project_database( $_name );
			// <-- Dill2 v2.0.0 - 02.07.2017, Jannik Haberbosch (JANHAB)

			// Set project variables.
			$this->project_name = $_name;
			$this->set_path_variables();
						
			// Establish a connection to the database.
			$this->sqlite3_conn = new SQLite3( $this->db_path );
			$this->sqlite3_conn->exec( "PRAGMA foreign_keys = ON;" );
			// $this->sqlite3_conn->exec( DILL2_CORE_WEBSITE_PROJECT_CREATETABLE_WEBSITE_PROJECT_SETTINGS );
			
			// Add already existing pictures and downloads to the sync_* tables.
			// But only if they have not been added before.
			$this->init_sync_file_tables(
				"MEDIA", 
				$this->get_file_names("media"));
			$this->init_sync_file_tables(
				"DOWNLOAD",
				$this->get_file_names("download"));				
			$this->init_sync_file_tables(
				"CSS", 
				$this->get_file_names("css"));
			$this->init_sync_file_tables(
				"JS",
				$this->get_file_names("js"));
			$this->init_sync_file_tables(
				"PHP", 
				$this->get_file_names("php"));				
		}
		
		
		$file_to_edit = array(
			"type" => NULL,
			"id" => NULL,
			"name" => NULL
		);
		
		$this->test_server_handle = NULL;
		$this->test_server_handle_descriptorspec = array(
			0 => array( "pipe", "r" ),
			1 => array( "pipe", "w" ),
			2 => array( "pipe", "r" )
		);
		
		$this->testserver_pipes = NULL;
		
		$this->file_to_edit_original = NULL;

		$this->files_buffer = array();
		$this->item_already_edited = array();
	}
	
	
	private function initialise_website_project_directory( $_name )
	{
		/* Creates the directories of a website project. */
		$dirs = array(
			DILL2_CORE_WEBSITE_PROJECT_WEBSITE_PATH,
			DILL2_CORE_WEBSITE_PROJECT_CSS_PATH,
			DILL2_CORE_WEBSITE_PROJECT_JS_PATH,
			DILL2_CORE_WEBSITE_PROJECT_PHP_PATH,
			DILL2_CORE_WEBSITE_PROJECT_MEDIA_PATH,
			DILL2_CORE_WEBSITE_PROJECT_DOWNLOAD_PATH,
		);
		
		for( $x = 0; $x < count( $dirs ); $x++ )
		{
			$tmp_path = ".." . DIRECTORY_SEPARATOR .
				DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH . DIRECTORY_SEPARATOR .
				$_name . DIRECTORY_SEPARATOR .
				$dirs[$x];

			if( !file_exists( $tmp_path ) )
			{
				$result = mkdir(
					".." . DIRECTORY_SEPARATOR .
					DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH . DIRECTORY_SEPARATOR .
					$_name . DIRECTORY_SEPARATOR .
					$dirs[$x],
					0777,
					TRUE // Create recursively.
				);			
			}
		}
	}
	
	
	private function initialise_website_project_database( $_name )
	{
		/* Creates the database and tables of a website project. */
		
		$db_path = ".." . DIRECTORY_SEPARATOR .
			DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH . DIRECTORY_SEPARATOR .
			$_name . DIRECTORY_SEPARATOR .
			DILL2_CORE_WEBSITE_PROJECT_DATABASE_PATH;
			
		// Create the database.
		$db = new SQLite3( $db_path );
		
		// Enable foreign keys.
		$db->exec( "PRAGMA foreign_keys = ON;" );
		
		// Create tables.
		$db->exec( DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_TEMPLATE );
		$db->exec( DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_PAGE );
		$db->exec( DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_CSS );
		$db->exec( DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_JS );
		$db->exec( DILL2_CORE_WEBSITE_PROJECT_CREATETABLE_TEMPLATES_CSS );
		$db->exec( DILL2_CORE_WEBSITE_PROJECT_CREATETABLE_TEMPLATES_JS );
		$db->exec( DILL2_CORE_WEBSITE_PROJECT_CREATETABLE_WEBSITE_PROJECT_SETTINGS );
		
		// Sync tables for files.
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_GENERATE);
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_UPLOAD_SFTP);
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_UPLOAD_FTPS);
		
		// Sync tables for pages.
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_GENERATE);
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_UPLOAD_SFTP);
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_UPLOAD_FTPS);
		
		// Close the connection.
		$db->close();
	}
	
	
	/*  This function updates the several database tables.
	*/
	private function update_website_project_database( $_name )
	{
		// Connect to the existing db.
		$db_path = ".." . DIRECTORY_SEPARATOR .
			DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH . DIRECTORY_SEPARATOR .
			$_name . DIRECTORY_SEPARATOR .
			DILL2_CORE_WEBSITE_PROJECT_DATABASE_PATH;
			
		$db = new SQLite3( $db_path );
		
		$res = $db->query( "SELECT * FROM 'website_project_settings';");

		if( $res->numColumns() < 10) // Need to fix this later.  Not very good.
		{
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_USERNAME);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_PUBLICKEY);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_PRIVATEKEY);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_AUTO_UPLOAD);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_WEBSERVER_PATH);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_WEBSERVER_IP_ADDRESS);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_USERNAME);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_PASSWORD);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_PRIVATEKEY);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_AUTHENTICATION_METHOD);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_WEBSERVER_PATH);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_WEBSERVER_IP_ADDRESS);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_USERNAME);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_PASSWORD);						
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_USE_FTP);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_FTPS_MODE_PASSIVE);	
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_PUBLICKEY);
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTER_TABLE_WEBSITE_PROJECT_SETTINGS_ADD_COL_SFTP_PRIVATEKEY_PASSPRHASE);			
		}		
		
		$res = $db->query( "SELECT * FROM 'page';");		
		
		if (($res->numColumns() == 6))
		{
			// Add column "type" to table "page".  Stores either "HTML" or "PHP".
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_ALTERTABLE_PAGE_ADD_COL_type);
			
			// All existing pages are by default of type "HTML".
			$db->exec(DILL2_CORE_WEBSITE_PROJECT_TABLE_PAGE_SET_TYPE_TO_HTML);		
		}
		
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_GENERATE);
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_UPLOAD_SFTP);
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_FILE_UPLOAD_FTPS);
		
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_GENERATE);
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_UPLOAD_SFTP);
		$db->exec(DILL2_CORE_WEBSITE_PROJECT_DB_CREATETABLE_SYNC_PAGE_UPLOAD_FTPS);

		// Close the connection.
		$db->close();
	}
	
	
	private function set_path_variables()
	{
		$this->relpath_websiteproject = ".." .
										DIRECTORY_SEPARATOR .
										DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH .
										DIRECTORY_SEPARATOR .
										$this->project_name .
										DIRECTORY_SEPARATOR;
										
		$this->abspath_websiteproject = basename( getcwd() ) .
										DIRECTORY_SEPARATOR .
										".." .
										DIRECTORY_SEPARATOR .
										DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH .
										DIRECTORY_SEPARATOR .
										$this->project_name .
										DIRECTORY_SEPARATOR;
		
		$this->abspath_websiteproject_website_pages = ".." .
										DIRECTORY_SEPARATOR .
										DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH .
										DIRECTORY_SEPARATOR .
										$this->project_name .
										DIRECTORY_SEPARATOR .
										"website" .
										DIRECTORY_SEPARATOR .
										"";
										
		$this->abspath_websiteproject_website = ".." .
										DIRECTORY_SEPARATOR .
										DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH .
										DIRECTORY_SEPARATOR .
										$this->project_name .
										DIRECTORY_SEPARATOR .
										"website";
		
													
		$this->css_files_path = $this->relpath_websiteproject .
								DILL2_CORE_WEBSITE_PROJECT_CSS_PATH;
		
		$this->js_files_path = $this->relpath_websiteproject .
								DILL2_CORE_WEBSITE_PROJECT_JS_PATH;
		
		$this->php_files_path = $this->relpath_websiteproject .
								DILL2_CORE_WEBSITE_PROJECT_PHP_PATH;
		
		$this->media_files_path = $this->relpath_websiteproject .
									DILL2_CORE_WEBSITE_PROJECT_MEDIA_PATH;
		
		$this->download_files_path = $this->relpath_websiteproject .
									DILL2_CORE_WEBSITE_PROJECT_DOWNLOAD_PATH;
		
		$this->db_path = ".." .
						DIRECTORY_SEPARATOR .
						DILL2_CORE_CONSTANT_WEBSITE_PROJECTS_PATH .
						DIRECTORY_SEPARATOR .
						$this->project_name .
						DIRECTORY_SEPARATOR .
						DILL2_CORE_WEBSITE_PROJECT_DATABASE_PATH;
	}
	
	
	public function get_file_names( $type = "" )
	{
		switch( $type )
		{
			case "css":
			{
				$filepath = $this->css_files_path;
				break;
			}
			case "js":
			{
				$filepath = $this->js_files_path;
				break;
			}
			case "php":
			{
				$filepath = $this->php_files_path;
				break;
			}
			case "media":
			{
				$filepath = $this->media_files_path;
				break;
			}
			case "download":
			{
				$filepath = $this->download_files_path;
				break;
			}
			default:
				return array();
		}		
		return array_values(
			array_diff(
				scandir( $filepath ),
				array( ".", ".." )
			)
		);	
	}
	
	
	function __destruct()
	{
		$this->sqlite3_conn->close();
	}
	
	
	public function get_template_names()
	{
		$template_names = array();
		$arr = $this->db_select(
			DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_NAME,
			array( DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_COLUMN_TEMPLATENAME ),
			NULL
		);
		return array_column( $arr, DILL2_CORE_CONSTANT_DB_TABLE_TEMPLATE_COLUMN_TEMPLATENAME );
	}
	
	
	public function get_file_names_of_dir( $filetype )
	{
		$filenames = array();
		$dirpath = $this->get_correct_file_path( $filetype );
		foreach( scandir( $dirpath ) as $dir )
		{
			if( $dir != "." && $dir != ".." )
			{
				$filenames[] = $dir;
			}
		}
		return $filenames;
	}
	
	
	public function db_value_exists( $value, $valuetype, $column, $table )
	{
		/* This function checks whether a value in a specified column in a
		specified table exists.  If a match is found, "TRUE" is returned,
		otherwise "FALSE" is returned.
		
		*/
		
		$sqlite3_stmt 	= NULL;
		$sqlite3_result = NULL;
		$rows			= 0;
		$returnvalue	= NULL;
		
		// Prepare the statement.
		$sqlite3_stmt = $this->sqlite3_conn->prepare(
			sprintf(
				"SELECT *
				FROM %s
				WHERE %s = :%s",
				$table,
				$column,
				$column
			)
		);
		
		// Bind values to the prepared statement.
		$sqlite3_stmt->bindValue(
			sprintf(
				":%s",
				$column
			),
			$value,
			$valuetype
		);
		
		// Execute the prepared statement.
		$sqlite3_result = $sqlite3_stmt->execute();
		while( $sqlite3_result->fetchArray( SQLITE3_ASSOC ) )
		{
			$rows += 1;
		}
		
		// Finalize the prepared statement.
		$sqlite3_result->finalize();
		$sqlite3_stmt->close();
		
		return $rows > 0 ? TRUE : FALSE;	
	}
	
	
	public function db_select( $table, $columns = "*", $condition = NULL,
								$order_by = NULL )
	{
		$rows = array();
		$sqlite3_stmt = NULL;
		$sqlite3_result = NULL;
		
		if( $condition == NULL )
		{
			if( $columns == "*" )
			{
				if( $order_by != NULL )
				{
					$sqlite3_result = $this->sqlite3_conn->query(
						sprintf(
							"SELECT * FROM %s ORDER BY %s %s",
							$table,
							$order_by[0],
							$order_by[1]
						)
					);
				}
				else
				{
					$sqlite3_result = $this->sqlite3_conn->query(
						sprintf(
							"SELECT * FROM %s",
							$table
						)
					);				
				}
			}
			else
			{
				if( $order_by != NULL )
				{
					$sqlite3_result = $this->sqlite3_conn->query(
						sprintf(
							"SELECT %s FROM %s ORDER BY %s %s",
							$this->sqlite3_select_columns_string( $columns ),
							$table,
							$order_by[0],
							$order_by[1]
						)
					);			
				}
				else
				{
					$sqlite3_result = $this->sqlite3_conn->query(
						sprintf(
							"SELECT %s FROM %s",
							$this->sqlite3_select_columns_string( $columns ),
							$table
						)
					);
				}
			}
		}
		else
		{
			if( $columns == "*" )
			{
				if( $order_by != NULL )
				{
					$sqlite3_stmt = $this->sqlite3_conn->prepare(
						sprintf(
							"SELECT *
							FROM %s
							WHERE %s %s :%s
							ORDER BY %s %s",
							$table,
							$condition[0],
							$condition[1],
							$condition[0] + "_",
							$order_by[0],
							$order_by[1]
						)
					);
				
					$sqlite3_stmt->bindValue(
						sprintf(
							":%s",
							$condition[0] + "_"
						),
						$condition[2],
						$condition[3]
					);
				
					$sqlite3_result = $sqlite3_stmt->execute();				
				}
				else
				{
					$sqlite3_stmt = $this->sqlite3_conn->prepare(
						sprintf(
							"SELECT *
							FROM %s
							WHERE %s %s :%s",
							$table,
							$condition[0],
							$condition[1],
							$condition[0] + "_"
						)
					);
				
					$sqlite3_stmt->bindValue(
						sprintf(
							":%s",
							$condition[0] + "_"
						),
						$condition[2],
						$condition[3]
					);
				
					$sqlite3_result = $sqlite3_stmt->execute();				
				}
			}
			else
			{
				if( $order_by != NULL )
				{
					$sqlite3_stmt = $this->sqlite3_conn->prepare(
						sprintf(
							"SELECT %s
							FROM %s
							WHERE %s %s :%s
							ORDER BY %s %s",
							$this->sqlite3_select_columns_string( $columns ),
							$table,
							$condition[0],
							$condition[1],
							$condition[0] + "_",
							$order_by[0],
							$order_by[1]
						)
					);
				
					$sqlite3_stmt->bindValue(
						sprintf(
							":%s",
							$condition[0] + "_"
						),
						$condition[2],
						$condition[3]
					);
				
					$sqlite3_result = $sqlite3_stmt->execute();				
				}
				else
				{
					$sqlite3_stmt = $this->sqlite3_conn->prepare(
						sprintf(
							"SELECT %s
							FROM %s
							WHERE %s %s :%s",
							$this->sqlite3_select_columns_string( $columns ),
							$table,
							$condition[0],
							$condition[1],
							$condition[0] + "_"
						)
					);
				
					$sqlite3_stmt->bindValue(
						sprintf(
							":%s",
							$condition[0] + "_"
						),
						$condition[2],
						$condition[3]
					);
				
					$sqlite3_result = $sqlite3_stmt->execute();				
				}
			}
		}
		while( $row = $sqlite3_result->fetchArray( SQLITE3_ASSOC ) )
		{
			$rows[] = $row;
		}
		$sqlite3_result->finalize();
		return $rows;		
	}
	
	
	public function db_insert( $table, $columns, $values, $valuetypes )
	{
		/* Inserts a new record into a table.
		
		*/
		
		$sqlite3_stmt 			= NULL;
		$sqlite3_result			= NULL;
		
		$sqlite3_format_string 	= str_repeat( ":%s, ", count( $values ) );
		$sqlite3_format_string	= substr( $sqlite3_format_string, 0, strlen( $sqlite3_format_string ) - 2 );
		$sqlite3_format_string	= vsprintf( $sqlite3_format_string, $columns );
		
		// Prepare the sqlite3 statement.
		$sqlite3_stmt = $this->sqlite3_conn->prepare(
			sprintf(
				"INSERT INTO %s
				( %s )
				VALUES
				( %s )",
				$table,
				implode( ", ", $columns ),
				$sqlite3_format_string
			)
		);
		
		// Bind values to the sqlite3 statement.
		$sqlite3_stmt = $this->sqlite3_bind_values( $sqlite3_stmt, $columns, $values, $valuetypes );
		
		// Execute the prepared statement.
		$sqlite3_result = $sqlite3_stmt->execute();
		if( $sqlite3_result == FALSE )
		{
			$sqlite3_stmt->close();
			return FALSE;
		}
		else
		{
			$sqlite3_result->finalize();
			$sqlite3_stmt->close();
			return TRUE;		
		}
	}
	
	
	public function db_update( $table, $columns, $values, $valuetypes, $condition )
	{
		/* Updates existing records in a table that match the condition.
		
		*/
		
		$sqlite3_stmt = NULL;
		$sqlite3_result = NULL;
		
		$sqlite3_update_format_string = "UPDATE %s SET %s WHERE %s";
		
		// Set format string.
		$sqlite3_single_set_format_string = "%s = :%s, ";
		$sqlite3_set_format_string = str_repeat( $sqlite3_single_set_format_string, count( $columns ) );
		$sqlite3_set_format_string = substr( $sqlite3_set_format_string, 0, strlen( $sqlite3_set_format_string ) - 2 );
		$sqlite3_set_format_string = vsprintf(
			$sqlite3_set_format_string,
			$this->array_create_duplicates( $columns )
		);
		
		// Where format string.
		$sqlite3_where_format_string = "%s %s :%s";
		$sqlite3_where_format_string = sprintf(
			$sqlite3_where_format_string,
			$condition[0],
			$condition[1],
			$condition[0] + "_"
		);
		
		// Almost there...
		$sqlite3_update_format_string = sprintf(
			$sqlite3_update_format_string,
			$table,
			$sqlite3_set_format_string,
			$sqlite3_where_format_string
		);
		
		
		// Bind parameters to the prepared statement.
		$sqlite3_stmt = $this->sqlite3_conn->prepare(
			$sqlite3_update_format_string
		);
		
		$sqlite3_stmt = $this->sqlite3_bind_values(
			$sqlite3_stmt,
			$columns,
			$values,
			$valuetypes
		);
		
		$sqlite3_stmt->bindValue(
			sprintf(
				":%s",
				$condition[0] + "_"
			),
			$condition[2],
			$condition[3]
		);
		
		// Execute the prepared statement.
		$sqlite3_result = $sqlite3_stmt->execute();
		
		if( $sqlite3_result )
		{
			$sqlite3_result->finalize();
			$sqlite3_stmt->close();
			return TRUE;
		}
		else
		{
			$sqlite3_result->finalize();
			$sqlite3_stmt->close();		
			return FALSE;
		}	
	}
	
	
	public function db_delete( $table, $condition )
	{
		/* Deletes existing records from a table that matches the condition.
		
		*/
		$sqlite3_stmt = NULL;
		$sqlite3_result = NULL;
		
		$sqlite3_statement_string = sprintf(
			"DELETE FROM %s WHERE %s %s :%s",
			$table,
			$condition[0],
			$condition[1],
			$condition[0]
		);
		
		// Prepare the sqlite3 statement.
		$sqlite3_stmt = $this->sqlite3_conn->prepare(
			$sqlite3_statement_string
		);
		
		$sqlite3_stmt->bindValue(
			sprintf(
				":%s",
				$condition[0]
			),
			$condition[2],
			$condition[3]
		);
		
		// Execute the prepared sqlite3 statement.
		$sqlite3_result = $sqlite3_stmt->execute();
		
		if( $sqlite3_result )
		{
			$sqlite3_result->finalize();
			$sqlite3_stmt->close();
			return TRUE;
		}
		else
		{
			$sqlite3_result->finalize();
			$sqlite3_stmt->close();		
			return FALSE;
		}
	}
	
	// Empties a table.
	public function db_empty($_table)
	{
		$stmt = sprintf(
			"DELETE FROM %s",
			$_table);
			
		$this->sqlite3_conn->exec($stmt);
	}
	
	
	private function sqlite3_bind_values( $sqlite3_stmt, $columns, $values, $valuetypes )
	{
		/* Binds many values to a prepared sqlite3 statement in one flush.
		
		*/
		for( $x = 0; $x < count( $columns ); $x++ )
		{
			$sqlite3_stmt->bindValue(
				sprintf(
					":%s",
					$columns[$x]
				),
				$values[$x],
				$valuetypes[$x]
			);
		}
		
		return $sqlite3_stmt;
	}
	
	
	private function sqlite3_select_columns_string( $columns )
	{
		$sqlite3_select_col_string 	= str_repeat( "%s, ", count( $columns ) );
		$sqlite3_select_col_string	= substr( $sqlite3_select_col_string, 0, strlen( $sqlite3_select_col_string ) - 2 );
		$sqlite3_select_col_string	= vsprintf( $sqlite3_select_col_string, $columns );
		
		return $sqlite3_select_col_string;
	}
	
	
	private function array_create_duplicates( $arr )
	{
		$new_array = array();
		for( $x = 0; $x < count( $arr ); $x++ )
		{
			$new_array[] = $arr[$x];
			$new_array[] = $arr[$x];
		}
		
		return $new_array;
	}
	
	
	public function get_website_structure()
	{
		/* I grab all parent pages in the corrent order and the branches of which
		they are the parents. */
		$all_pages = $this->db_select(
			"page",
			"*",
			NULL,
			array(
				"sort_id",
				"ASC"
			)
		);
		$parent_pages_raw = array_filter(
			$all_pages,
			function( $element )
			{
				return $element["parent_id"] == NULL ? TRUE : FALSE;
			}
		);
		
		$parent_pages = array();
		
		$counter = 0;
		foreach( $parent_pages_raw as $ppr )
		{
			$parent_pages[$counter] = array(
				"self" => $ppr,
				"children" => $this->get_parent_page_tree( $ppr["id"] )
			);
			$counter += 1;
		}
		
		return $parent_pages;
	}
	
	
	private function get_parent_page_tree( $parent_page_id )
	{
		$arr = array();
		
		$child_pages = $this->db_select(
			"page",
			"*",
			array(
				"parent_id",
				"=",
				$parent_page_id,
				SQLITE3_INTEGER
			),
			array(
				"sort_id",
				"ASC"
			)
		);
		
		for( $x = 0; $x < count( $child_pages ); $x++ )
		{
			$arr[$x] = array(
				"self" => $child_pages[$x],
				"children" => $this->get_parent_page_tree( $child_pages[$x]["id"] )
			);
		}
		
		return $arr;
	}
	
	
	public function website_structure_branch_assign_new_sort_ids( $parent_id = NULL )
	{
		/* This function assigns new sort ids to all elements of a branch in the
		website structure.
		
		*/
		$sort_id_counter = 1;
		
		// First let's retrieve an array that contains all elements of a branch.
		if( $parent_id == NULL )
		{
			$branch_elements = $this->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"IS",
					NULL,
					SQLITE3_INTEGER
				),
				array(
					"sort_id",
					"ASC"
				)
			);
		}
		else
		{
			$branch_elements = $this->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"=",
					$parent_id,
					SQLITE3_INTEGER
				),
				array(
					"sort_id",
					"ASC"
				)				
			);
		}
		
		
		foreach( $branch_elements as $element )
		{
			$this->db_update(
				"page",
				array(
					"sort_id"
				),
				array(
					$sort_id_counter
				),
				array(
					SQLITE3_INTEGER
				),
				array(
					"id",
					"=",
					$element["id"],
					SQLITE3_INTEGER
				)
			);
			$sort_id_counter += 1;
		}
	}
	
	
	public function exists_file( $filetype, $filename )
	{
		/* This function checks if a file exists in a directory and returns TRUE
		if it does, otherwise FALSE.
		
		*/
		$relpath = $this->get_correct_file_path( $filetype, $filename );
		return file_exists( $relpath ) ? TRUE : FALSE;
	}
	
	
	public function create_file( $filetype, $filename )
	{
		/* This function creates a file in a directory.
		
		*/
		$relpath = $this->get_correct_file_path( $filetype, $filename );
		$file_handle = fopen( $relpath, "wb" );
		fclose( $file_handle );
	}
	
	
	public function copy_file( $filetype, $filename, $orig_filepath )
	{
		/* This function copies a file into a directory.
		
		*/
		$relpath = $this->get_correct_file_path( $filetype, $filename );
		copy( $orig_filepath, $relpath );
	}
	
	
	public function rename_file( $filetype, $old_filename, $new_filename )
	{
		/* This function renames a file.
		
		*/
		$old_filepath = $this->get_correct_file_path( $filetype, $old_filename );
		$new_filepath = $this->get_correct_file_path( $filetype, $new_filename );
		rename( $old_filepath, $new_filepath );
	}
	
	
	public function delete_file( $filetype, $filename )
	{
		/* This function deletes a file.
		
		*/
		$relpath = $this->get_correct_file_path( $filetype, $filename );
		unlink( $relpath );
	}
	
	
	private function get_correct_file_path( $filetype, $filename = "" )
	{
		switch( $filetype )
		{
			case "CSS":
			{
				return $this->css_files_path . DIRECTORY_SEPARATOR . $filename;
			}
			case "JS":
			{
				return $this->js_files_path . DIRECTORY_SEPARATOR . $filename;
			}
			case "PHP":
			{
				return $this->php_files_path .
						DIRECTORY_SEPARATOR .
						$filename;
			}
			case "MEDIA":
			{
				return $this->media_files_path . DIRECTORY_SEPARATOR . $filename;
			}
			case "DOWNLOAD":
			{
				return $this->download_files_path . DIRECTORY_SEPARATOR . $filename;
			}
			default:
			{
				return FALSE;
			}
		}	
	}
	
	
	public function read_content()
	{
		$type = $this->file_to_edit["type"];
		if( isset( $this->file_to_edit["id"] ) )
		{
			$id = $this->file_to_edit["id"];
		}
		else
		{
			$name = $this->file_to_edit["name"];
		}
		
		switch( $type )
		{
			case "PAGE":
			{
				$sqlite3_result_array = $this->db_select(
					"page",
					array(
						"content"
					),
					array(
						"id",
						"=",
						$id,
						SQLITE3_INTEGER
					)
				);
				return $sqlite3_result_array[0]["content"];
			}
			case "TEMPLATE":
			{
				$sqlite3_result_array = $this->db_select(
					"template",
					array(
						"content"
					),
					array(
						"name",
						"=",
						$name,
						SQLITE3_TEXT
					)
				);
				return $sqlite3_result_array[0]["content"];
			}
			case "CSS":
			case "JS":
			case "PHP":
			{
				return file_get_contents(
					$this->get_correct_file_path(
						$this->file_to_edit["type"],
						$this->file_to_edit["name"]
					)
				);				
			}
			default:
			{
				return "";
			}
		}
	}
	
	
	public function write_content( $text )
	{
		$text = utf8_encode( $text );
		$type = $this->file_to_edit["type"];
		if( isset( $this->file_to_edit["id"] ) )
		{
			$id = $this->file_to_edit["id"];
		}
		else
		{
			$name = $this->file_to_edit["name"];		
		}
		
		switch( $type )
		{
			case "PAGE":
			{
				$sqlite3_result_array = $this->db_update(
					"page",
					array(
						"content"
					),
					array(
						$text
					),
					array(
						SQLITE3_TEXT
					),
					array(
						"id",
						"=",
						$id,
						SQLITE3_INTEGER
					)
				);
				break;
			}
			case "TEMPLATE":
			{
				$sqlite3_result_array = $this->db_update(
					"template",
					array(
						"content"
					),
					array(
						$text
					),
					array(
						SQLITE3_TEXT
					),
					array(
						"name",
						"=",
						$name,
						SQLITE3_TEXT
					)
				);
				break;
			}
			case "CSS":
			case "JS":
			case "PHP":
			{
				file_put_contents(
					$this->get_correct_file_path(
						$this->file_to_edit["type"],
						$this->file_to_edit["name"]
					),
					$text
				);
				
				$rel_f_path = $type .
					DIRECTORY_SEPARATOR .
					$name;
				
				// Update sync tables.
				foreach(array("sync_file_generate") as $tmp_table)
				{
					$this->sync_table_update_file(
						$tmp_table,
						$rel_f_path,
						$rel_f_path);
				}
				
				break;				
			}
			default:
			{
				return;
			}
		}	
	}
	
	
	private function rrmdir( $path )
	{
		foreach( scandir( $path ) as $item )
		{
			if( $item == "." 
			 || $item == ".." 
			 || $item == "css" 
			 || $item == "js" 
			 || $item == "php" 
			 || $item == "media" 
			 || $item == "download" ) continue;		 
			
			if( is_dir( $path . DIRECTORY_SEPARATOR . $item ) )
			{				
				$this->rrmdir( $path . DIRECTORY_SEPARATOR . $item );					
			}
			else
			{
				if (file_exists($path . DIRECTORY_SEPARATOR . $item))
				{
					// Check if it can be deleted.
					if (empty($this->sync_table_select_page(
						"sync_page_generate",
						$path .
						DIRECTORY_SEPARATOR .
						$item)))
					{					
						unlink( $path . DIRECTORY_SEPARATOR . $item );
					}
				}
			}
		}
		
		if($path != $this->abspath_websiteproject_website_pages)
		{
			if(file_exists($path))
			{
				// Check if it can be deleted.
				if (empty($this->sync_table_select_page(
					"sync_page_generate",
					$path)))
				{				
					rmdir($path);
				}
			}
		}
	}
	
	
	public function generate_website()
	{
		// Create the root directory where we put our pages in.
		if (!file_exists($this->abspath_websiteproject_website_pages))
		{
			mkdir( $this->abspath_websiteproject_website_pages );
		}
		
		// Firstly, mark all records in "sync_page_generate" to generated = 0.
		$this->db_update(
			"sync_page_generate",
			array("generated"),
			array(0),
			array(SQLITE3_INTEGER),			
			array(
				"generated",
				"=",
				1,
				SQLITE3_INTEGER));
		
		$all_pages_array = $this->db_select(
			"page"
		);
		
		$website_project_title_array = $this->db_select(
			"website_project_settings"
		);
		$website_project_title = $website_project_title_array[0]["website_project_title"];
		
		$this->generate_pages( NULL, "", $website_project_title, $all_pages_array );	

		// And lastly, let's create a PHP file to redirect the user to the first root page (usually the HOME page).
		if( count( $all_pages_array ) > 0 )
		{
			// Select the page with the lowest ID, right after the ROOT.
			$home_page_node = $this->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"IS",
					NULL,
					SQLITE3_INTEGER
				),
				array(
					"sort_id",
					"ASC"
				));
				
			$php_content = sprintf(
				"<?php\nheader('Location: /%s');\n?>",
				$home_page_node[0]["name"]);
			
			foreach(array("sync_page_generate") as $tmp_table)
			{
				$compare_page_to = $this->sync_table_select_page(
					$tmp_table,
					$this->abspath_websiteproject_website_pages .
					DIRECTORY_SEPARATOR .
					"index.php");
					
				if (count($compare_page_to) == 0)
				{
					file_put_contents(
						$this->abspath_websiteproject_website_pages .
						"index.php",
						$php_content
					);
						
					$this->sync_table_add_page(
						$tmp_table,
						$php_content,
						$this->abspath_websiteproject_website_pages .
						DIRECTORY_SEPARATOR .
						"index.php",
						1);						
				}
				else
				{
					if($compare_page_to[0]["generated_content"] != $php_content)
					{
						file_put_contents(
							$this->abspath_websiteproject_website_pages .
							"index.php",
							$php_content
						);
						
						$this->sync_table_update_page(
							$tmp_table,
							$php_content,
							$this->abspath_websiteproject_website_pages .
							DIRECTORY_SEPARATOR .
							"index.php",
							1);
					}
					else
					{
						// Only update "generated" (Set to 1).
						$this->sync_table_update_page_already_generated(
							$tmp_table,
							$this->abspath_websiteproject_website_pages .
							DIRECTORY_SEPARATOR .
							"index.php",
							1);						
					}
				}
			}
		}
		
		foreach(array("sync_page_generate") as $tmp_table)
		{			
			// Then delete the records of files that don't exist anymore.
			$this->sync_table_delete_page($tmp_table);
		}		
		
		
		// First of all, delete all files that were not generated.
		// EXCEPT the css, js, php, media and download directories.
		if(file_exists($this->abspath_websiteproject_website_pages))
		{
			$this->rrmdir($this->abspath_websiteproject_website_pages);
		}

		// Always close progress dialog window.
		$this->close_observer_generate_website_view();		
	}
	
	
	private function generate_pages( $parent_id = NULL, $cur_dir, $website_project_title, $all_pages_array )
	{
		/* This function generates the entire pages.
		
		*/
		if( $parent_id == NULL )
		{
			$nodes = $this->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"IS",
					NULL,
					SQLITE3_INTEGER
				),
				array(
					"sort_id",
					"ASC"
				)
			);
		}
		else
		{
			$nodes = $this->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"=",
					$parent_id,
					SQLITE3_INTEGER
				),
				array(
					"sort_id",
					"ASC"
				)
			);
		}
		$cur_dir_static = $cur_dir;
		foreach( $nodes as $node )
		{
			$cur_dir = $cur_dir_static . DIRECTORY_SEPARATOR . $node["name"];
			
			// Before creating the dir, check if it exists in sync-tables.
			foreach(array("sync_page_generate") as $tmp_table)
			{
				$compare_dir_to = $this->sync_table_select_page(
					$tmp_table,
					$this->abspath_websiteproject_website_pages .
					$cur_dir);
					
				if (empty($compare_dir_to))
				{
					if (file_exists($this->abspath_websiteproject_website_pages .
						   $cur_dir) == FALSE)
					{
						mkdir($this->abspath_websiteproject_website_pages .
							$cur_dir);
					}
						
					$this->sync_table_add_page(
						$tmp_table,
						"",
						$this->abspath_websiteproject_website_pages .
						$cur_dir,
						1);						
				}
				else
				{
					$this->sync_table_update_page_already_generated(
						$tmp_table,
						$this->abspath_websiteproject_website_pages .
						$cur_dir,
						1);						
				}
			}
			
			// Now let's prepare the file we are going to create in the current directory.
			// For this, we need the template for the current page.
			$template_array = $this->db_select(
				"template",
				array(
					"content"
				),
				array(
					"id",
					"=",
					$node["template_id"],
					SQLITE3_INTEGER
				)
			);
			$template = $template_array[0]["content"];
			
			$arr = array();
			$arr = $this->page_generate_subnav_get_path_to_root( $node, $arr );
			$arr = array_reverse( $arr );			
			
			// Now we create the content for the current page.
			$page_content = $template;
			
			// I generate the title of the current page.
			$page_content = str_replace(
				"#title#",
				$this->page_generate_title( $arr, $all_pages_array, $website_project_title ),
				$page_content
			);
			
			// We put the content of the current page.
			$markdown_parser = new Parsedown();
			$page_content = str_replace(
				"#content#",
				$markdown_parser->text($node["content"]),
				$page_content
			);
			
			// We generate the main navigation for this page and put it into the source-code.
			$root_parent = $this->get_root_parent( $node );
			$page_content = str_replace(
				"#mainnav#",
				$this->page_generate_mainnav( $root_parent["name"] ),
				$page_content
			);
			
			// Subnavigation.			
			$page_content = str_replace(
				"#subnav#",
				$this->page_generate_subnav( $arr ),
				$page_content
			);
			
			// Breadcrumb navigation.
			$page_content = str_replace(
				"#breadcrumbnav#",
				$this->page_generate_breadcrumbnav( $arr ),
				$page_content
			);
			
			// Write the content to an "index.html" file.
			if ($node["type"] == "HTML")
			{
				$index_file = "index.html";
			}
			else if ($node["type"] == "PHP")
			{
				$index_file = "index.php";
			}
			else
			{
				$index_file = "index.html"; // Should actually never go here, but let's just do this fallback.
			}
			
			// TODO:  Before I put the content, check if the content has changed.
			// If the content hasn't changed, there is no need to update the file.
			// Update the sync_tables, too!!
			

			foreach(array("sync_page_generate") as $tmp_table)
			{
				$testtest = $this->abspath_websiteproject_website_pages .
					$cur_dir .
					DIRECTORY_SEPARATOR .
					$index_file;
				
				$compare_page_to = $this->sync_table_select_page(
					$tmp_table,
					$this->abspath_websiteproject_website_pages .
					$cur_dir .
					DIRECTORY_SEPARATOR .
					$index_file);
					
				if (empty($compare_page_to))
				{
					file_put_contents(
						$this->abspath_websiteproject_website_pages .
						$cur_dir .
						DIRECTORY_SEPARATOR .
						$index_file,
						$page_content
					);
						
					$this->sync_table_add_page(
						$tmp_table,
						$page_content,
						$this->abspath_websiteproject_website_pages .
						$cur_dir .
						DIRECTORY_SEPARATOR .
						$index_file,
						1);						
				}
				else
				{
					if($compare_page_to[0]["generated_content"] != $page_content)
					{
						// Content has changed.  Update record.
						file_put_contents(
							$this->abspath_websiteproject_website_pages .
							$cur_dir .
							DIRECTORY_SEPARATOR .
							$index_file,
							$page_content
						);
						
						$this->sync_table_update_page(
							$tmp_table,
							$page_content,
							$this->abspath_websiteproject_website_pages .
							$cur_dir .
							DIRECTORY_SEPARATOR .
							$index_file,
							1);						
					}
					else
					{
						// Only update "generated" (Set to 1).
						$this->sync_table_update_page_already_generated(
							$tmp_table,
							$this->abspath_websiteproject_website_pages .
							$cur_dir .
							DIRECTORY_SEPARATOR .
							$index_file,
							1);						
					}
				}						
			}
			
			// Upgrade progress bar.
			$this->notify_observer_generate_website_view();
			
			// Go deeper.
			$this->generate_pages( $node["id"], $cur_dir, $website_project_title, $all_pages_array );
		}	
	}
	
	
	private function page_generate_mainnav( $_page_name )
	{
		$HTML = "<ul>";
		$parent_pages = $this->db_select(
			"page",
			array(
				"name"
			),
			array(
				"parent_id",
				"IS",
				NULL,
				SQLITE3_INTEGER
			),
			array(
				"sort_id",
				"ASC"
			)
		);
		
		foreach( $parent_pages as $parent_page )
		{
			if( $parent_page["name"] == $_page_name )
			{
				$HTML .= sprintf(
					"<li id='%s'><a class='%s' href='/%s'>%s</a></li>",
					"MAINNAV_" . $parent_page["name"],
					'active_link',
					$parent_page["name"],
					str_replace( "_", " ", $parent_page["name"] )
				);			
			}
			else
			{
				$HTML .= sprintf(
					"<li id='%s'><a href='/%s'>%s</a></li>",
					"MAINNAV_" . $parent_page["name"],
					$parent_page["name"],
					str_replace( "_", " ", $parent_page["name"] )
				);
			}
		}
		$HTML .= "</ul>";
		
		return $HTML;
	}


	private function get_root_parent( $current_page )
	{
		if( $current_page["parent_id"] == NULL )
		{
			return $current_page;
		}
		else
		{
			$page_arr = $this->db_select(
				"page",
				"*",
				array(
					"id",
					"=",
					$current_page["parent_id"],
					SQLITE3_INTEGER
				)
			);
			
			$current_page = $this->get_root_parent( $page_arr[0] );
		}
		return $current_page;
	}	
	
	
	private function page_generate_subnav_get_path_to_root( $current_page, $arr )
	{
		$arr[] = $current_page["id"];
		if( $current_page["parent_id"] == NULL )
		{
			return $arr;
		}
		else
		{
			$page_arr = $this->db_select(
				"page",
				"*",
				array(
					"id",
					"=",
					$current_page["parent_id"],
					SQLITE3_INTEGER
				)
			);
			
			$arr = $this->page_generate_subnav_get_path_to_root( $page_arr[0], $arr );
		}
		return $arr;
	}
	
	
	private function page_generate_subnav( $ids, $children = FALSE, $parent_id = NULL, $rellink = NULL )
	{
		$HTML = "<ul>";
		
		if( !$children )
		{
			$pages = $this->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"IS",
					NULL,
					SQLITE3_INTEGER
				),
				array(
					"sort_id",
					"ASC"
				)
			);
			$cur_id = array_shift( $ids );
			foreach( $pages as $page )
			{
				if( $page["id"] == $cur_id )
				{
					$rellink = "/" . $page["name"];
					$HTML .= sprintf(
						"<li id='%s'><a class='%s' href='%s'>%s</a>",
						"SUBNAV" . str_replace("/", "_", $rellink),
						"active_link",
						$rellink,
						str_replace( "_", " ", $page["name"] )
					);
					break;
				}
			}
			$HTML .= $this->page_generate_subnav( $ids, TRUE, $cur_id, $rellink );
			$HTML .= "</li>";
		}
		else
		{
			$pages = $this->db_select(
				"page",
				"*",
				array(
					"parent_id",
					"=",
					$parent_id,
					SQLITE3_INTEGER
				),
				array(
					"sort_id",
					"ASC"
				)
			);
			$cur_id = array_shift( $ids );
			foreach( $pages as $page )
			{
				if( $page["id"] == $cur_id )
				{
					$HTML .= sprintf(
						"<li id='%s'><a class='%s' href='%s'>%s</a>",
						"SUBNAV" . str_replace( "/", "_", $rellink ) . "_" . $page["name"],
						"active_link",
						$rellink . "/" . $page["name"],
						str_replace( "_", " ", $page["name"] )
					);					
				}
				else
				{
					$HTML .= sprintf(
						"<li id='%s'><a href='%s'>%s</a>",
						"SUBNAV" . str_replace( "/", "_", $rellink ) . "_" . $page["name"],
						$rellink . "/" . $page["name"],
						str_replace( "_", " ", $page["name"] )
					);
				}
				if( $page["id"] == $cur_id )
				{
					$HTML .= $this->page_generate_subnav( $ids, TRUE, $cur_id, $rellink . "/" . $page["name"] );
				}
				$HTML .= "</li>";
			}			
		}
		
		$HTML .= "</ul>";
		return $HTML;
	}
	
	
	private function page_generate_breadcrumbnav( $navids )
	{
		$cur_link = "";
		$HTML = "<p>";
		$iteration = 0;
		foreach( $navids as $id )
		{
			$page_arr = $this->db_select(
				"page",
				array(
					"name"
				),
				array(
					"id",
					"=",
					$id,
					SQLITE3_INTEGER
				)
			);
			$cur_link = $cur_link . "/" . $page_arr[0]["name"];
			if( $iteration >= 1 )
			{
				$HTML .= " -> ";
			}
			$HTML .= sprintf(
				"<a id = '%s' href='%s'>%s</a>",
				"BREADCRUMBNAV" . str_replace( "/", "_", $cur_link ),
				$cur_link,
				str_replace( "_", " ", $page_arr[0]["name"] )
			);
			$iteration += 1;
		}
		$HTML .= "</p>";
		
		return $HTML;
	}
	
	
	private function page_generate_title( $navids, $all_pages, $title )
	{
		foreach( $navids as $id )
		{
			foreach( $all_pages as $page )
			{
				if( $page["id"] == $id )
				{
					$title .= " - " . str_replace( "_", " ", $page["name"] );
				}
			}
		}
		
		return $title;
	}


	public function add_file_to_files_buffer( $id, $type, $content )
	{
		$this->files_buffer[] = array( "id" => $id, "type" => $type, "content" => $content );
	}


	public function remove_file_from_files_buffer( $id, $type )
	{
		for( $x = 0; $x < count( $this->files_buffer ); $x++ )
		{
			if( ( $this->files_buffer[$x]["id"] == $id ) && ( $this->files_buffer[$x]["type"] == $type ) )
			{
				unset( $this->files_buffer[$x] );
				return;
			}
		}
		unset( $this->files_buffer[$id] );
	}


	public function read_file_contents_from_files_buffer( $id, $type )
	{
		for( $x = 0; $x < count( $this->files_buffer ); $x++ )
		{
			if( ( $this->files_buffer[$x]["id"] == $id ) && ( $this->files_buffer[$x]["type"] == $type ) )
			{
				return $this->files_buffer[$x]["content"];
			}
		}
	}


	public function write_file_contents_to_files_buffer( $id, $type, $content )
	{
		for( $x = 0; $x < count( $this->files_buffer ); $x++ )
		{
			if( ( $this->files_buffer[$x]["id"] == $id ) && ( $this->files_buffer[$x]["type"] == $type ) )
			{
				$this->files_buffer[$x]["content"] = $content;
				return;
			}
		}
		$this->add_file_to_files_buffer( $id, $type, $content );
	}


	public function rename_file_in_files_buffer( $id_old, $type, $id_new )
	{
		for( $x = 0; $x < count( $this->files_buffer ); $x++ )
		{
			if( ( $this->files_buffer[$x]["id"] == $id_old ) && ( $this->files_buffer[$x]["type"] == $type ) )
			{
				$this->files_buffer[$x]["id"] = $id_new;
				return;
			}
		}
	}


	public function write_files_buffer_content_to_harddisk()
	{
		$type = $this->file_to_edit["type"];
		if( isset( $this->file_to_edit["id"] ) )
		{
			$id = $this->file_to_edit["id"];
		}
		else
		{
			$id = $this->file_to_edit["name"];		
		}

		$buf = read_file_contents_from_files_buffer( $id, $type );			
		$this->write_content( $buf );
	}


	public function read_files_buffer_content_from_harddisk()
	{
		$buf = $this->read_content();

		$type = $this->file_to_edit["type"];
		
		if( isset( $this->file_to_edit["id"] ) )
		{
			$id = $this->file_to_edit["id"];
		}
		else
		{
			$id = $this->file_to_edit["name"];
		}

		$this->write_file_contents_to_files_buffer( $id, $type, $buf );
	}


	public function exists_files_buffer( $id, $type )
	{
		for( $x = 0; $x < count( $this->files_buffer ); $x++ )
		{
			if( ( $this->files_buffer[$x]["id"] == $id ) && ( $this->files_buffer[$x]["type"] == $type ) )
			{
				return TRUE;
			}
		}
		return FALSE;	
	}
	
	
	public function register_observer_generate_website(
		$_generate_website_view)
	{
		$this->observer_generate_website_view = $_generate_website_view;
	}
	
	
	public function notify_observer_generate_website_view()
	{
		$this->observer_generate_website_view->update_progress_dialog();
	}
	
	public function close_observer_generate_website_view()
	{
		$this->observer_generate_website_view->Show(0);
	}
	
	
	public function sync_table_add_file(
		$_table,
		$_filepath)
	{
		date_default_timezone_set("Europe/Berlin");
		$datetime_modified = date("d.m.Y-H:i:s");
		
		$result = $this->db_select(
			$_table,
			"*",		
			array(
				"filepath",
				"=",
				$_filepath,
				SQLITE3_TEXT));

		if(empty($result))
		{
			// Create record.
			$this->db_insert(
				$_table,
				array(
					"filepath",
					"modified_date"),
				array(
					$_filepath,
					$datetime_modified),
				array(
					SQLITE3_TEXT,
					SQLITE3_TEXT));			
		}
	}
	
	
	public function sync_table_update_file(
		$_table,
		$_filepath_old,
		$_filepath)
	{
		date_default_timezone_set("Europe/Berlin");
		$datetime_modified = date("d.m.Y-H:i:s");
		
		// Make sure the record exists.
		// If it doesn't, it means that the website project is older than when
		// this feature was programmed.  Add a record in this case.
		$result = $this->db_select(
			$_table,
			"*",		
			array(
				"filepath",
				"=",
				$_filepath_old,
				SQLITE3_TEXT));
		
		if ($result == NULL)
		{
			$this->sync_table_add_file(
				$_table,
				$_filepath_old);
		}
		else
		{
			$this->db_update(
				$_table,
				array(
					"filepath",
					"modified_date"),
				array(
					$_filepath,
					$datetime_modified),
				array(
					SQLITE3_TEXT,
					SQLITE3_TEXT),					
				array(
					"filepath",
					"=",
					$_filepath_old,
					SQLITE3_TEXT));			
		}
	}			
	
	
	public function sync_table_delete_file(
		$_table,
		$_filepath)
	{
		$this->db_delete(
			$_table,
			array(
				"filepath",
				"=",
				$_filepath,
				SQLITE3_TEXT));
	}
	
	
	public function init_sync_file_tables(
		$_type,
		$_array)
	{
		foreach($_array as $element)
		{
			$this->sync_table_update_file(
				"sync_file_generate",
				$_type . 
				DIRECTORY_SEPARATOR .
				$element,
				$_type .
				DIRECTORY_SEPARATOR .
				$element);
		}
	}
	
	
	public function sync_table_add_page(
		$_table,
		$_generated_content,
		$_filepath,
		$_generated)
	{
		date_default_timezone_set("Europe/Berlin");
		$datetime_modified = date("d.m.Y-H:i:s");
		
		// Create record.
		$this->db_insert(
			$_table,
			array(
				"generated_content",
				"filepath",
				"generated",
				"modified_date"),
			array(
				$_generated_content,
				$_filepath,
				$_generated,
				$datetime_modified),
			array(
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_INTEGER,
				SQLITE3_TEXT));
	}
	
	
	public function sync_table_select_page(
		$_table,
		$_filepath)
	{
		return $this->db_select(
			$_table,
			"*",
			array(
				"filepath",
				"=",
				$_filepath,
				SQLITE3_TEXT));
	}
	
	
	public function sync_table_update_page(
		$_table,
		$_generated_content,
		$_filepath,
		$_generated)
	{
		date_default_timezone_set("Europe/Berlin");
		$datetime_modified = date("d.m.Y-H:i:s");
		
		$this->db_update(
			$_table,
			array(
				"generated_content",
				"filepath",
				"generated",
				"modified_date"),
			array(
				$_generated_content,
				$_filepath,
				$_generated,
				$datetime_modified),
			array(
				SQLITE3_TEXT,
				SQLITE3_TEXT,
				SQLITE3_INTEGER,
				SQLITE3_TEXT),
			array(
				"filepath",
				"=",
				$_filepath,
				SQLITE3_TEXT));
	}


	public function sync_table_update_page_already_generated(
		$_table,
		$_filepath,
		$_generated)
	{	
		$this->db_update(
			$_table,
			array(
				"filepath",
				"generated"),
			array(
				$_filepath,
				$_generated),
			array(
				SQLITE3_TEXT,
				SQLITE3_INTEGER),
			array(
				"filepath",
				"=",
				$_filepath,
				SQLITE3_TEXT));
	}
	
	
	public function sync_table_delete_page($_table)
	{
		$this->db_delete(
			$_table,
			array(
				"generated",
				"!=",
				1,
				SQLITE3_INTEGER));
	}	
	
	
	public function sync_file_upload_exists(
		$_table,
		$_path)
	{
		$result = $this->db_select(
			$_table,
			"*",
			array(
				"filepath",
				"=",
				$_path,
				SQLITE3_TEXT));
				
		if (empty($result) == TRUE)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}
	
	public function sync_file_upload_create(
		$_table,
		$_path)
	{
		date_default_timezone_set("Europe/Berlin");
		$datetime_modified = date("d.m.Y-H:i:s");
		
		$this->db_insert(
			$_table,
			array(
				"filepath",
				"checked",
				"uploaded_date"),
			array(
				$_path,
				1,
				$datetime_modified),
			array(
				SQLITE3_TEXT,
				SQLITE3_INTEGER,
				SQLITE3_TEXT));
				
	}
	
	public function sync_file_upload_update(
		$_table,
		$_path)
	{
		date_default_timezone_set("Europe/Berlin");
		$datetime_modified = date("d.m.Y-H:i:s");
		
		$this->db_update(
			$_table,
			array(
				"uploaded_date",
				"checked"),
			array(
				$datetime_modified,
				1),
			array(
				SQLITE3_TEXT,
				SQLITE3_INTEGER),
			array(
				"filepath",
				"=",
				$_path,
				SQLITE3_TEXT));
	}
	
	
	public function sync_file_upload_update_checked(
		$_table,
		$_path)
	{
		$this->db_update(
			$_table,
			array("checked"),
			array(1),
			array(SQLITE3_INTEGER),
			array(
				"filepath",
				"=",
				$_path,
				SQLITE3_TEXT));
	}
	
	
	public function sync_file_upload_delete(
		$_table
	)
	{
		$this->db_delete(
			$_table,
			array(
				"checked",
				"=",
				0,
				SQLITE3_INTEGER));
	}
	
	
	public function is_valid_filename(
		$_input)
	{
		$regex = "/[^\x20-\x7e]/";
		if (preg_match(
			$regex,
			$_input))
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
		
	}
	
	public static function is_valid_filename_static(
		$_input)
	{
		$regex = "/[^\x20-\x7e]/";
		if (preg_match(
			$regex,
			$_input))
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
		
	}	
}
?>
