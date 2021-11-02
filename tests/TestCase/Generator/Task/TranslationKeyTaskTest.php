<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\Core\Configure;
use IdeHelper\Generator\Task\TranslationKeyTask;
use Shim\TestSuite\TestCase;

class TranslationKeyTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\TranslationKeyTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new TranslationKeyTask();

		Configure::write('App.paths.locales', [
			TEST_ROOT . 'locales' . DS,
		]);
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(3, $result);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\__()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'A {0} placeholder' => "'A {0} placeholder'",
			'Some \\\' special case' => '\'Some \\\' special case\'',
			'my foo and bar' => "'my foo and bar'",
		];
		$this->assertSame($expected, $list);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\__d()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		$expected = [
			'A plugin translation' => '\'A plugin translation\'',
		];
		$this->assertSame($expected, $list);

		/** @var \IdeHelper\Generator\Directive\ExpectedArguments $directive */
		$directive = array_shift($result);
		$this->assertSame('\__d()', $directive->toArray()['method']);

		$list = $directive->toArray()['list'];
		$list = array_map(function ($className) {
			return (string)$className;
		}, $list);

		// 'my_plugin' is now superseeded by 'my_namespace/my_plugin'
		$expected = [
			'awesome' => '\'awesome\'',
			'cake' => '\'cake\'',
			'controllers' => '\'controllers\'',
			'ide_helper' => '\'ide_helper\'',
			'my_namespace/my_plugin' => '\'my_namespace/my_plugin\'',
			'relations' => '\'relations\'',
			'shim' => '\'shim\'',
		];
		$this->assertSame($expected, $list);
	}

}
