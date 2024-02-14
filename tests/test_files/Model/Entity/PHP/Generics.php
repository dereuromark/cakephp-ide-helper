<?php
namespace TestApp\Model\Entity\PHP;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property array<\TestApp\Model\Entity\Wheel> $wheels
 * @property \Cake\I18n\Date $offer_date
 */
class Generics extends Entity {
}
