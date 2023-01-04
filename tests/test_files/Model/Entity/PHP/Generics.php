<?php
namespace TestApp\Model\Entity\PHP7;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property array<\TestApp\Model\Entity\Wheel> $wheels
 */
class Generics extends Entity {
}
