<?php
/**
 * Streamer
 * Streamer Info Special Page
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class SpecialStreamerInfo extends SpecialPage {
	/**
	 * Output HTML
	 *
	 * @var		string
	 */
	private $content;


	/**
	 * Main Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct() {
		global $wgRequest, $wgUser, $wgOut;

		parent::__construct('StreamerInfo');

		$this->wgRequest	= $wgRequest;
		$this->wgUser		= $wgUser;
		$this->output		= $this->getOutput();

		$this->DB = wfGetDB(DB_MASTER);
	}

	/**
	 * Main Executor
	 *
	 * @access	public
	 * @return	void	[Outputs to screen]
	 */
	public function execute($subpage) {
		if (!$this->wgUser->isAllowed('edit_streamer_info')) {
			throw new PermissionsError('edit_streamer_info');
			return;
		}

		$this->mouse = mouseNest::getMouse();
		$this->mouse->output->addTemplateFolder(STREAMER_EXT_DIR.'/templates');

		$this->mouse->output->loadTemplate('streamerinfo');

		$this->output->addModules('ext.streamer');

		$this->streamerInfoPage();

		$this->setHeaders();

		$this->output->addHTML($this->content);
	}

	/**
	 * Display database listing of streamer information.
	 *
	 * @access	private
	 * @return	void	[Outputs to screen]
	 */
	private function streamerInfoPage() {


		$this->output->setPageTitle(wfMessage('streamer_info_page_title')->escaped());
		$this->content = $this->mouse->output->streamerinfo->streamerInfoPage($streamers);
	}

	/**
	 * Hides special page from SpecialPages special page.
	 *
	 * @access	public
	 * @return	boolean
	 */
	public function isListed() {
		return $this->wgUser->isAllowed('edit_streamer_info');
	}

	/**
	 * Lets others determine that this special page is restricted.
	 *
	 * @access	public
	 * @return	boolean	False
	 */
	public function isRestricted() {
		return true;
	}
}
