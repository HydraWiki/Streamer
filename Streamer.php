<?php
/**
 * Streamer
 * Streamer Mediawiki Setup
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

if (!defined('MEDIAWIKI')) {
	exit;
}

/******************************************/
/* Credits                                */
/******************************************/
define('STREAMER_VERSION', '0.1.0');

$wgExtensionCredits['parserhook'][] = [
	'path'				=> __FILE__,
	'name'				=> 'Streamer',
	'author'			=> ['Wiki Platform Team', 'Curse Inc.', 'Alexia E. Smith'],
	'url'				=> 'http://www.mediawiki.org/wiki/Extension:Streamer',
	'version'			=> STREAMER_VERSION,
	'descriptionmsg'	=> 'streamer_description'
];

/******************************************/
/* Language Strings, Page Aliases, Hooks  */
/******************************************/
$extDir = __DIR__;

if (!defined('STREAMER_EXT_DIR')) {
	define('STREAMER_EXT_DIR', $extDir);
}

$wgAvailableRights[] = 'edit_streamer_info';

$wgExtensionMessagesFiles['Streamer']			= "{$extDir}/Streamer.i18n.php";
$wgExtensionMessagesFiles['StreamerMagic']		= "{$extDir}/Streamer.i18n.magic.php";
$wgMessagesDirs['Streamer']						= "{$extDir}/i18n";

$wgAutoloadClasses['StreamerHooks']				= "{$extDir}/Streamer.hooks.php";
$wgAutoloadClasses['ApiStreamerBase']			= "{$extDir}/classes/ApiStreamerBase.php";
$wgAutoloadClasses['ApiTwitch']					= "{$extDir}/classes/ApiTwitch.php";
$wgAutoloadClasses['StreamerTemplate']			= "{$extDir}/classes/StreamerTemplate.php";

$wgHooks['ParserFirstCallInit'][]				= 'StreamerHooks::onParserFirstCallInit';
$wgHooks['LoadExtensionSchemaUpdates'][]		= 'StreamerHooks::onLoadExtensionSchemaUpdates';

$wgResourceModules['ext.streamer'] = [
	'localBasePath'	=> __DIR__,
	'remoteExtPath'	=> 'Streamer',
	'styles'		=> ['css/streamer.css'],
	'position'		=> 'top'
];
