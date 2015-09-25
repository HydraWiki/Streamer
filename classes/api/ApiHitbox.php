<?php
/**
 * Streamer
 * Hitbox API
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class ApiHitbox extends ApiStreamerBase {
	/**
	 * API Entry Point
	 *
	 * @var		string
	 */
	protected $apiEntryPoint = "https://api.hitbox.tv/";

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		$this->service = 'hitbox';
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
			//return true;
		}

		if (($json = $this->makeApiRequest(['media', 'live', $this->user])) === false) {
			return false;
		}

		$media = $json['livestream'][0];
		if (isset($media['media_display_name'])) {
			$this->setName($media['media_display_name']);
			$this->setLogo("http://edge.sf.hitbox.tv".$media['channel']['user_logo']);
			$this->setDoing($media['category_name']);
			$this->setViewers($media['media_views']);
			//$this->setLifetimeViews($json['vods_view_count']);
			$this->setChannelUrl($media['channel']['channel_link']);
			$this->setStatus($media['media_status']);
			$this->setFollowers($media['channel']['followers']);
			$this->setOnline((bool) $media['media_is_live']);
			$this->setThumbnail("http://edge.sf.hitbox.tv".$media['media_thumbnail_large']);
		}

		$this->updateCache();

		return true;
	}
}
