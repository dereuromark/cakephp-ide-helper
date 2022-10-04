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
		$events = <<<TXT
		public function startup(EventInterface \$event): ?Response {}
		public function beforeFilter(EventInterface \$event): ?Response {}
		public function beforeRender(EventInterface \$event): ?Response {}
		public function afterFilter(EventInterface \$event): ?Response {}
		public function shutdown(EventInterface \$event): ?Response {}
		public function beforeRedirect(EventInterface \$event, \$url, Response \$response) {}
TXT;

		return <<<TXT

use Cake\Event\EventInterface;
use Cake\Http\Response;

if (false) {
	abstract class Controller
	{
$events
	}

	abstract class Component
	{
$events
	}
}

TXT;
	}

}
