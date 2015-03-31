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

			$streamerInfo->setId($data['streamer_id']);
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
		if (array_key_exists($service, self::$serviceConstants)) {
			return self::$serviceConstants[$service];
		}
		return self::SERVICE_NULL;
	}

	/**
	 * Return the list of service constants.
	 *
	 * @access	public
	 * @return	array	Service Constants
	 */
	static public function getServicesList() {
		return self::$serviceConstants;
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
					'service'		=> $this->data['service'],
					'remote_name'	=> $this->data['remote_name']
				],
				__METHOD__
			);

			$row = $result->fetchRow();

			if (is_array($row)) {
				$this->data = $row;
				$title = Title::newFromDBkey($row['page_title']);
				if ($title !== null) {
					$this->setPageTitle($title);
				} else {
					$this->data['page_title'] = null;
				}
			}
		}
		$this->isLoaded = true;
	}

	/**
	 * Save to the database.
	 *
	 * @access	public
	 * @return	boolean	Success
	 */
	public function save() {
		$success = false;

		//Temporarily store and unset the streamer ID.
		$streamerId = $this->data['streamer_id'];
		unset($this->data['streamer_id']);

		$this->DB->begin();
		if ($streamerId > 0) {
			$result = $this->DB->update(
				'streamer',
				$this->data,
				['streamer_id' => $streamerId],
				__METHOD__
			);
		} else {
			$result = $this->DB->insert(
				'streamer',
				$this->data,
				__METHOD__
			);
			$streamerId = $this->DB->insertId();
		}
		if ($result !== false) {
			$success = true;
		}
		$this->DB->commit();

		$this->data['streamer_id'] = $streamerId;

		return $success;
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
	 * Set the Streamer Database ID.
	 *
	 * @access	protected
	 * @param	integer	Streamer Database ID
	 * @return	void
	 */
	protected function setId($streamerId) {
		$this->data['streamer_id'] = intval($streamerId);
	}

	/**
	 * Return the Streamer Database ID.
	 *
	 * @access	public
	 * @return	integer	Streamer Database ID
	 */
	public function getId() {
		$this->load();
		return intval($this->data['streamer_id']);
	}

	/**
	 * Set the service ID.
	 *
	 * @access	public
	 * @return	integer	Service Number from Constant
	 * @return	boolean	Success
	 */
	public function setService($service) {
		$service = intval($service);
		if (!in_array($service, self::$serviceConstants)) {
			return false;
		}

		$this->data['service'] = $service;
		return true;
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
	 * @return	boolean	Success
	 */
	public function setDisplayName($displayName = null) {
		$this->data['display_name'] = $displayName;
		return true;
	}

	/**
	 * Return the display name for this streamer.
	 *
	 * @access	public
	 * @return	string	Display Name
	 */
	public function getDisplayName() {
		$this->load();
		return $this->data['display_name'];
	}

	/**
	 * Set the Page Title for this streamer.
	 *
	 * @access	public
	 * @return	mixed	Title object or null.
	 * @return	boolean	Success
	 */
	public function setPageTitle($title) {
		if ($title instanceOf Title) {
			$this->data['page_title'] = $title;
		} else {
			$this->data['page_title'] = null;
		}
		return true;
	}

	/**
	 * Return the page title for this streamer.
	 *
	 * @access	public
	 * @return	object	Title
	 */
	public function getPageTitle() {
		$this->load();
		return $this->data['page_title'];
	}

	/**
	 * Return a title page link.
	 *
	 * @access	public
	 * @return	string	Title string form ready for parsing or false on error.
	 */
	public function getLink() {
		$link = false;
		if ($this->getPageTitle() instanceOf Title) {
			$link = $this->getPageTitle()->getFullURL();
		}

		return $link;
	}
}
