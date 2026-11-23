<?php
namespace TestApp\Behavior;

use Cake\ORM\Behavior;

/**
 * Behavior outside a `Model\Behavior` namespace, loaded by its FQCN.
 */
class StampBehavior extends Behavior {

	/**
	 * @return string
	 */
	public function stamp(): string {
		return 'stamped';
	}

}
