<?php
namespace TestApp\Model\Entity\PHP;

use Cake\ORM\Entity;

/**
 * @property array|null $params
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \TestApp\Model\Entity\Wheel[] $wheels
 */
class Generics extends Entity {
}
