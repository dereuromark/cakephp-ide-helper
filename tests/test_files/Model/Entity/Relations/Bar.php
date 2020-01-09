<?php
namespace Relations\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property int|null $user_id
 * @property \Relations\Model\Entity\User|null $user
 */
class Bar extends Entity {
}
