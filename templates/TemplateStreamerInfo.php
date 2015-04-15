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
		$HTML .= Linker::link(Title::newFromText("Special:StreamerInfo/edit"), wfMessage('sip_add')->escaped())."
		<table class='wikitable'>
			<thead>
				<tr>
					<th>Service</th>
					<th>Streamer Name</th>
					<th>Display Name</th>
					<th>Page Title</th>
					<th>Link Preview</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>";
		if (is_array($streamers)) {
			foreach ($streamers as $streamer) {
				$HTML .= "
					<tr>
						<td>".wfMessage("service_".$streamer->getService())->escaped()."</td>
						<td>".$streamer->getRemoteName()."</td>
						<td>".$streamer->getDisplayName()."</td>
						<td>".($streamer->getPageTitle() ? $streamer->getPageTitle()->getPrefixedText() : '')."</td>
						<td><a href='".$streamer->getLink(true)."'>".($streamer->getDisplayName() ? $streamer->getDisplayName() : $streamer->getRemoteName())."</a></td>
						<td>(".Linker::link(Title::newFromText("Special:StreamerInfo/edit"), wfMessage('sip_edit')->escaped(), [], ["streamer_id" => $streamer->getId()])." | ".Linker::link(Title::newFromText("Special:StreamerInfo/delete"), wfMessage('sip_delete')->escaped(), [], ["streamer_id" => $streamer->getId()]).")</td>
					</tr>";
			}
		} else {
			$HTML .= "
					<tr>
						<td colspan='6'>".wfMessage('no_streamers_found')."</td>
					</tr>";
		}
		$HTML .= "
			</tbody>
		</table>";

		return $HTML;
	}

	/**
	 * Streamer Information Form
	 *
	 * @access	public
	 * @param	array	Streamer Information
	 * @param	array	Errors keyed on field names.
	 * @return	string	Built HTML
	 */
	public function streamerInfoForm($streamer, $errors) {
		$title = Title::newFromText("Special:StreamerInfo/edit");
		$HTML .= "
		<form id='streamer_info_form' method='post' action='{$title->getFullURL()}?do=save'>
			".($errors['service'] ? '<span class="error">'.$errors['service'].'</span><br/>' : '')."
			<label for='service' class='label_above'>".wfMessage('sif_service')->escaped()."</label><br/>
			<select id='service' name='service'>";
		if (is_array(StreamerInfo::getServicesList()) && count(StreamerInfo::getServicesList())) {
			foreach (StreamerInfo::getServicesList() as $serviceName => $serviceId) {
				$HTML .= "
				<option value='{$serviceId}'".($streamer->getService() == $serviceId ? ' selected="selected"' : null).">".wfMessage("service_".$serviceId)->escaped()."</option>\n";
			}
		}
		$HTML .= "
			</select><br/>
		<br/>
			".($errors['remote_name'] ? '<span class="error">'.$errors['remote_name'].'</span><br/>' : '')."
			<label for='remote_name' class='label_above'>".wfMessage('sif_remote_name')->escaped()."</label><br/>
			<input id='remote_name' name='remote_name' type='text' value='".htmlspecialchars($streamer->getRemoteName(), ENT_QUOTES)."'/><br/>
			<br/>
			".($errors['display_name'] ? '<span class="error">'.$errors['display_name'].'</span><br/>' : '')."
			<label for='display_name' class='label_above'>".wfMessage('sif_display_name')->escaped()."</label><br/>
			<input id='display_name' name='display_name' type='text' value='".htmlspecialchars($streamer->getDisplayName(), ENT_QUOTES)."'/><br/>
			<br/>
			".($errors['page_title'] ? '<span class="error">'.$errors['page_title'].'</span><br/>' : '')."
			<label for='page_title' class='label_above'>".wfMessage('sif_page_title')->escaped()."</label><br/>
			<input id='page_title' name='page_title' type='text' value='".htmlspecialchars(($streamer->getPageTitle() instanceOf Title ? $streamer->getPageTitle()->getPrefixedText() : ''), ENT_QUOTES)."'/><br/>
			<br/>
			<input id='streamer_id' name='streamer_id' type='hidden' value='{$streamer->getId()}'/>
			<input id='streamer_submit' name='streamer_submit' type='submit' value='".wfMessage('sif_save')->escaped()."'/>
		</form>";

		return $HTML;
	}

	/**
	 * Streamer Information Delete
	 *
	 * @access	public
	 * @param	array	Streamer Information
	 * @return	string	Built HTML
	 */
	public function streamerInfoDelete($streamer) {
		$title = Title::newFromText("Special:StreamerInfo/delete");
		$HTML .= "
		<form id='streamer_info_form' method='post' action='{$title->getFullURL()}?confirm=true'>
			".wfMessage('sid_streamer_delete_confirm')->escaped()."<br/>
			<input id='streamer_id' name='streamer_id' type='hidden' value='{$streamer->getId()}'/>
			<input id='streamer_submit' name='streamer_submit' type='submit' value='".wfMessage('sid_confirm')->escaped()."'/>
		</form>";

		return $HTML;
	}
}
