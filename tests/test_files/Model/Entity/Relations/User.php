<?php
namespace Relations\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property \Relations\Model\Entity\Foo|null $foo
 * @property \Relations\Model\Entity\Bar|null $bar
 */
class User extends Entity {
}
