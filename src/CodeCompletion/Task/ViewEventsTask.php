<?php

namespace IdeHelper\CodeCompletion\Task;

class ViewEventsTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const TYPE_NAMESPACE = 'Cake\View';

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
		public function beforeRenderFile(EventInterface $event): void {}
		public function afterRenderFile(EventInterface $event): void {}
		public function beforeRender(EventInterface $event): void {}
		public function afterRender(EventInterface $event): void {}
		public function beforeLayout(EventInterface $event): void {}
		public function afterLayout(EventInterface $event): void {}
TXT;

		return <<<CODE

use Cake\Event\EventInterface;

if (false) {
	abstract class Helper {
$events
	}
}

CODE;
	}

}
