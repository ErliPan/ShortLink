<?php

/**
 * Config file where variable are stored
 */
class Config
{
	//Shortlink domain
	public const site = "erpn.tk";

	//DB config
	public const nameDB = "db";
	public const dbUser = "user";
	public const pass = "";

	//Admin passoword
	public const adminUser = "erli";
	//Sha1 is not secure anymore, this uses SHA256 default "admin"
	public const adminPass = "8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918";

	public const projectDir = "/ShortLink/";
	public const shortlinkCsrfName = "shortlink_csrf";

	public const protocols = array(
		"data:",
		"magnet:",
		"mailto:",
		"ftp://",
		"ftps://",
		"http://",
		"https://",
		"git://",
		"irc://",
		"irc6://",
		"ircs://",
		"ssh://",
		"vnc://"
	);
}
