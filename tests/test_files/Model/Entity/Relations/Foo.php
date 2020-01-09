<?php
namespace Relations\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property int $user_id
 * @property \Relations\Model\Entity\User $user
 */
class Foo extends Entity {
}
