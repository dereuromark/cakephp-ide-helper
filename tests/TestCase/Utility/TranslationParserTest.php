<?php

namespace IdeHelper\Test\TestCase\Utility;

use Cake\TestSuite\TestCase;
use IdeHelper\Utility\TranslationParser;

class TranslationParserTest extends TestCase {

	/**
	 * @var \IdeHelper\Utility\TranslationParser
	 */
	protected $translationParser;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->translationParser = new TranslationParser();
	}

	/**
	 * @return void
	 */
	public function testParse() {
		$path = TEST_FILES . 'locales' . DS . 'default.po';

		$result = $this->translationParser->parse($path);

		$expected = [
			'A "quoted" string' => 'A "quoted" string',
			'A ""escape-quoted"" string' => 'A ""escape-quoted"" string',
			'A \\\'literally quoted\\\' string' => 'A \\\'literally quoted\\\' string',
			'A variable \\\'\\\'{0}\\\'\\\' be replaced.' => 'A variable \\\'\\\'{0}\\\'\\\' be replaced.',
		];
		$this->assertSame($expected, $result);
	}

}
