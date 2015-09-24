<?php
/**
 * Streamer
 * Streamer API Base
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

abstract class ApiStreamerBase {
	/**
	 * User Agent for HTTP Requests
	 *
	 * @var		string
	 */
	private $userAgent = null;

	/**
	 * Service Identifier
	 *
	 * @var		string
	 */
	protected $service = null;

	/**
	 * User Identifier
	 *
	 * @var		string
	 */
	protected $user = null;

	/**
	 * Streamer Data
	 *
	 * @var		array
	 */
	private $data = [];

	/**
	 * Mediawiki Cache Object
	 *
	 * @var		object
	 */
	private $cache = null;

	/**
	 * Cache Key
	 *
	 * @var		string
	 */
	private $cacheKey = null;

	/**
	 * API Entry Point
	 *
	 * @var		string
	 */
	protected $apiEntryPoint = null;

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		global $wgServer, $wgVersion;

		$this->cache = wfGetCache(CACHE_ANYTHING);

		$this->userAgent = $wgServer." (MediaWiki/{$wgVersion}; Streamer ".STREAMER_VERSION.")";
	}

	/**
	 * Return a new object based on the user and service names.
	 *
	 * @access	public
	 * @param	string	User
	 * @param	string	Service
	 * @return	mixed	New service specific API class or false on error.
	 */
	final static public function newFromService($service) {
		$class = 'Api'.ucfirst($service);
		if (class_exists($class)) {
			return new $class;
		}
		return false;
	}

	/**
	 * Set the user identifier.
	 * This function should do any validation and return a boolean.
	 *
	 * @access	public
	 * @return	string	User Identifier
	 * @return	boolean	Success
	 */
	abstract public function setUser($user);

	/**
	 * Return the user identifier.
	 *
	 * @access	public
	 * @return	string	User Identifier
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * Set the streamer name.
	 *
	 * @access	protected
	 * @return	string	Streamer Name
	 * @return	void
	 */
	protected function setName($name) {
		$this->data['name'] = $name;
	}

	/**
	 * Return the streamer name
	 *
	 * @access	public
	 * @return	string	Streamer Name
	 */
	public function getName() {
		return $this->data['name'];
	}

	/**
	 * Set the current live viewers.
	 *
	 * @access	protected
	 * @return	integer	Current Live Viewers
	 * @return	void
	 */
	protected function setViewers($viewers) {
		$this->data['viewers'] = intval($viewers);
	}

	/**
	 * Return the current live viewers.
	 *
	 * @access	public
	 * @return	integer	Current Live Viewers
	 */
	public function getViewers() {
		return intval($this->data['viewers']);
	}

	/**
	 * Set a logo for this stream.
	 *
	 * @access	protected
	 * @return	string	Fully qualified URL to the logo.
	 * @return	void
	 */
	protected function setLogo($logo) {
		$this->data['logo'] = $logo;
	}

	/**
	 * Return a logo for this stream.
	 *
	 * @access	public
	 * @return	string	Fully qualified URL to the logo.
	 */
	public function getLogo() {
		return $this->data['logo'];
	}

	/**
	 * Set a thumbnail for this stream.
	 *
	 * @access	protected
	 * @return	string	Fully qualified URL to the thumbnail.
	 * @return	void
	 */
	protected function setThumbnail($thumbnail) {
		$this->data['thumbnail'] = $thumbnail;
	}

	/**
	 * Return a thumbnail for this stream.
	 *
	 * @access	public
	 * @return	string	Fully qualified URL to the thumbnail.
	 */
	public function getThumbnail() {
		return $this->data['thumbnail'];
	}

	/**
	 * Set a status message for this stream.
	 *
	 * @access	protected
	 * @return	string	Status Message
	 * @return	void
	 */
	protected function setStatus($status) {
		$this->data['status'] = $status;
	}

	/**
	 * Return a status message for this stream.
	 *
	 * @access	public
	 * @return	string	Status Message
	 */
	public function getStatus() {
		return $this->data['status'];
	}

	/**
	 * Set the online/offline streaming status.
	 *
	 * @access	protected
	 * @return	boolean	True, Online.  False, Offline
	 * @return	void
	 */
	protected function setOnline($online) {
		$this->data['online'] = (bool) $online;
	}

	/**
	 * Return the online/offline streaming status.
	 *
	 * @access	public
	 * @return	boolean	True, Online.  False, Offline
	 */
	public function getOnline() {
		return (bool) $this->data['online'];
	}

	/**
	 * Set the lifetime stream views.(Previous, current, and live.)
	 *
	 * @access	protected
	 * @return	integer	Lifetime Views
	 * @return	void
	 */
	protected function setLifetimeViews($lifetimeViews) {
		$this->data['lifetimeViews'] = intval($lifetimeViews);
	}

	/**
	 * Return the lifetime stream views.(Previous, current, and live.)
	 *
	 * @access	public
	 * @return	integer	Lifetime Views
	 */
	public function getLifetimeViews() {
		return intval($this->data['lifetimeViews']);
	}

	/**
	 * Set the number of followers.
	 *
	 * @access	protected
	 * @return	integer	Followers
	 * @return	void
	 */
	protected function setFollowers($followers) {
		$this->data['followers'] = intval($followers);
	}

	/**
	 * Return the number of followers.
	 *
	 * @access	public
	 * @return	integer	Followers
	 */
	public function getFollowers() {
		return intval($this->data['followers']);
	}

	/**
	 * Set what the streamer is currently doing.
	 *
	 * @access	protected
	 * @return	string	Currently Doing
	 * @return	void
	 */
	protected function setDoing($doing) {
		$this->data['doing'] = $doing;
	}

	/**
	 * Return what the streamer is currently doing.
	 *
	 * @access	public
	 * @return	string	Currently Doing
	 */
	public function getDoing() {
		return $this->data['doing'];
	}

	/**
	 * Set channel URL.
	 *
	 * @access	protected
	 * @return	string	Fully Qualified Channel URL
	 * @return	void
	 */
	protected function setChannelUrl($channelUrl) {
		$this->data['channelUrl'] = $channelUrl;
	}

	/**
	 * Return the channel URL.
	 *
	 * @access	public
	 * @return	string	Fully Qualified Channel URL
	 */
	public function getChannelUrl() {
		return $this->data['channelUrl'];
	}

	/**
	 * Possibly return parsed JSON data into an array.
	 *
	 * @access	protected
	 * @return	mixed	Array, parsed JSON data.  False on error.
	 */
	protected function parseRawJson($rawJson) {
		if ($rawJson === false) {
			return false;
		}

		$json = @json_decode($rawJson, true);

		if (!is_array($json)) {
			return false;
		}
		return $json;
	}

	/**
	 * Make an API request to the service.
	 *
	 * @access	protected
	 * @param	array	URL bits to put between directory separators.
	 * @return	mixed	Parsed JSON or false on error.
	 */
	protected function makeApiRequest($bits) {
		$rawJson = Http::request('GET', $this->getFullRequestUrl($bits), $this->getRequestOptions());

		$json = $this->parseRawJson($rawJson);

		if ($json === false) {
			return false;
		}
		return $json;
	}

	/**
	 * Return an assembled URL to use for API requests.
	 *
	 * @access	protected
	 * @param	array	URL bits to put between directory separators.
	 * @return	string	Full URL
	 */
	protected function getFullRequestUrl($bits) {
		return $this->apiEntryPoint.implode('/', $bits);
	}

	/**
	 * Return default request options for MWHttpRequest.  Includes basics such as user agent and character encoding.
	 *
	 * @access	protected
	 * @return	array	Request Options
	 */
	protected function getRequestOptions() {
		return [
			'userAgent'	=> $this->userAgent,
			'timeout'	=> 5
		];
	}

	/**
	 * Possibly load a cache of streamer information into the object.
	 *
	 * @access	public
	 * @return	boolean	Success
	 */
	protected function loadCache() {
		$this->setCacheKey();
		$data = $this->cache->get($this->cacheKey);

		if (is_string($data)) {
			$data = $this->parseRawJson($data);
			if (!empty($data['name'])) {
				$this->data = $data;
				return true;
			}
		}
		return false;
	}

	/**
	 * Update cache of streamer information.
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function updateCache() {
		$this->setCacheKey();
		$this->cache->set($this->cacheKey, json_encode($this->data), 300);
	}

	/**
	 * Function Documentation
	 *
	 * @access	protected
	 * @return	void
	 */
	protected function setCacheKey() {
		$this->cacheKey = wfMemcKey('streamer', $this->service, $this->user);
	}
}
