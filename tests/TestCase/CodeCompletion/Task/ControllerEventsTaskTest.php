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
	class Controller {
		/**
         * @param \Cake\Event\EventInterface $event
         *
         * @return \Cake\Http\Response|null|void
         */
		public function startup(EventInterface $event) {
			return null;
		}
		/**
         * @param \Cake\Event\EventInterface $event
         *
         * @return \Cake\Http\Response|null|void
         */
		public function beforeFilter(EventInterface $event) {
			return null;
		}
		/**
         * @param \Cake\Event\EventInterface $event
         *
         * @return \Cake\Http\Response|null|void
         */
		public function beforeRender(EventInterface $event) {
			return null;
		}
		/**
         * @param \Cake\Event\EventInterface $event
         *
         * @return \Cake\Http\Response|null|void
         */
		public function afterFilter(EventInterface $event) {
			return null;
		}
		/**
         * @param \Cake\Event\EventInterface $event
         *
         * @return \Cake\Http\Response|null|void
         */
		public function shutdown(EventInterface $event) {
			return null;
		}
		/**
         * @param \Cake\Event\EventInterface $event
         *
         * @return \Cake\Http\Response|null|void
         */
		public function beforeRedirect(EventInterface $event, $url, Response $response) {
			return null;
		}
	}

	class Component {
		public function startup(EventInterface $event): \Cake\Http\Response|null {
			return null;
		}
		public function beforeFilter(EventInterface $event): \Cake\Http\Response|null {
			return null;
		}
		public function beforeRender(EventInterface $event): \Cake\Http\Response|null {
			return null;
		}
		public function afterFilter(EventInterface $event): \Cake\Http\Response|null {
			return null;
		}
		public function shutdown(EventInterface $event): \Cake\Http\Response|null {
			return null;
		}
		public function beforeRedirect(EventInterface $event, $url, Response $response): \Cake\Http\Response|null {
			return null;
		}
	}
}

TXT;

		$this->assertTextEquals($expected, $result);
	}

}
