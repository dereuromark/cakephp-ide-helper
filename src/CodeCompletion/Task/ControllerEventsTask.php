<?php

namespace IdeHelper\CodeCompletion\Task;

use Cake\Core\Configure;

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
		/** @var bool|null $returnType */
		$returnType = Configure::read('IdeHelper.codeCompletionReturnType');

		$controllerEvents = $this->events($returnType ?? false);
		$componentEvents = $this->events($returnType ?? true);

		return <<<CODE

use Cake\Event\EventInterface;
use Cake\Http\Response;

if (false) {
	class Controller {
$controllerEvents
	}

	class Component {
$componentEvents
	}
}

CODE;
	}

	/**
	 * @param bool $returnType
	 *
	 * @return string
	 */
	protected function events(bool $returnType): string {
		$type = null;
		$docBlock = null;
		if ($returnType) {
			$type = ': ' . '\Cake\Http\Response|null';
		} else {
			$docBlock = <<<TXT
        /**
         * @param \Cake\Event\EventInterface \$event
         *
         * @return \Cake\Http\Response|null|void
         */
TXT;
			$docBlock = trim($docBlock) . PHP_EOL . str_repeat("\t", 2);
		}

		$events = <<<TXT
		{$docBlock}public function startup(EventInterface \$event)$type {
			return null;
		}
		{$docBlock}public function beforeFilter(EventInterface \$event)$type {
			return null;
		}
		{$docBlock}public function beforeRender(EventInterface \$event)$type {
			return null;
		}
		{$docBlock}public function afterFilter(EventInterface \$event)$type {
			return null;
		}
		{$docBlock}public function shutdown(EventInterface \$event)$type {
			return null;
		}
		{$docBlock}public function beforeRedirect(EventInterface \$event, \$url, Response \$response)$type {
			return null;
		}
TXT;

		return $events;
	}

}
