<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxDill2Frame.php

Author:  Jannik Haberbosch

Year: 2018

Info:  This file defines the main frame of the tool 'dill2'.

*******************************************************************************/

function copy_entire_tree($src, $dst)
{
	$items = scandir($src);
	foreach($items as $key => $value)
	{
		if(!in_array($value, array(".","..")))
		{
			if(is_dir($src . DIRECTORY_SEPARATOR . $value))
			{
				mkdir($dst . DIRECTORY_SEPARATOR . $value);
				copy_entire_tree($src . DIRECTORY_SEPARATOR . $value,
							  $dst . DIRECTORY_SEPARATOR . $value);
			}
			else
			{
				copy($src . DIRECTORY_SEPARATOR . $value,
					$dst . DIRECTORY_SEPARATOR . $value);
			}
		}
	}
}

?>