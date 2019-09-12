<?php

class Model
{
	/**
	 * @param string $len len of the random string
	 * @return string random string base62 of len $len
	 */
	public static function getRandomString($len)
	{
		$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';
		$max = strlen($characters) - 1;
		for ($i = 0; $i < $len; $i++) {
			$string .= $characters[mt_rand(0, $max)];
		}
		return $string;
	}
	/**
	 * @param mixed $ret return value from db class
	 * @param string $csrf csrf token if need protection, default ""
	 * @return string generated table, sanitized
	 */
	public static function generateTable($ret, $csrf = "")
	{
		$response = '<div class="table-responsive">
      <table class="table table-striped table-dark">';
		$response .= "<thead>";
		$response .= "<tr>";
		foreach ($ret[0] as $key2 => $row2) {

			if ($key2 != "tracking") {
				$response .= "<td><b>" . $key2 . "</b></td>";
			}
		}
		$response .= "</tr>";
		$response .= "</thead>";

		$response .= "<tbody>";
		foreach ($ret as $key => $row) {
			$response .= "<tr>";
			foreach ($row as $key2 => $row2) {
				$color = "#fff";

				//Show expire time with diffent color if expired
				if (isset($row["expire"]) && $key2 == "expire") {
					if ((int) $row2 < time()) {
						$color = "#d22222";
					} else {
						$color = "#20b820";
					}
					//convert to readable format
					$row2 = date("Y-m-d H:i:s", $row2);
				}

				//add prefix to shortUrl
				if ($key2 == "short") {
					$row2 = Config::site . "/" . $row2;
				}

				$row2 = htmlspecialchars($row2);

				//add hyperlink for every shortUrl
				if ($key2 != "tracking" && isset($row["tracking"])) {
					$response .= "<td><a style='color: $color !important' href='?" .
						Config::shortlinkCsrfName . "=$csrf&t=" . $row["tracking"] . "'>" . $row2 . "</a></td>";
				} else if ($key2 != "tracking") {
					$response .= "<td>" . $row2 . "</td>";
				}
			}
			$response .= "</tr>";
		}
		return $response . '</tbody></table></div></div><div style="height: 35px;">';
	}

	/**
	 * Create shortlink
	 * @param string $url full url
	 * @param int $timer timer in second before expire
	 * @return string shorted value
	 */
	public static function addShort($url, $timer)
	{

		$db = new Database\Database(Config::nameDB, Config::dbUser, Config::pass);

		if ($timer >= 3600 * 24 * 365) { //If it last more than 1 year it will be 3 char long
			$size = 3;
		} else {
			$size = 2;
		}

		do {
			// Generate unique index for shortUrl
			$randomString = self::getRandomString($size);
		} while (isset($db->getShort($randomString)[0]));
		$short = new Short($randomString, $url, $timer);

		$x = $db->addShort($short);

		return $x;
	}
	/**
	 * return in html of the existing shortlink
	 * @return string html code
	 */
	public static function getShortList()
	{
		$db = new Database\Database(Config::nameDB, Config::dbUser, Config::pass);

		$ret = $db->getShortList();

		if (empty($ret)) {
			$response = "<h1>Nessun link generato</h1><br />";
			$response .= "<a href='ShortLink.php'><h1>Torna indietro</h1></a>";
		} else {
			$csrf = AuthClass::set_csrf_token(Config::shortlinkCsrfName);
			$response = self::generateTable($ret, $csrf);
			$response .= "<a href='?delete=old&" . Config::shortlinkCsrfName . "=$csrf'>
      <button type='button' class='btn btn-danger' style='width:150px'>Delete expired</button></a><br>";
			$response .= "<a href='?'><button type='button' class='btn btn-outline-secondary'
      style='width:150px; margin-top: 10px'>Home</button></a>";
		}
		return $response;
	}
	/**
	 * @param string $tracking unique hash that identify this short
	 * @return string all iteration with that link in html
	 */
	public static function linkStats($tracking)
	{
		$db = new Database\Database(Config::nameDB, Config::dbUser, Config::pass);

		$ret = $db->getClickList($tracking);
		if (!isset($ret[0])) {
			$response = "<h1>Nessuna interazione</h1>";
		} else {
			$response = self::generateTable($ret);
		}
		$csrf = AuthClass::set_csrf_token(Config::shortlinkCsrfName);
		$response .= "<a href='?delete=$tracking&" . Config::shortlinkCsrfName . "=$csrf'>
    <button type='button' class='btn btn-danger' style='width:150px'>Delete</button></a><br>";
		$response .= "<a href='?manage'><button type='button' class='btn btn-outline-secondary'
    style='width:150px; margin-top: 10px'>Back</button></a>";
		return $response;
	}
	/**
	 * Act between deleting old shorlink or deliting specific shortlink
	 */
	public static function manageAction($action)
	{
		$db = new Database\Database(Config::nameDB, Config::dbUser, Config::pass);
		if ($action == "old") {
			$db->deleteOld();
		} else {
			$db->deleteByHash($_GET["delete"]);
		}
	}
	/**
	 * @param int $short shorted value
	 * @return mixed false if it doesn't exists the full url if exists
	 */
	public static function getShort($short)
	{

		$db = new Database\Database(Config::nameDB, Config::dbUser, Config::pass);
		$short = $db->getShort($short);

		if (isset($short[0]["full"])) {
			$t = new Tracking();
			$db->trackUser($short[0]["shortID"], $t);
			return $short[0]["full"];
		} else {
			return false;
		}
	}
}
