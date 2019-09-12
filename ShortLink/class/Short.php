<?php


/**
 * @param string $short shorted version eg: d3
 * @param string $url full version eg: example.com
 * @param string $timer how much time it last (sec)
 */
class Short
{

	public $short;
	public $url;
	public $timer;
	public $tracking;


	public function __construct($short, $url, $timer)
	{
		$timer += time();
		$tracking = hash("sha1", $short . $url . $timer);
		$this->short = $short;
		$this->url = $this->addProtocol($url);
		$this->timer = $timer;
		$this->tracking = $tracking;
	}

	/**
	 * Check if url contains a protocol otherwise add http://
	 * 
	 * Example input: example.com
	 * returns: http://example.com
	 * 
	 * @param string $url some url maybe with a protocol
	 * 
	 * @return string a url with a protocol
	 */
	public function addProtocol($url)
	{
		foreach (Config::protocols as $a) {
			if (stripos($url, $a) === 0) {
				return $url;
			}
		}
		return "http://" . $url;
	}
}
