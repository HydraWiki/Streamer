<?php
/**
 * Streamer
 * Beam API
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class ApiBeam extends ApiStreamerBase {
	/**
	 * API Entry Point
	 *
	 * @var		string
	 */
	protected $apiEntryPoint = "https://beam.pro/api/v1/";

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		$this->service = 'beam';
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
		/* Search            */
		/*********************/
		if (($json = $this->makeApiRequest(['users', 'search?query='.$this->user])) === false) {
			return false;
		}

		$userId = false;
		foreach ($json as $user) {
			//The request might return more than one user.  Loop through the results and find the correct one.
			$lowercaseUser = mb_strtolower($this->user, 'UTF-8');
			if (mb_strtolower($user['username'], 'UTF-8') == $lowercaseUser) {
				$userId = $user['id'];
				break;
			}
		}

		if ($userId === false) {
			return false;
		}

		/*********************/
		/* User              */
		/*********************/
		if (($json = $this->makeApiRequest(['users', $userId])) === false) {
			return false;
		}

		$channelId = false;
		if (isset($json['username'])) {
			$this->setName($json['username']);
			$this->setLogo("https://s3.amazonaws.com/uploads.beam.pro/avatar/{$userId}.jpg");
			$this->setChannelUrl("https://beam.pro/".$json['username']);
			$this->setLifetimeViews($json['channel']['viewersTotal']);
			$this->setFollowers($json['channel']['numFollowers']);
			$this->setViewers($json['channel']['viewersCurrent']);
			$this->setStatus($json['channel']['name']);
			$this->setOnline($json['channel']['online']);
			$channelId = $json['channel']['id'];
		}

		if ($channelId === false) {
			return false;
		}

		/*********************/
		/* Channel           */
		/*********************/
		if (($json = $this->makeApiRequest(['channels', $channelId])) === false) {
			return false;
		}

		$this->setDoing($json['type']['name']);

		$this->setThumbnail($this->getLogo()); //@TODO: If Beam.pro ever supports an actual video thumbnail it should changed here.

		$this->updateCache();

		return true;
	}

	/**
	 * Make an API request to Beam.  Beam returns error codes inside their JSON and can be handled gracefully.
	 *
	 * @access	protected
	 * @param	array	URL bits to put between directory separators.
	 * @return	mixed	Parsed JSON or false on error.
	 */
	protected function makeApiRequest($bits) {
		$json = parent::makeApiRequest($bits);

		if (isset($json['error'])) {
			return false;
		}
		return $json;
	}

	/**
	 * Return default request options for MWHttpRequest.  Includes basics such as user agent and character encoding.
	 *
	 * @access	protected
	 * @return	array	Request Options
	 */
	protected function getRequestOptions() {
		return array_merge(
			parent::getRequestOptions(),
			[
				'followRedirects'	=> true
			]
		);
	}
}
