<?php

namespace IdeHelper\Utility;

use Cake\I18n\Parser\PoFileParser;

class TranslationParser {

	/**
	 * @var \Cake\I18n\Parser\PoFileParser
	 */
	protected $poFileParser;

	public function __construct() {
		$this->poFileParser = new PoFileParser();
	}

	/**
	 * @param string $path File path
	 *
	 * @return string[]
	 */
	public function parse($path) {
		$result = $this->poFileParser->parse($path);
		$resultKeys = array_keys($result);

		$domainKeys = [];
		foreach ($resultKeys as $resultKey) {
			$resultKey = $this->escapeSlashes($resultKey);

			$domainKeys[$resultKey] = $resultKey;
		}

		return $domainKeys;
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected function escapeSlashes($key) {
		return addcslashes($key, '\'');
	}

}
