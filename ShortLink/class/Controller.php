<?php


/**
 * Proxy layer between user and model
 * 
 * Check csrf token and make everything usable to model
 * 
 * Very specific function that shoudn't exists
 * @param string $input input in natural language
 * @return int natural language -> seconds
 */
class Controller
{

	private $csrf;

	public function __construct($csrfName, $csrfValue)
	{
		$this->csrf = AuthClass::verify_csrf_token($csrfName, $csrfValue);
	}

	public static function convertToSecond($input)
	{
		if ($input == "14 giorni") {
			return 3600 * 24 * 14;
		} else if ($input == "24 ore") {
			return 3600 * 24;
		} else if ($input == "7 giorni") {
			return 3600 * 24 * 7;
		} else if ($input == "5 anni") {
			return 3600 * 24 * 365 * 5;
		} else {
			return 3600 * 6;
		}
	}
	/**
	 * Create new shortlink
	 * @return string shorted url
	 */
	public function addShort($timer, $url, $demo)
	{

		$x = 404;
		if ($this->csrf) {
			if (!AuthClass::isLogged() || $demo) {
				$x = Model::addShort(base64_decode($url), 300);
				sleep(4);
			} else {
				$timer = self::convertToSecond($timer);
				$x = Model::addShort(base64_decode($url), $timer);
			}
		}
		return Config::site . "/" . $x;
	}
	public function linkStats($tracking)
	{
		return Model::linkStats($tracking);
	}
	/**
	 * Do action like detele a shortlink
	 * If csrf not ok it will redirect and remove querystring
	 */
	public function manageAction($action)
	{
		if ($this->csrf) {
			Model::manageAction($action);
		}
		header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?manage");
	}

	/**
	 * Get the shorturl
	 */
	public static function getShort($short)
	{
		return Model::getShort($short);
	}
}
