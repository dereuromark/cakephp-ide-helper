<?php

namespace IdeHelper\Generator\Task;

use Cake\Utility\Inflector;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Utility\App;
use IdeHelper\Utility\Plugin;
use IdeHelper\Utility\TranslationParser;
use IdeHelper\ValueObject\StringName;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * @link https://book.cakephp.org/4/en/core-libraries/global-constants-and-functions.html#global-functions
 */
class TranslationKeyTask implements TaskInterface {

	/**
	 * @var \IdeHelper\Utility\TranslationParser
	 */
	protected $translationParser;

	public function __construct() {
		$this->translationParser = new TranslationParser();
	}

	/**
	 * function __($singular, ...$args)
	 */
	const METHOD_DEFAULT = '__()';

	/**
	 * function __d($domain, $msg, ...$args)
	 */
	const METHOD_DOMAIN = '__d()';

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect() {
		$result = [];

		$translationKeys = $this->translationKeys();

		$domains = [];
		$domainKeys = [];
		foreach ($translationKeys as $domain => $keys) {
			if ($domain === 'default') {
				$method = '\\' . static::METHOD_DEFAULT;
				$directive = new ExpectedArguments($method, 0, $keys);
				$result[$directive->key()] = $directive;

				continue;
			}

			$domains[$domain] = StringName::create($domain);
			$domainKeys += $keys;
		}

		if ($domainKeys) {
			ksort($domainKeys);

			$method = '\\' . static::METHOD_DOMAIN;
			$directive = new ExpectedArguments($method, 1, $domainKeys);
			$result[$directive->key()] = $directive;
		}

		$domains = $this->completeDomains($domains);

		if ($domains) {
			$method = '\\' . static::METHOD_DOMAIN;
			$directive = new ExpectedArguments($method, 0, $domains);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return \IdeHelper\ValueObject\StringName[][]
	 */
	protected function translationKeys() {
		$translationsKeys = $this->parseTranslations();

		foreach ($translationsKeys as $domain => $array) {
			$result = [];
			foreach ($array as $key) {
				$result[$key] = StringName::create($key);
			}

			ksort($result);

			$translationsKeys[$domain] = $result;
		}

		ksort($translationsKeys);

		return $translationsKeys;
	}

	/**
	 * @return string[][]
	 */
	protected function parseTranslations() {
		$keys = [];

		$localePaths = App::path('Locale');
		foreach ($localePaths as $localePath) {
			if (!is_dir($localePath)) {
				continue;
			}

			$directoryIterator = new RecursiveDirectoryIterator($localePath);
			$iterator = new RecursiveIteratorIterator($directoryIterator);
			$regexIterator = new RegexIterator($iterator, '/^.+\.po/i', RecursiveRegexIterator::GET_MATCH);

			foreach ($regexIterator as $files) {
				foreach ($files as $file) {
					if (!file_exists($file)) {
						continue;
					}

					$domainKeys = $this->translationParser->parse($file);

					$domain = pathinfo($file, PATHINFO_FILENAME);
					if (!isset($keys[$domain])) {
						$keys[$domain] = [];
					}
					$keys[$domain] += $domainKeys;
				}
			}

			$plugins = Plugin::all();
			foreach ($plugins as $plugin) {
				$localePath = Plugin::path($plugin) . 'resources' . DIRECTORY_SEPARATOR . 'locales' . DIRECTORY_SEPARATOR;

				if (!is_dir($localePath)) {
					continue;
				}

				$directoryIterator = new RecursiveDirectoryIterator($localePath);
				$iterator = new RecursiveIteratorIterator($directoryIterator);
				$regexIterator = new RegexIterator($iterator, '/^.+\.po/i', RecursiveRegexIterator::GET_MATCH);

				foreach ($regexIterator as $files) {
					foreach ($files as $file) {
						if (!file_exists($file)) {
							continue;
						}

						$domainKeys = $this->translationParser->parse($file);

						$domain = pathinfo($file, PATHINFO_FILENAME);
						if (!isset($keys[$domain])) {
							$keys[$domain] = [];
						}
						$keys[$domain] += $domainKeys;
					}
				}
			}
		}

		return $keys;
	}

	/**
	 * @param \IdeHelper\ValueObject\StringName[] $domains
	 *
	 * @return \IdeHelper\ValueObject\StringName[]
	 */
	protected function completeDomains(array $domains) {
		$plugins = Plugin::all();
		foreach ($plugins as $plugin) {
			$pieces = explode('/', $plugin);
			foreach ($pieces as $key => $piece) {
				$pieces[$key] = Inflector::underscore($piece);
			}

			$domain = implode('/', $pieces);

			// Issue of https://github.com/cakephp/docs/pull/6585 and for 5.0 to be resolved
			if (count($pieces) > 1) {
				$lastPiece = array_pop($pieces);
				unset($domains[$lastPiece]);
			}

			$domains[$domain] = StringName::create($domain);
		}

		if (!isset($domains['cake'])) {
			$domains['cake'] = StringName::create('cake');
		}

		ksort($domains);

		return $domains;
	}

}
