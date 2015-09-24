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
	protected $apiEntryPoint = "https://api.twitch.tv/kraken/";

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

		if (($json = $this->makeApiRequest(['channels', $this->user])) === false) {
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

		$json = $this->makeApiRequest(['streams', $this->user]);

		//Twitch sort of pretends this end point does not exist when the user is not streaming.  So instead of returning false on a fake API error it is better to check and set the stream to be listed as offline.
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
	 * Return an assembled URL to use for API requests.  Twitch API end points require an extra / at the end of the URL.
	 *
	 * @access	protected
	 * @param	array	URL bits to put between directory separators.
	 * @return	string	Full URL
	 */
	protected function getFullRequestUrl($bits) {
		return parent::getFullRequestUrl($bits).'/';
	}
}
