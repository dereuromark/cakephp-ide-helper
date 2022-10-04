<?php

namespace IdeHelper\Test\TestCase\CodeCompletion\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\Task\ControllerEventsTask;

class ControllerEventsTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\CodeCompletion\Task\ControllerEventsTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new ControllerEventsTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->create();

		$expected = <<<TXT

use Cake\Event\EventInterface;
use Cake\Http\Response;

abstract class Controller
{
	public function startup(EventInterface \$event): ?Response {}
	public function beforeFilter(EventInterface \$event): ?Response {}
	public function beforeRender(EventInterface \$event): ?Response {}
	public function afterFilter(EventInterface \$event): ?Response {}
	public function shutdown(EventInterface \$event): ?Response {}
	public function beforeRedirect(EventInterface \$event, \$url, Response \$response) {}
}

abstract class Component
{
	public function startup(EventInterface \$event): ?Response {}
	public function beforeFilter(EventInterface \$event): ?Response {}
	public function beforeRender(EventInterface \$event): ?Response {}
	public function afterFilter(EventInterface \$event): ?Response {}
	public function shutdown(EventInterface \$event): ?Response {}
	public function beforeRedirect(EventInterface \$event, \$url, Response \$response) {}
}

TXT;

		$this->assertTextEquals($expected, $result);
	}

}
