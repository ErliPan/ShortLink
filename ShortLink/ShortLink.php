<?php
//Class for generate my website style
require_once("../lib/Site.php");

date_default_timezone_set("Europe/Rome");
if (!isset($_SESSION)) {
	session_start();
}
//Autoload class so i don't have to require it
spl_autoload_register(function ($className) {
	$className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
	include_once __DIR__ . '/class/' . $className . '.php';
});

//Add new shortUrl
if (isset($_GET["new"]) && isset($_GET["timer"]) && isset($_GET[Config::shortlinkCsrfName])) {
	$ctr = new Controller(Config::shortlinkCsrfName, $_GET[Config::shortlinkCsrfName]);
	echo $ctr->addShort($_GET["timer"], $_GET["new"], isset($_GET["demo"]));
	die();
}

/*
Detect if it's a shorted url like "thisfile.php?A4f" key($_GET) will return "A4f"
The short is base62 so with 2 char we can have 3844 combination, with 3 char we'll get 238328
for private use 2 char is enought. 3 is the max supported but it can be expanded easily
*/
if (key($_GET) !== NULL && strlen(key($_GET)) < 4) {
	//Try to check if the short is associated to some url
	$ret = Controller::getShort(key($_GET));
	//redirect if short exists
	if ($ret != false) {
		header('Location:' . $ret);
		die();
	} else {
		//Else show 404 screen saying short doesn't exists
		$site = new Site();
		$site->card("?", "c6", "/lib/img/icon/home.png", "Torna indietro", "");
		$site->template1("ERRORE 404", "Il link non esiste o è stato eliminato");
		die();
	}
}

//Demo version is limited to 5 min. Just for try it out
if (key($_GET) == "demo") {
	AuthClass::set_csrf_token(Config::shortlinkCsrfName);
	$site = new Site(View::indexDemo());
	$site->template2("SHORTLINK");
	die();
}

//Is user is logged he'll access the full feature
if (AuthClass::isLogged()) {
	if (isset($_GET[Config::shortlinkCsrfName])) {
		$ctr = new Controller(Config::shortlinkCsrfName, $_GET[Config::shortlinkCsrfName]);
		//t means "tracking" but with this word some adblock cause issues thinking it's a ad
		//Show who clicked and other infos
		if (isset($_GET["t"])) {
			$site = new Site($ctr->linkStats($_GET["t"]));
			$site->template2("SHORTLINK");
			die();
		}
		//Dont need csrf token for deleting because they have individual secure hash
		if (isset($_GET["delete"])) {
			$ctr->manageAction($_GET["delete"]);
			die();
		}
	}
	//Manage page for deleting shorts and show who clicked
	if (key($_GET) == "manage") {
		$site = new Site(View::indexManage());
		$site->template2("SHORTLINK");
		die();
	}
	//Main page
	else {
		AuthClass::set_csrf_token(Config::shortlinkCsrfName);
		$site = new Site(View::indexMain());
		$site->template2("SHORTLINK");
		die();
	}
}
//If not logged in it will redirect to login page
else {
	if (isset($_POST["user"]) && isset($_POST["pass"]) && !AuthClass::auth($_POST["user"], $_POST["pass"])) {
		//Login failed
		$site = new Site();
		$site->card("?", "c6", "/lib/img/icon/home.png", "Torna indietro", "");
		$site->template1("ERRORE", "Accesso negato, credenziali sbagliate");
	} else {
		$site = new Site(View::adminLogin());
		$site->template2("LOGIN");
	}
	die();
}
