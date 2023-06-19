<?php

namespace IdeHelper\CodeCompletion\Task;

class ControllerEventsTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const TYPE_NAMESPACE = 'Cake\Controller';

	/**
	 * @return string
	 */
	public function type(): string {
		return static::TYPE_NAMESPACE;
	}

	/**
	 * @return string
	 */
	public function create(): string {
		$events = <<<'TXT'
		public function startup(EventInterface $event) {
			return null;
		}
		public function beforeFilter(EventInterface $event) {
			return null;
		}
		public function beforeRender(EventInterface $event) {
			return null;
		}
		public function afterFilter(EventInterface $event) {
			return null;
		}
		public function shutdown(EventInterface $event) {
			return null;
		}
		public function beforeRedirect(EventInterface $event, $url, Response $response) {
			return null;
		}
TXT;

		return <<<CODE

use Cake\Event\EventInterface;
use Cake\Http\Response;

if (false) {
	abstract class Controller {
$events
	}

	abstract class Component {
$events
	}
}

CODE;
	}

}
