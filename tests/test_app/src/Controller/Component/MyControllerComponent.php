<?php
namespace TestApp\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\EventInterface;

class MyControllerComponent extends Component {

	/**
	 * @param \Cake\Event\EventInterface $event
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event): void {
		$user = (bool)$this->getController()->AuthUser->user();
	}

}
