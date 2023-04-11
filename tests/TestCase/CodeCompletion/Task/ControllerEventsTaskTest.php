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

		$expected = <<<'TXT'

use Cake\Event\EventInterface;
use Cake\Http\Response;

if (false) {
	abstract class Controller {
		public function startup(EventInterface $event): ?Response {
			return null;
		}
		public function beforeFilter(EventInterface $event): ?Response {
			return null;
		}
		public function beforeRender(EventInterface $event): ?Response {
			return null;
		}
		public function afterFilter(EventInterface $event): ?Response {
			return null;
		}
		public function shutdown(EventInterface $event): ?Response {
			return null;
		}
		public function beforeRedirect(EventInterface $event, $url, Response $response) {
			return null;
		}
	}

	abstract class Component {
		public function startup(EventInterface $event): ?Response {
			return null;
		}
		public function beforeFilter(EventInterface $event): ?Response {
			return null;
		}
		public function beforeRender(EventInterface $event): ?Response {
			return null;
		}
		public function afterFilter(EventInterface $event): ?Response {
			return null;
		}
		public function shutdown(EventInterface $event): ?Response {
			return null;
		}
		public function beforeRedirect(EventInterface $event, $url, Response $response) {
			return null;
		}
	}
}

TXT;

		$this->assertTextEquals($expected, $result);
	}

}
