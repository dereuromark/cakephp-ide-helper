<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * My car entity which is very cool.
 *
 * @property int $id
 * @property string $name
 * @property string $content
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time|null $modified
 * @property \App\Model\Entity\Wheel[] $wheels
 */
class Car extends Entity {
}
