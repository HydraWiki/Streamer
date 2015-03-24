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
$wgExtensionCredits['parserhook'][] = [
	'path'				=> __FILE__,
	'name'				=> 'Streamer',
	'author'			=> ['Wiki Platform Team', 'Curse Inc.', 'Alexia E. Smith'],
	'url'				=> 'http://www.mediawiki.org/wiki/Extension:Streamer',
	'version'			=> '0.1.0',
	'descriptionmsg'	=> 'streamer_description'
];

/******************************************/
/* Language Strings, Page Aliases, Hooks  */
/******************************************/
$extDir = __DIR__;

$wgExtensionMessagesFiles['Streamer']			= "{$extDir}/Streamer.i18n.php";
$wgExtensionMessagesFiles['StreamerMagic']		= "{$extDir}/Streamer.i18n.magic.php";
$wgMessagesDirs['Streamer']						= "{$extDir}/i18n";

$wgAutoloadClasses['StreamerHooks']				= "{$extDir}/Streamer.hooks.php";

$wgHooks['ParserFirstCallInit'][]				= 'StreamerHooks::onParserFirstCallInit';

$wgResourceModules['ext.streamer'] = [
	'localBasePath'	=> __DIR__,
	'remoteExtPath'	=> 'Streamer',
	'styles'		=> ['css/streamer.css'],
	'position'		=> 'top'
];
