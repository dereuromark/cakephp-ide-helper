<?php
namespace TestApp\Model\Entity\PHP7;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime|null $modified
 * @property \TestApp\Model\Entity\Wheel[] $wheels
 */
class Generics extends Entity {
}
