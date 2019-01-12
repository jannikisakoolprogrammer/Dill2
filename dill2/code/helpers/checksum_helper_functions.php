<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxDill2Frame.php

Author:  Jannik Haberbosch

Year: 2018

Info:  This file defines the main frame of the tool 'dill2'.

*******************************************************************************/

define("FILE_BUFFER_SIZE", 65536);

/*
Calculates the md5 checksum of files and returns the md5 checksum to the caller.
*/
function calculate_md5_checksum($filepath)
{
	$buffer			= "";
	$hash			= "";
	$hashing_context 	= hash_init("md5");
	$file_handle 		= fopen($filepath, "r");
	
	
	while(!feof($file_handle))
	{
		$buffer = fgets($file_handle,
					 FILE_BUFFER_SIZE);
		
		hash_update($hashing_context,
				  $buffer);
	}
	
	$hash = hash_final($hashing_context,
				    false);
	
	fclose($file_handle);

	return $hash;
}
?>