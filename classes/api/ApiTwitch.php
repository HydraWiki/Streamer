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

		$channel = $this->makeApiRequest(['channels', $this->user]);
		if ($channel === false) {
			return false;
		}

		if (isset($channel['display_name'])) {
			$this->setName($channel['display_name']);
			$this->setLogo($channel['logo']);
			$this->setDoing($channel['game']);
			$this->setLifetimeViews($channel['views']);
			$this->setChannelUrl($channel['url']);
			$this->setStatus($channel['status']);
			$this->setFollowers($channel['followers']);
		}

		$steam = $this->makeApiRequest(['streams', $this->user]);

		//Twitch sort of pretends this end point does not exist when the user is not streaming.  So instead of returning false on a fake API error it is better to check and set the stream to be listed as offline.
		if (array_key_exists('stream', $stream) && $stream['stream'] !== null) {
			$this->setViewers($stream['stream']['viewers']);
			$this->setThumbnail($stream['stream']['preview']['large']);
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
		global $wgTwitchClientId;

		return parent::getFullRequestUrl($bits)."/?api_version=2&client_id={$wgTwitchClientId}";
	}
}
