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
	 * Valid parameters.
	 *
	 * @var		array
	 */
	static private $parameters = [
		'service' => [
			'required'	=> true,
			'default'	=> null,
			'values'	=> ['twitch']
		],
		'user' => [
			'required'	=> true,
			'default'	=> null
		],
		'template' => [
			'required'	=> false,
			'default'	=> 'block',
			'built_in'	=> [
				'block',
				'live',
				'minilive',
				'thumbnail',
				'viewers',
				'link'
			]
		],
		'link' => [
			'required'	=> false,
			'default'	=> null
		],
	];

	/**
	 * Any error messages that may have been triggerred.
	 *
	 * @var		array
	 */
	static private $errors = false;

    /**
     * Sets up this extension's parser functions.
     *
     * @access	public
     * @param	object	Parser object passed as a reference.
     * @return	boolean	true
     */
    static public function onParserFirstCallInit(Parser &$parser) {
		$parser->setFunctionHook("streamer", "StreamerHooks::parseStreamerTag", SFH_OBJECT_ARGS);

		return true;
	}

	/**
	 * Displays streamer information for the given parameters.
	 *
	 * @access	public
	 * @param	object	Parser
	 * @param	object	PPFrame
	 * @param	array	Arguments
	 * @return	array	Generated Output
	 */
	static public function parseStreamerTag(Parser &$parser, PPFrame $frame, $arguments) {
		self::$errors = false;

		/************************************/
		/* Clean Parameters                 */
		/************************************/
		$rawParameterOptions = [];
		foreach ($arguments as $argument) {
			$rawParameterOptions[] = trim($frame->expand($argument));
		}
		$parameters = self::cleanAndSetupParameters($rawParameterOptions);

		/************************************/
		/* Error Checking                   */
		/************************************/
		if (self::$errors === false) {
			$streamer = ApiStreamerBase::newFromService($parameters['service']);
			$userGood = $streamer->setUser($parameters['user']);

			if (!$userGood) {
				self::setError('streamer_error_invalid_user', [$parameters['service'], $parameters['user']]);
			} else {
				/************************************/
				/* HMTL Generation                  */
				/************************************/
				$streamerInfo = StreamerInfo::newFromServiceAndName($parameters['service'], $parameters['user']);
				$displayName = $streamerInfo->getDisplayName();

				if (isset($parameters['link'])) {
					$link = $parameters['link'];
				} else {
					$link = $streamerInfo->getLink();
				}
				if (!$link) {
					//Fallback in case of no actual links.
					$link = $streamer->getChannelUrl();
				}

				$variables = [
					'%ONLINE%'			=> $streamer->getOnline(),
					'%NAME%'			=> (!empty($displayName) ? $displayName : $streamer->getName()),
					'%VIEWERS%'			=> $streamer->getViewers(),
					'%DOING%'			=> $streamer->getDoing(),
					'%STATUS%'			=> $streamer->getStatus(),
					'%LIFETIME_VIEWS%'	=> $streamer->getLifetimeViews(),
					'%LOGO%'			=> $streamer->getLogo(),
					'%THUMBNAIL%'		=> $streamer->getThumbnail(),
					'%CHANNEL_URL%'		=> $streamer->getChannelUrl(),
					'%LINK%'			=> $link
				];

				$html = self::getTemplateWithReplacements($parameters['template'], $variables);

				$parser->getOutput()->addModuleStyles(['ext.streamer']);
			}
		}

		if (self::$errors !== false) {
			$html = "
			<div class='errorbox'>
				<strong>Streamer ".STREAMER_VERSION."</strong><br/>
				".implode("<br/>\n", self::$errors)."
			</div>";
		}

		return [
			$html,
			'noparse' => false,
			'isHTML' => true
		];
	}

	/**
	 * Clean user supplied parameters and setup defaults.
	 *
	 * @access	private
	 * @param	array	Raw strings of 'parameter=option'.
	 * @return	array	Safe Parameter => Option key value pairs.
	 */
	static private function cleanAndSetupParameters($rawParameterOptions) {
		//Check user supplied parameters.
		foreach ($rawParameterOptions as $raw) {
			$equals = strpos($raw, '=');
			if ($equals === false || $equals === 0 || $equals === strlen($raw) - 1) {
				continue;
			}

			list($parameter, $option) = explode('=', $raw);
			$parameter = trim($parameter);
			$option = trim($option);

			if (isset(self::$parameters[$parameter])) {
				if (is_array(self::$parameters[$parameter]['values'])) {
					if (!in_array($option, self::$parameters[$parameter]['values'])) {
						//Throw an error.
						self::setError('streamer_error_invalid_option', [$parameter, $option]);
					} else {
						$cleanParameterOptions[$parameter] = $option;
					}
				} else {
					$cleanParameterOptions[$parameter] = $option;
				}
			} else {
				self::setError('streamer_error_bad_parameter', [$parameter]);
			}
		}

		foreach (self::$parameters as $parameter => $parameterData) {
			if ($parameterData['required'] && !array_key_exists($parameter, $cleanParameterOptions)) {
				self::setError('streamer_error_parameter_required', [$parameter]);
			}
			//Assign the default if not supplied by the user and a default exists.
			if (!$parameterData['required'] && !array_key_exists($parameter, $cleanParameterOptions) && $parameterData['default'] !== null) {
				$cleanParameterOptions[$parameter] = $parameterData['default'];
			}
		}

		return $cleanParameterOptions;
	}

	/**
	 * Return a parsed template with variables replaced.
	 *
	 * @access	private
	 * @param	string	Template Name - Either a built in template or a namespaced template.
	 * @param	array	Replacement Variables
	 * @return	string	HTML
	 */
	static private function getTemplateWithReplacements($template, $variables) {
		$rawTemplate = StreamerTemplate::get($template);

		if ($rawTemplate !== false) {
			foreach ($variables as $variable => $replacement) {
				$rawTemplate = str_replace($variable, $replacement, $rawTemplate);
			}
		}

		return $rawTemplate;
	}

	/**
	 * Set a non-fatal error to be returned to the end user later.
	 *
	 * @access	private
	 * @param	string	Message language string.
	 * @param	array	Message replacements.
	 * @return	void
	 */
	static private function setError($message, $replacements) {
		self::$errors[] = wfMessage($message, $replacements)->escaped();
	}

	/**
	 * Setups and Modifies Database Information
	 *
	 * @access	public
	 * @param	object	DatabaseUpdater Object
	 * @return	boolean	true
	 */
	static public function onLoadExtensionSchemaUpdates($updater = null) {
		$updater->addExtensionUpdate(['addTable', 'streamer', STREAMER_EXT_DIR."/install/sql/streamer_table_streamer.sql", true]);

		return true;
	}
}
