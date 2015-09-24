<?php
/**
 * Streamer
 * Azubu API
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class ApiAzubu extends ApiStreamerBase {
	/**
	 * API Entry Point
	 *
	 * @var		string
	 */
	private $apiEntryPoint = "http://api.azubu.tv/";

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		$this->service = 'azubu';
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

		$rawJson = Http::request('GET', $this->apiEntryPoint.'public/channel/list?channels='.$this->user, $this->getRequestOptions());

		$json = $this->parseRawJson($rawJson);

		if ($json === false) {
			return false;
		}

		$json = $json['data'][0];
		if (isset($json['user']['display_name'])) {
			$this->setName($json['user']['display_name']);
			$this->setLogo($json['user']['profile']['url_photo_large']);
			$this->setDoing($json['category']['title']);
			$this->setViewers($json['view_count']);
			$this->setLifetimeViews($json['vods_view_count']);
			$this->setChannelUrl($json['url_channel']);
			$this->setStatus($json['title']);
			$this->setFollowers($json['followers_count']);
			$this->setOnline($json['is_live']);
			$this->setThumbnail($json['url_thumbnail']); //@TODO: If Azubu.tv ever supports an actual video thumbnail it should changed here.
		}

		$this->updateCache();

		return true;
	}
}
