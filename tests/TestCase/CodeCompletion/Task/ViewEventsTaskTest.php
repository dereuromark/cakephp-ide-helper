<?php

namespace IdeHelper\Test\TestCase\CodeCompletion\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\Task\ViewEventsTask;

class ViewEventsTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\CodeCompletion\Task\ViewEventsTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ViewEventsTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->create();

		$expected = <<<'TXT'

use Cake\Event\EventInterface;

if (false) {
	abstract class Helper {
		public function beforeRenderFile(EventInterface $event): void {}
		public function afterRenderFile(EventInterface $event): void {}
		public function beforeRender(EventInterface $event): void {}
		public function afterRender(EventInterface $event): void {}
		public function beforeLayout(EventInterface $event): void {}
		public function afterLayout(EventInterface $event): void {}
	}
}

TXT;

		$this->assertTextEquals($expected, $result);
	}

}
