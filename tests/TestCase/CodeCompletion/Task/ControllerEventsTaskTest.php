<?php

namespace IdeHelper\Test\TestCase\CodeCompletion\Task;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\Task\ControllerEventsTask;

class ControllerEventsTaskTest extends TestCase {

	protected ControllerEventsTask $task;

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
	public function testCollectLegacy(): void {
		Configure::write('IdeHelper.codeCompletionReturnType', false);

		$result = $this->task->create();

		$expected = <<<'TXT'

use Cake\Event\EventInterface;
use Cake\Http\Response;
use Psr\Http\Message\UriInterface;

if (false) {
	class Controller {
		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function startup(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function beforeFilter(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function beforeRender(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function afterFilter(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function shutdown(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 * @param \Psr\Http\Message\UriInterface|array|string $url
		 * @param \Cake\Http\Response $response
		 *
		 * @return void
		 */
		public function beforeRedirect(EventInterface $event, UriInterface|array|string $url, Response $response) {
		}
	}

	class Component {
		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function startup(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function beforeFilter(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function beforeRender(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function afterFilter(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 *
		 * @return void
		 */
		public function shutdown(EventInterface $event) {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 * @param \Psr\Http\Message\UriInterface|array|string $url
		 * @param \Cake\Http\Response $response
		 *
		 * @return void
		 */
		public function beforeRedirect(EventInterface $event, UriInterface|array|string $url, Response $response) {
		}
	}
}

TXT;
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		Configure::write('IdeHelper.codeCompletionReturnType', true);

		$result = $this->task->create();

		$expected = <<<'TXT'

use Cake\Event\EventInterface;
use Cake\Http\Response;
use Psr\Http\Message\UriInterface;

if (false) {
	class Controller {
		public function startup(EventInterface $event): void {
		}

		public function beforeFilter(EventInterface $event): void {
		}

		public function beforeRender(EventInterface $event): void {
		}

		public function afterFilter(EventInterface $event): void {
		}

		public function shutdown(EventInterface $event): void {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 * @param \Psr\Http\Message\UriInterface|array|string $url
		 * @param \Cake\Http\Response $response
		 *
		 * @return void
		 */
		public function beforeRedirect(EventInterface $event, UriInterface|array|string $url, Response $response): void {
		}
	}

	class Component {
		public function startup(EventInterface $event): void {
		}

		public function beforeFilter(EventInterface $event): void {
		}

		public function beforeRender(EventInterface $event): void {
		}

		public function afterFilter(EventInterface $event): void {
		}

		public function shutdown(EventInterface $event): void {
		}

		/**
		 * @param \Cake\Event\EventInterface $event
		 * @param \Psr\Http\Message\UriInterface|array|string $url
		 * @param \Cake\Http\Response $response
		 *
		 * @return void
		 */
		public function beforeRedirect(EventInterface $event, UriInterface|array|string $url, Response $response): void {
		}
	}
}

TXT;

		$this->assertTextEquals($expected, $result);
	}

}
