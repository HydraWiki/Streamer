<?php
/**
 * Streamer
 * Twitch API
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class ApiTwitch extends ApiStreamerBase {
	/**
	 * API Entry Point
	 *
	 * @var		string
	 */
	private $apiEntryPoint = "https://api.twitch.tv/kraken/";

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		$this->service = 'twitch';
		parent::__construct();
	}

	/**
	 * Set the user identifier.
	 * This function should do any validation and return a boolean.
	 *
	 * @access	public
	 * @return	string	User Identifier
	 * @return	boolean	Success
	 */
	public function setUser($user) {
		if (preg_match("#^[\w]+$#i", $user) !== 1) {
			return false;
		}
		$this->user = $user;

		if ($this->loadCache()) {
			return true;
		}

		$rawJson = Http::request('GET', $this->getFullRequestUrl(['channels', $this->user]), $this->getRequestOptions());

		$json = $this->parseRawJson($rawJson);

		if ($json === false) {
			return false;
		}

		if (isset($json['display_name'])) {
			$this->setName($json['display_name']);
			$this->setLogo($json['logo']);
			$this->setDoing($json['game']);
			$this->setLifetimeViews($json['views']);
			$this->setChannelUrl($json['url']);
			$this->setStatus($json['status']);
			$this->setFollowers($json['followers']);
		}

		$rawJson = Http::request('GET', $this->getFullRequestUrl(['streams', $this->user]), $this->getRequestOptions());

		$json = $this->parseRawJson($rawJson);

		if (array_key_exists('stream', $json) && $json['stream'] !== null) {
			$this->setViewers($json['stream']['viewers']);
			$this->setThumbnail($json['stream']['preview']['large']);
			$this->setOnline(true);
		} else {
			$this->setOnline(false);
		}

		$this->updateCache();

		return true;
	}

	/**
	 * Return an assembled URL to use for API requests.
	 *
	 * @access	public
	 * @param	array	URL bits to put between directory separators.
	 * @return	string	Full URL
	 */
	public function getFullRequestUrl($bits) {
		return $this->apiEntryPoint.implode('/', $bits).'/';
	}
}
