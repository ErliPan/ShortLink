<?php

namespace Database;

use PDO;

/**
 * Database class using PDO so it's injection resistant
 * it can use both SQLite and MySQL but you'll need to change some query
 * 
 * !!! Currently using SQLite so no database name and user !!!
 * 
 * @param string $nomeDB name of MySQL db
 * @param string $user name if user for connecting to MySQL
 */
class Database
{

	private $connessione;

	function __construct($nomeDB, $user, $pwd = "", $host = "127.0.0.1")
	{
		try {
			//$this->connessione = new PDO("mysql:host=$host;dbname=$nomeDB", $user, $pwd);
			$this->connessione = new PDO("sqlite:" . __DIR__ . "/sql.db");
			//$this->connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e) {
			echo "Errore: " . $e->getMessage();
			die();
		}
	}

	//SQL functions
	public function deleteOld()
	{
		$time = time();
		$sql = "SELECT shortID FROM Shortener WHERE expire < '$time'";
		$stmt = $this->connessione->prepare($sql);
		$stmt->execute();
		$app = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();

		foreach ($app as $key => $value) {
			$this->delete($value["shortID"]);
		}
	}
	public function delete($id)
	{
		$this->deleteClicks($id);
		$this->deleteShort($id);
	}
	public function deleteByHash($hash)
	{
		$sql = "SELECT shortID FROM Shortener WHERE tracking = :hash";
		$stmt = $this->connessione->prepare($sql);
		$stmt->bindParam(':hash', $hash);
		$stmt->execute();
		$app = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		if (isset($app[0]["shortID"])) {
			$this->delete($app[0]["shortID"]);
		}
	}
	public function deleteClicks($id)
	{
		$sql = "delete from Clicks where shortID = :id";
		$stmt = $this->connessione->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$stmt->closeCursor();
	}
	public function deleteShort($id)
	{
		$sql = "delete from Shortener where shortID = :id";
		$stmt = $this->connessione->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->execute();
		$stmt->closeCursor();
	}
	public function addShort($ShortClass)
	{
		$sql = "insert into Shortener(short, full, expire, tracking)
    values(:Short, :url, :timer, :tracking)";

		$stmt = $this->connessione->prepare($sql);
		$stmt->bindParam(':Short', $ShortClass->short);
		$stmt->bindParam(':url', $ShortClass->url);
		$stmt->bindParam(':timer', $ShortClass->timer);
		$stmt->bindParam(':tracking', $ShortClass->tracking);
		$stmt->execute();
		$stmt->closeCursor();
		return $ShortClass->short;
	}
	public function getShort($key)
	{
		$time = time();
		$sql = "SELECT short, full, shortID FROM Shortener
    WHERE short = :key AND expire > '$time'";

		$stmt = $this->connessione->prepare($sql);
		$stmt->bindParam(':key', $key);
		$stmt->execute();
		$app = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $app;
	}
	public function trackUser($id, $Tracking)
	{
		$sql = "insert into Clicks(
      shortID, clientAddr, clickTime, clientLang, userAgent
      ) values(:id, :ipAdd, :time, :lang, :agent)";

		$stmt = $this->connessione->prepare($sql);
		$stmt->bindParam(':id', $id);
		$stmt->bindParam(':ipAdd', $Tracking->ipAdd);
		$stmt->bindParam(':time', $Tracking->time);
		$stmt->bindParam(':lang', $Tracking->lang);
		$stmt->bindParam(':agent', $Tracking->agent);
		$stmt->execute();
		$stmt->closeCursor();
	}
	public function getShortList()
	{
		$sql = "SELECT full, short, expire, tracking, count(clientAddr) as clicks
      FROM Shortener LEFT JOIN Clicks ON Shortener.shortID = Clicks.shortID
      GROUP BY Shortener.shortID;";

		$stmt = $this->connessione->prepare($sql);
		$stmt->execute();
		$app = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $app;
	}
	public function getClickList($track)
	{
		$sql = "SELECT full, clientAddr as ip, clickTime, clientLang, userAgent
      FROM Shortener INNER JOIN Clicks ON Shortener.shortID = Clicks.shortID
      WHERE tracking = :track ORDER BY clickTime DESC";

		$stmt = $this->connessione->prepare($sql);
		$stmt->bindParam(':track', $track);
		$stmt->execute();
		$app = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$stmt->closeCursor();
		return $app;
	}
}
