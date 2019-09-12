<?php


/**
 * Static class for login and csrf token generation/validation
 * Cryptographically secure using random_bytes
 */
class AuthClass
{
	/**
	 * Authenticate user, if auth is successful it reloads the page removing querystring 
	 * so if user reloads the page it wont prompt to resend post data
	 * @param string $userInput username
	 * @param string $passInput password
	 * @return bool if login is successful or not
	 */
	public static function auth($userInput, $passInput)
	{
		if ($userInput == Config::adminUser && hash("sha256", $passInput) == Config::adminPass) {

			$cookie = hash("sha256", Config::adminPass . date("Y-m-d"));
			setcookie("auth", $cookie, NULL, NULL, NULL, NULL, true);
			$_SESSION["cookie"] = $cookie;

			header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
			return true;
		}
		return false;
	}
	/**
	 * @return bool if user is logged
	 */
	public static function isLogged()
	{
		return isset($_SESSION["cookie"]) && isset($_COOKIE["auth"]) && $_SESSION["cookie"] == $_COOKIE["auth"];
	}

	/**
	 * @param string $name name for the csrf token
	 * @return string csrf token value (HEX)
	 */
	public static function set_csrf_token($name)
	{
		$csrf_token = bin2hex(random_bytes(32));
		setcookie($name, $csrf_token);
		$_SESSION[$name] = $csrf_token;
		return $csrf_token;
	}

	/**
	 * @param string $name name of the csrf token
	 * @param string $input csrf value in HEX
	 * @return bool if is verified or not
	 */
	public static function verify_csrf_token($name, $input)
	{
		if (isset($_SESSION[$name]) && $_SESSION[$name] == $input) {
			self::set_csrf_token($name);
			return true;
		} else {
			return false;
		}
	}
}
