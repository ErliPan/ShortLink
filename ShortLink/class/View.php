<?php

/**
 * class that return the main pages
 */
class View
{
	public static function indexMain()
	{
		return file_get_contents($_SERVER['DOCUMENT_ROOT'] . Config::projectDir . "resources/shortlink.html");
	}

	public static function indexDemo()
	{
		return file_get_contents($_SERVER['DOCUMENT_ROOT'] . Config::projectDir . "resources/shortlinkDemo.html");
	}
	public static function indexManage()
	{
		return Model::getShortList();
	}
	public static function adminLogin() {
		return file_get_contents($_SERVER['DOCUMENT_ROOT'] . Config::projectDir . "resources/adminLogin.html");
	}
}
