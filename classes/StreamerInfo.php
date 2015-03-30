<?php
/**
 * Streamer
 * Streamer Info
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class StreamerInfo {
	/**
	 * Null Service
	 *
	 * @var		constant
	 */
	const SERVICE_NULL = 0;

	/**
	 * Twitch Service
	 *
	 * @var		constant
	 */
	const SERVICE_TWITCH = 1;

	/**
	 * Service Constant Map
	 *
	 * @var		array
	 */
	static private $serviceConstants = [
		'twitch'	=> self::SERVICE_TWITCH
	];

	/**
	 * Mediawiki Database Object
	 *
	 * @var		object
	 */
	private $DB = false;

	/**
	 * Data holder for database values.
	 *
	 * @var		array
	 */
	private $data = [];

	/**
	 * Fully loaded from the database.
	 *
	 * @var		boolean
	 */
	public $isLoaded = false;

	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		$this->DB = wfGetDB(DB_MASTER);
	}

	/**
	 * Function Documentation
	 *
	 * @access	public
	 * @param	string	Service Name
	 * @param	string	User Name on the Service
	 * @return	mixed	Object on successful object creation, otherwise false on error.
	 */
	static public function newFromServiceAndName($service, $name) {
		$streamerInfo = new StreamerInfo();

		$serviceId = self::getServiceId($service);

		$success = $streamerInfo->setService($serviceId);

		$success = $streamerInfo->setRemoteName($name);

		if (!$success) {
			return false;
		}

		return $streamerInfo;
	}

	/**
	 * Load a new object from a database row.
	 *
	 * @access	public
	 * @param	array	Raw database row of streamer information.
	 * @return	mixed	Object on successful object creation, otherwise false on error.
	 */
	static public function newFromRow($data) {
		if ($data['streamer_id'] > 0) {
			$streamerInfo = new StreamerInfo();

			$streamerInfo->setService($data['service']);
			$streamerInfo->setRemoteName($data['remote_name']);
			$streamerInfo->setDisplayName($data['display_name']);
			$title = Title::newFromDBkey($data['page_title']);
			if ($title !== null) {
				$streamerInfo->setPageTitle($title);
			}

			$streamerInfo->isLoaded = true;
			return $streamerInfo;
		}
		return false;
	}

	/**
	 * Return the service constant for the named service.
	 *
	 * @access	public
	 * @param	string	Service Name
	 * @return	integer	Service Constant
	 */
	static public function getServiceId($service) {
		$service = strtolower($service);
		if (array_key_exists($service, $serviceConstants)) {
			return $serviceConstants[$service];
		}
		return self::SERVICE_NULL;
	}

	/**
	 * Load from the database.
	 *
	 * @access	public
	 * @return	void
	 */
	public function load() {
		if (!$this->isLoaded) {
			$result = $this->DB->select(
				['streamer'],
				['*'],
				[
					'service'		=> $this->getService(),
					'remote_name'	=> $this->getRemoteName()
				],
				__METHOD__
			);

			$row = $result->fetchRow();

			if (is_array($row)) {
				$this->data = $row;
				$title = Title::newFromDBkey($row['page_title']);
				if ($title !== null) {
					$streamerInfo->setPageTitle($title);
				} else {
					$this->data['page_title'] = null;
				}
			}
		}
		$this->isLoaded = true;
	}

	/**
	 * Return if a database entry exists.
	 *
	 * @access	public
	 * @return	boolean	Database Entry Exists
	 */
	public function exists() {
		$this->load();
		return $this->data['streamer_id'] > 0;
	}

	/**
	 * Set the service ID.
	 *
	 * @access	public
	 * @return	integer	Service Number from Constant
	 * @return	boolean	Success
	 */
	public function setService($service) {
		$this->data['service'] = intval($service);
	}

	/**
	 * Return the service ID.
	 *
	 * @access	public
	 * @return	integer	Service ID
	 */
	public function getService() {
		$this->load();
		return intval($this->data['service']);
	}

	/**
	 * Set the streamer name.
	 *
	 * @access	public
	 * @return	string	Streamer Name
	 * @return	boolean	Success
	 */
	public function setRemoteName($name) {
		if (!empty($name)) {
			$this->data['remote_name'] = $name;
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Return the streamer name
	 *
	 * @access	public
	 * @return	string	Streamer Name
	 */
	public function getRemoteName() {
		$this->load();
		return $this->data['remote_name'];
	}

	/**
	 * Set the display name for this streamer.
	 *
	 * @access	public
	 * @return	mixed	[Optional] Display Name - Set to null to null out in the database.
	 * @return	void
	 */
	public function setDisplayName($displayName = null) {
		$this->data['display_name'] = $displayName;
	}

	/**
	 * Return the display name for this streamer.
	 *
	 * @access	public
	 * @return	string	Display Name
	 */
	public function getDisplayName() {
		return $this->data['display_name'];
	}

	/**
	 * Set the Page Title for this streamer.
	 *
	 * @access	public
	 * @return	object	Title
	 * @return	void
	 */
	public function setPageTitle(Title $title) {
		$this->data['page_title'] = $title;
	}

	/**
	 * Return the page title for this streamer.
	 *
	 * @access	public
	 * @return	object	Title
	 */
	public function getPageTitle() {
		return $this->data['page_title'];
	}
}
