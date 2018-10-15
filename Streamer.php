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

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'Streamer' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['Streamer'] = __DIR__ . '/i18n';
	wfWarn(
		'Deprecated PHP entry point used for Streamer extension. Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return;
 } else {
	die( 'This version of the Streamer extension requires MediaWiki 1.25+' );
}