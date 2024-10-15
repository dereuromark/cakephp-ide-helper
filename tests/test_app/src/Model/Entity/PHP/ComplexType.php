<?php
namespace TestApp\Model\Entity\PHP;

use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property array{foo: array<mixed>|null}|null $params !
 * @property \Cake\I18n\Date $offer_date
 */
class ComplexType extends Entity {
}
