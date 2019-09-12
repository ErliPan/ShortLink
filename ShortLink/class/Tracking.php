<?php

/**
 * Tracking class, it track user and show some useful infos
 */
class Tracking
{


	public $ipAdd;
	public $lang;
	public $agent;
	public $time;

	public function __construct()
	{
		date_default_timezone_set("Europe/Rome");


		//If website is under Cloudflare "REMOTE_ADDR" will not be accurate
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
			$this->ipAdd = $_SERVER["HTTP_CF_CONNECTING_IP"];
		} else {
			$this->ipAdd = $_SERVER["REMOTE_ADDR"];
		}

		$this->lang  = $_SERVER["HTTP_ACCEPT_LANGUAGE"];
		$this->agent = $_SERVER["HTTP_USER_AGENT"];

		$this->time  = date("Y-m-d H:i:s");
	}
}
