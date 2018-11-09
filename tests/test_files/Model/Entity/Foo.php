<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property \Cake\I18n\FrozenTime $created
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\FrozenTime|null $modified
 */
class Foo extends Entity {
}
