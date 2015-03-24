<?php
/**
 * Streamer
 * Streamer Hooks
 *
 * @license		LGPLv3
 * @package		Streamer
 * @link		https://www.mediawiki.org/wiki/Extension:Streamer
 *
 **/

class StreamerHooks {
    /**
     * Sets up this extension's parser functions.
     *
     * @access	public
     * @param	object	Parser object passed as a reference.
     * @return	boolean	true
     */
    static public function onParserFirstCallInit(Parser &$parser) {
		$parser->setFunctionHook("streamer", "StreamerHooks::parseStreamerTag");

		return true;
	}

	/**
	 * Displays streamer information for the given parameters.
	 *
	 * @access	public
	 * @param	object	Parser
	 * @param	string	Which online service the streamer is using.
	 * @param	string	User Identifier for the streamer.
	 * @return	array	Generated Output
	 */
	static public function parseStreamerTag($parser, $service = null, $streamer = null) {
		/************************************/
		/* Clean Parameters                 */
		/************************************/


		/************************************/
		/* Error Checking                   */
		/************************************/


		/************************************/
		/* HMTL Generation                  */
		/************************************/


		$parser->getOutput()->addModuleStyles(['ext.streamer']);

		return [
			$html,
			'noparse' => true,
			'isHTML' => true
		];
	}
}