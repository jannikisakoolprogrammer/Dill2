<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxDill2Frame.php

Author:  Jannik Haberbosch

Year: 2018

Info:  This file defines the main frame of the tool 'dill2'.

*******************************************************************************/

define("FILE_BUFFER_SIZE_ARCHIVE", 65536);

/*
Write .gz archive to disk using buffers.
*/
function write_gz_archive($filepath_tar,
					 $filepath_gz)
{
	$file_handle_tar	= fopen($filepath_tar,
						   "r");
	
	$file_handle_gz	= fopen($filepath_gz,
						   "w");
	
	$buffer			= "";
	
	
	while(!feof($file_handle_tar))
	{
		$buffer = fgets($file_handle_tar,
					 FILE_BUFFER_SIZE_ARCHIVE);
					 
		$buffer = gzencode($buffer);
		
		fwrite($file_handle_gz,
			  $buffer);
	}
	
	fclose($file_handle_tar);
	fclose($file_handle_gz);
}


/*
Read a .gz archive and save it as .tar.
*/
function read_gz_archive($filepath_gz,
					$filepath_tar)
{
	$file_handle_gz = gzopen($filepath_gz,
					     "r");
	
	$file_handle_tar = fopen($filepath_tar,
						"w");
	
	$buffer = "";
	
	while(!feof($file_handle_gz))
	{
		$buffer = gzread($file_handle_gz,
					  FILE_BUFFER_SIZE_ARCHIVE);
		
		$buffer = gzdecode($buffer);
		
		fwrite($file_handle_tar,
			  $buffer);
	}
	
	fclose($file_handle_gz);
	fclose($file_handle_tar);
}
?>