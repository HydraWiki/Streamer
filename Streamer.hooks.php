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
			'default'	=> 'all',
			'built_in'	=> [
				'status',
				'thumbnail',
				'viewers',
				'link',
				'all'
			]
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
				$variables = [
					'%ONLINE%'			=> $streamer->getOnline(),
					'%NAME%'			=> $streamer->getName(),
					'%VIEWERS%'			=> $streamer->getViewers(),
					'%DOING%'			=> $streamer->getDoing(),
					'%STATUS%'			=> $streamer->getStatus(),
					'%LIFETIMEVIEWS%'	=> $streamer->getLifetimeViews(),
					'%LOGO%'			=> $streamer->getLogo(),
					'%THUMBNAIL%'		=> $streamer->getThumbnail()
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
			'noparse' => true,
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
}
