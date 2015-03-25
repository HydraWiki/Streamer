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
	 * Function Documentation
	 *
	 * @access	public
	 * @return	void
	 */
	static public function _block() {
		global $wgServer;
		$imageBase = wfExpandUrl("extensions/Streamer/images/", PROTO_CURRENT);
		$html = "
			<div class='stream'>
				<div class='logo'><img src='{{#if:%THUMBNAIL%|%THUMBNAIL%|%LOGO%}}'/></div>
				<div class='online'><img src='{$imageBase}{{#ifeq:%ONLINE%|1|online|offline}}.png'/></div><div class='name'>%NAME%</div>
			</div>";

	return $html;
	}
}
