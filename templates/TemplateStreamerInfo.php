<?php
/**
 * Streamer
 * Streamer Info Template
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class TemplateStreamerInfo {
	/**
	 * Output HTML
	 *
	 * @var		string
	 */
	private $HTML;

	/**
	 * List of Streamer Information
	 *
	 * @access	public
	 * @param	array	Streamer Information
	 * @return	string	Built HTML
	 */
	public function streamerInfoPage($streamers) {
		$HTML .= "
		<table class='wikitable'>
			<thead>
				<tr>
					<th>Service</th>
					<th>Streamer Name</th>
					<th>Display Name</th>
					<th>Page Title</th>
					<th>Link Preview</th>
				</tr>
			</thead>
			<tbody>";
		foreach ($streamers as $streamer) {
			$HTML .= "
				<tr>
					<td>".wfMessage("service_".$streamer->getService())->escaped()."</td>
					<td>".$streamer->getRemoteName()."</td>
					<td>".$streamer->getDisplayName()."</td>
					<td>".($streamer->getPageTitle() ? $streamer->getPageTitle()->getPrefixedText() : '')."</td>
					<td>".$streamer->getLink(true)."</td>
				</tr>";
		}
		$HTML .= "
			</tbody>
		</table>";

		return $HTML;
	}
}
