<?php

namespace IdeHelper\Generator\Task;

use Cake\I18n\Parser\PoFileParser;
use Cake\Utility\Inflector;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Utility\App;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\StringName;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * @link https://book.cakephp.org/4/en/core-libraries/global-constants-and-functions.html#global-functions
 */
class TranslationKeyTask implements TaskInterface {

	public const SET_TRANSLATION_KEYS = 'translationKeys';

	/**
	 * function __(string $singular, ...$args): string
	 */
	public const METHOD_DEFAULT = '__()';

	/**
	 * function __d(string $domain, string $msg, ...$args): string
	 */
	public const METHOD_DOMAIN = '__d()';

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
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
	protected function translationKeys(): array {
		$translationsKeys = $this->parseTranslations();

		foreach ($translationsKeys as $domain => $array) {
			$array = array_unique($array);
			$array = array_unique($array);

			$result = [];
			foreach ($array as $key) {
				$key = $this->escapeSlashes($key);
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
	protected function parseTranslations(): array {
		$keys = [];

		$localePaths = App::path('locales');
		foreach ($localePaths as $localePath) {
			if (!is_dir($localePath)) {
				continue;
			}

			$Directory = new RecursiveDirectoryIterator($localePath);
			$Iterator = new RecursiveIteratorIterator($Directory);
			$Regex = new RegexIterator($Iterator, '/^.+\.po/i', RecursiveRegexIterator::GET_MATCH);

			foreach ($Regex as $files) {
				foreach ($files as $file) {
					$domain = pathinfo($file, PATHINFO_FILENAME);

					$result = (new PoFileParser())->parse($file);
					$domainKeys = array_keys($result);

					if (!isset($keys[$domain])) {
						$keys[$domain] = [];
					}
					$keys[$domain] = array_merge($keys[$domain], $domainKeys);
				}
			}
		}

		return $keys;
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected function escapeSlashes(string $key): string {
		if (version_compare(PHP_VERSION, '7.3') >= 0) {
			return filter_var($key, FILTER_SANITIZE_ADD_SLASHES);
		}

		return addcslashes($key, '\'');
	}

	/**
	 * @param \IdeHelper\ValueObject\StringName[] $domains
	 *
	 * @return \IdeHelper\ValueObject\StringName[]
	 */
	protected function completeDomains(array $domains): array {
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

		ksort($domains);

		return $domains;
	}

}
