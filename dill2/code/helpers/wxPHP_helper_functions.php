<?php
/*******************************************************************************
Tool:  dill2

File:  dill2/code/classes/wxDill2Frame.php

Author:  Jannik Haberbosch

Year: 2019

Info:  Provides helper functions to be used for wxPHP specifically.

*******************************************************************************/

/*
Disables a wxphp control, such as listboxes, ...
*/
function wxphp_disable_control($_control)
{
	$_control->Enable(FALSE);
}


/*
Disables a wxphp menu item.
*/
function wxphp_disable_menu_item(
	$_wxphp_menu,
	$_id)
{
	$_wxphp_menu->Enable(
		$_id,
		FALSE);
}


/*
Enables a wxphp control, such as a listbox.
*/
function wxphp_enable_control($_control)
{
	$_control->Enable(TRUE);
}


/*
Enables a wxphp menu item.
*/
function wxphp_enable_menu_item(
	$_wxphp_menu,
	$_id)
{
	$_wxphp_menu->Enable(
		$_id,
		TRUE);
}
?>