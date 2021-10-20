<?php

namespace IdeHelper\Generator\Task;

use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\ORM\Table;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\ValueObject\ClassName;

class TableAssociationTask extends ModelTask {

	public const CLASS_TABLE = Table::class;

	/**
	 * @var array<string, string>
	 */
	protected $aliases = [
		'\\' . self::CLASS_TABLE . '::belongsTo(0)' => BelongsTo::class,
		'\\' . self::CLASS_TABLE . '::hasOne(0)' => HasOne::class,
		'\\' . self::CLASS_TABLE . '::hasMany(0)' => HasMany::class,
		'\\' . self::CLASS_TABLE . '::belongToMany(0)' => BelongsToMany::class,
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$models = $this->collectModels();

		$result = [];
		foreach ($this->aliases as $alias => $className) {
			$map = [];
			foreach ($models as $model => $modelClassName) {
				$map[$model] = ClassName::create($className);
			}

			ksort($map);

			$directive = new Override($alias, $map);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

}
