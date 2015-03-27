<?php
/**
 * Streamer
 * Streamer Template
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class StreamerTemplate {
	/**
	 * Function Documentation
	 *
	 * @access	public
	 * @return	void
	 */
	static public function get($template) {
		$function = '_'.$template;
		if (method_exists('StreamerTemplate', $function)) {
			return self::$function();
		}

		return false;
	}

	/**
	 * Built In 'block' template.
	 *
	 * @access	public
	 * @return	string	HTML
	 */
	static public function _block() {
		$html = "
			<div class='stream block'>
				<div class='logo'><img src='{{#if:%THUMBNAIL%|%THUMBNAIL%|%LOGO%}}'/></div>
				<div class='stream_info'>
					<div class='name'><a href='%LINK%'>%NAME%</a></div>
					<div class='online {{#ifeq:%ONLINE%|1|live|offline}}'><div class='dot'></div><div class='text'>{{#ifeq:%ONLINE%|1|".wfMessage('stream_online')->escaped()."|".wfMessage('stream_offline')->escaped()."}}</div></div>
				</div>
			</div>";

		return $html;
	}

	/**
	 * Built In 'live' template.
	 *
	 * @access	public
	 * @return	string	HTML
	 */
	static public function _live() {
		$html = "
			<div class='stream live'>
				<div class='stream_info'>
					<div class='name'><a href='%LINK%'>%NAME%</a></div>
					<div class='online {{#ifeq:%ONLINE%|1|live|offline}}'><div class='dot'></div><div class='text'>{{#ifeq:%ONLINE%|1|".wfMessage('stream_online')->escaped()."|".wfMessage('stream_offline')->escaped()."}}</div></div>
				</div>
			</div>";

		return $html;
	}

	/**
	 * Built In 'live' template.
	 *
	 * @access	public
	 * @return	string	HTML
	 */
	static public function _minilive() {
		$html = "
			<div class='stream minilive'>
				<div class='stream_info'>
					<div class='name'><a href='%LINK%'>%NAME%</a></div>
					<div class='online {{#ifeq:%ONLINE%|1|live|offline}}'><div class='dot'></div></div>
				</div>
			</div>";

		return $html;
	}

	/**
	 * Built In 'link' template.
	 *
	 * @access	public
	 * @return	string	HTML
	 */
	static public function _link() {
		$html = "<div class='name'><a href='%LINK%'>%NAME%</a></div>";

		return $html;
	}

	/**
	 * Built In 'viewers' template.
	 *
	 * @access	public
	 * @return	string	HTML
	 */
	static public function _viewers() {
		$html = "
			<div class='stream viewers'>
				<div class='stream_info'>
					<div class='name'><a href='%LINK%'>%NAME%</a></div>
					<div class='online {{#ifeq:%ONLINE%|1|live|offline}}'><div class='dot'></div><div class='text'>%VIEWERS%</div></div>
				</div>
			</div>";

		return $html;
	}

	/**
	 * Built In 'thumbnail' template.
	 *
	 * @access	public
	 * @return	string	HTML
	 */
	static public function _thumbnail() {
		$html = "
			<div class='stream thumbnail'>
				<div class='logo'><img src='{{#if:%THUMBNAIL%|%THUMBNAIL%|%LOGO%}}'/></div>
			</div>";

		return $html;
	}
}
