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
	 * User Identifier
	 *
	 * @var		string
	 */
	private $user = null;

	/**
	 * Streamer Name
	 *
	 * @var		string
	 */
	private $name = null;

	/**
	 * Current Live Stream Viewers
	 *
	 * @var		integer
	 */
	private $viewers = 0;

	/**
	 * Stream Logo
	 *
	 * @var		string
	 */
	private $logo = null;

	/**
	 * Stream Thumbnail
	 *
	 * @var		string
	 */
	private $thumbnail = null;

	/**
	 * Stream Status Message
	 *
	 * @var		string
	 */
	private $status = null;

	/**
	 * Stream Online/Offline Status
	 *
	 * @var		boolean
	 */
	private $online = false;

	/**
	 * Lifetime Views(Previous, current, and live.)
	 *
	 * @var		integer
	 */
	private $lifetimeViews = 0;

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		global $wgServer, $wgVersion;

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
	 * Return default request options for MWHttpRequest.  Includes basics such as user agent and character encoding.
	 *
	 * @access	public
	 * @return	array	Request Options
	 */
	public function getRequestOptions() {
		return [
			'userAgent'	=> $this->userAgent,
			'timeout'	=> 5
		];
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
		$this->name = $name;
	}

	/**
	 * Return the streamer name
	 *
	 * @access	public
	 * @return	string	Streamer Name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set the current live viewers.
	 *
	 * @access	protected
	 * @return	integer	Current Live Viewers
	 * @return	void
	 */
	protected function setViewers($viewers) {
		$this->viewers = intval($viewers);
	}

	/**
	 * Return the current live viewers.
	 *
	 * @access	public
	 * @return	integer	Current Live Viewers
	 */
	public function getViewers() {
		return $this->viewers;
	}

	/**
	 * Set a logo for this stream.
	 *
	 * @access	protected
	 * @return	string	Fully qualified URL to the logo.
	 * @return	void
	 */
	protected function setLogo($logo) {
		$this->logo = $logo;
	}

	/**
	 * Return a logo for this stream.
	 *
	 * @access	public
	 * @return	string	Fully qualified URL to the logo.
	 */
	public function getLogo() {
		return $this->logo;
	}

	/**
	 * Set a thumbnail for this stream.
	 *
	 * @access	protected
	 * @return	string	Fully qualified URL to the thumbnail.
	 * @return	void
	 */
	protected function setThumbnail($thumbnail) {
		$this->thumbnail = $thumbnail;
	}

	/**
	 * Return a thumbnail for this stream.
	 *
	 * @access	public
	 * @return	string	Fully qualified URL to the thumbnail.
	 */
	public function getThumbnail() {
		return $this->thumbnail;
	}

	/**
	 * Set a status message for this stream.
	 *
	 * @access	protected
	 * @return	string	Status Message
	 * @return	void
	 */
	protected function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * Return a status message for this stream.
	 *
	 * @access	public
	 * @return	string	Status Message
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set the online/offline streaming status.
	 *
	 * @access	protected
	 * @return	boolean	True, Online.  False, Offline
	 * @return	void
	 */
	protected function setOnline($online) {
		$this->online = (bool) $online;
	}

	/**
	 * Return the online/offline streaming status.
	 *
	 * @access	public
	 * @return	boolean	True, Online.  False, Offline
	 */
	public function getOnline() {
		return $this->online;
	}

	/**
	 * Set the lifetime stream views.(Previous, current, and live.)
	 *
	 * @access	protected
	 * @return	integer	Lifetime Views
	 * @return	void
	 */
	protected function setLifetimeViews($lifetimeViews) {
		$this->lifetimeViews = intval($lifetimeViews);
	}

	/**
	 * Return the lifetime stream views.(Previous, current, and live.)
	 *
	 * @access	public
	 * @return	integer	Lifetime Views
	 */
	public function getLifetimeViews() {
		return $this->lifetimeViews;
	}

	/**
	 * Set what the streamer is currently doing.
	 *
	 * @access	protected
	 * @return	string	Currently Doing
	 * @return	void
	 */
	protected function setDoing($doing) {
		$this->doing = $doing;
	}

	/**
	 * Return what the streamer is currently doing.
	 *
	 * @access	public
	 * @return	string	Currently Doing
	 */
	public function getDoing() {
		return $this->doing;
	}
}
