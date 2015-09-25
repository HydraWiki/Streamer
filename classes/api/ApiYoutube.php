<?php
/**
 * Streamer
 * YouTube API
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class ApiYoutube extends ApiStreamerBase {
	/**
	 * API Entry Point
	 *
	 * @var		string
	 */
	protected $apiEntryPoint = "https://www.googleapis.com/youtube/v3/";

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		$this->service = 'youtube';
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

		/*********************/
		/* Channel           */
		/*********************/
		if (($json = $this->makeApiRequest(['channels', '?part=id,snippet,statistics&forUsername='.$this->user])) === false) {
			return false;
		}

		$channelId = false;
		if (isset($json['items'][0]['id'])) {
			$channel = $json['items'][0];
			$this->setName($channel['snippet']['title']);
			$this->setLogo($channel['snippet']['thumbnails']['high']['url']);
			$this->setLifetimeViews($channel['statistics']['viewCount']);
			$this->setFollowers($channel['statistics']['subscriberCount']);
			$channelId = $channel['id'];
			$this->setChannelUrl("https://www.youtube.com/channel/".$channelId);
		}

		if ($channelId === false) {
			return false;
		}

		/*********************/
		/* Stream Video ID   */
		/*********************/
		if (($json = $this->makeApiRequest(['search', "?part=snippet&channelId={$channelId}&eventType=live&type=video"])) === false) {
			return false;
		}

		$videoId = false;
		$search = $json['items'][0];
		if (isset($search['snippet']['liveBroadcastContent']) && $search['snippet']['liveBroadcastContent'] == 'live') {
			$videoId = $search['id']['videoId'];
		}

		if ($videoId === false) {
			//No video ID just means they are most likely offline.
			$this->setOnline(false);
			return true;
		}

		/*********************/
		/* Video             */
		/*********************/
		$json = $this->makeApiRequest(['videos', "?part=id,snippet,liveStreamingDetails&id={$videoId}"]);

		if ($json !== false) {
			$video = $json['items'][0];

			$this->setThumbnail($video['snippet']['thumbnails']['maxres']['url']);
			$this->setViewers($video['liveStreamingDetails']['concurrentViewers']);
			$this->setStatus($video['snippet']['title']);
			//$this->setDoing($json['type']['name']); //@TODO: YouTube generates a game channel hash ID.  Unfortunately it does not appear to be in the API data yet.  Example: https://gaming.youtube.com/game/UCfcK1A4HBfoVC5PgTG-e7Ug
			$this->setOnline(true);
		} else {
			$this->setOnline(false);
		}

		$this->updateCache();

		return true;
	}

	/**
	 * Return an assembled URL to use for API requests.  YouTube requires an API key to be reliable.
	 *
	 * @access	protected
	 * @param	array	URL bits to put between directory separators.
	 * @return	string	Full URL
	 */
	protected function getFullRequestUrl($bits) {
		global $wgYouTubeApiKey;

		return parent::getFullRequestUrl($bits).'&key='.$wgYouTubeApiKey;
	}
}
