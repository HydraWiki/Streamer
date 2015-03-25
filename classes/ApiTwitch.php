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
	 * User Identifier
	 *
	 * @var		string
	 */
	private $user = null;

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
		# code...
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

		$rawJson = Http::request('GET', $this->getFullRequestUrl(['streams', $this->user]), $this->getRequestOptions());

		if ($rawJson === false) {
			return false;
		}

		$json = @json_decode($rawJson, true);

		if (!is_array($json)) {
			return false;
		}

		if (array_key_exists('stream', $json) && $json['stream'] !== null) {
			$this->setDoing($json['stream']['game']);
			$this->setViewers($json['stream']['viewers']);
			$this->setLogo($json['stream']['channel']['logo']);
			$this->setThumbnail($json['stream']['preview']['large']);
			$this->setStatus($json['stream']['channel']['status']);
			$this->setName($json['stream']['channel']['display_name']);
			$this->setLifetimeViews($json['stream']['channel']['views']);
			$this->setOnline(true);
		} else {
			$this->setOnline(false);
		}

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
