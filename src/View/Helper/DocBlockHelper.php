<?php

namespace IdeHelper\View\Helper;

use Bake\View\Helper\DocBlockHelper as BakeDocBlockHelper;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\ORM\Association;
use Cake\Utility\Inflector;
use IdeHelper\Utility\GenericString;

class DocBlockHelper extends BakeDocBlockHelper {

	/**
	 * @var array<string, bool>|null
	 */
	protected static $nullableMap;

	/**
	 * @var array<string>
	 */
	protected array $virtualFields = [];

	/**
	 * Overwrite Bake plugin class method until https://github.com/cakephp/bake/pull/470 lands.
	 *
	 * @param array<array<string, mixed>> $propertySchema The property schema to use for generating the type map.
	 * @return array<string, string> The property DocType map.
	 */
	public function buildEntityPropertyHintTypeMap(array $propertySchema): array {
		$properties = [];
		foreach ($propertySchema as $property => $info) {
			if ($info['kind'] === 'column') {
				$type = $this->columnTypeToHintType($info['type']);

				$properties[$property] = $this->columnTypeNullable($info, $type);
			}
		}

		return $properties;
	}

	/**
	 * @param array<string, string> $info
	 * @param string|null $type
	 * @param string|null $default
	 *
	 * @return string
	 */
	public function columnTypeNullable(array $info, ?string $type, ?string $default = null): string {
		if (!$type) {
			$type = $default ?: 'mixed';
		}

		if ($type === 'mixed' || empty($info['null'])) {
			return $type;
		}

		if (static::$nullableMap === null) {
			static::$nullableMap = (array)Configure::read('IdeHelper.nullableMap');
		}

		if (isset(static::$nullableMap[$type]) && static::$nullableMap[$type] === false) {
			return $type;
		}

		$type .= '|null';

		return $type;
	}

	/**
	 * {@inheritDoc}
	 *
	 * Overwrite with nullable option for now until Bake is adjusted ( https://github.com/cakephp/bake/issues/579 )
	 *
	 * @param array<string, array<string, mixed>> $propertySchema The property schema to use for generating the type map.
	 * @return array<string, string> The property DocType map.
	 */
	public function buildEntityAssociationHintTypeMap(array $propertySchema): array {
		$properties = [];
		foreach ($propertySchema as $property => $info) {
			if ($info['kind'] === 'association') {
				$type = $this->associatedEntityTypeToHintType($info['type'], $info['association']);
				if ($info['association']->type() === Association::MANY_TO_ONE) {
					$key = $info['association']->getForeignKey();
					if (is_array($key)) {
						$key = implode('-', $key);
					}
					$properties = $this->_insertAfter(
						$properties,
						$key,
						[$property => $this->columnTypeNullable($info, $type)],
					);
				} else {
					$properties[$property] = $this->columnTypeNullable($info, $type);
				}
			}
		}

		return $properties;
	}

	/**
	 * {@inheritDoc}
	 *
	 * Overwrite with array vs generics syntax switch.
	 *
	 * @param string $type The entity class type (a fully qualified class name).
	 * @param \Cake\ORM\Association $association The association related to the entity class.
	 * @return string The DocBlock type
	 */
	public function associatedEntityTypeToHintType(string $type, Association $association): string {
		$annotationType = $association->type();
		if (
			$annotationType === Association::MANY_TO_MANY ||
			$annotationType === Association::ONE_TO_MANY
		) {
			return GenericString::generate($type);
		}

		return $type;
	}

	/**
	 * {@inheritDoc}
	 *
	 * Overwrite with array vs generics syntax switch.
	 *
	 * @param array<string, array<array<string, mixed>>> $associations Associations list.
	 * @param array<string, array<string, mixed>> $associationInfo Association info.
	 * @param array<string, array<mixed>> $behaviors Behaviors list.
	 * @param string $entity Entity name.
	 * @param string $namespace Namespace.
	 * @return array<string>
	 */
	public function buildTableAnnotations(
		array $associations,
		array $associationInfo,
		array $behaviors,
		string $entity,
		string $namespace,
	): array {
		$annotations = [];
		foreach ($associations as $type => $assocs) {
			foreach ($assocs as $assoc) {
				$typeStr = Inflector::camelize($type);
				if (isset($associationInfo[$assoc['alias']])) {
					$tableFqn = $associationInfo[$assoc['alias']]['targetFqn'];
					$annotations[] = "@property {$tableFqn}&\Cake\ORM\Association\\{$typeStr} \${$assoc['alias']}";
				}
			}
		}

		$class = "\\{$namespace}\\Model\\Entity\\{$entity}";
		$classes = GenericString::generate($class);
		$classInterface = '\\Cake\\Datasource\\EntityInterface';
		if (Configure::read('IdeHelper.concreteEntitiesInParam')) {
			$classInterface = $class;
		}

		$dataType = 'array';
		$optionsType = 'array';
		$itterable = 'iterable';
		if (Configure::read('IdeHelper.genericsInParam')) {
			$dataType = 'array<mixed>';
			$optionsType = 'array<string, mixed>';
			$itterable = "iterable<{$classInterface}>";
		}

		$annotations[] = "@method {$class} newEmptyEntity()";
		$annotations[] = "@method {$class} newEntity({$dataType} \$data, {$optionsType} \$options = [])";
		$annotations[] = "@method {$classes} newEntities({$dataType} \$data, {$optionsType} \$options = [])";
		$annotations[] = "@method {$class} get(mixed \$primaryKey, array|string \$finder = 'all', \Psr\SimpleCache\CacheInterface|string|null \$cache = null, \Closure|string|null \$cacheKey = null, mixed ...\$args)";
		$annotations[] = "@method {$class} findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array \$search, ?callable \$callback = null, {$optionsType} \$options = [])";
		$annotations[] = "@method {$class} patchEntity({$classInterface} \$entity, {$dataType} \$data, {$optionsType} \$options = [])";
		$annotations[] = "@method {$classes} patchEntities({$itterable} \$entities, {$dataType} \$data, {$optionsType} \$options = [])";
		$annotations[] = "@method {$class}|false save({$classInterface} \$entity, {$optionsType} \$options = [])";
		$annotations[] = "@method {$class} saveOrFail({$classInterface} \$entity, {$optionsType} \$options = [])";
		$annotations[] = "@method {$classes}|\Cake\Datasource\ResultSetInterface<{$class}>|false saveMany({$itterable} \$entities, {$optionsType} \$options = [])";
		$annotations[] = "@method {$classes}|\Cake\Datasource\ResultSetInterface<{$class}> saveManyOrFail({$itterable} \$entities, {$optionsType} \$options = [])";
		$annotations[] = "@method {$classes}|\Cake\Datasource\ResultSetInterface<{$class}>|false deleteMany({$itterable} \$entities, {$optionsType} \$options = [])";
		$annotations[] = "@method {$classes}|\Cake\Datasource\ResultSetInterface<{$class}> deleteManyOrFail({$itterable} \$entities, {$optionsType} \$options = [])";

		foreach ($behaviors as $behavior => $behaviorData) {
			$className = App::className($behavior, 'Model/Behavior', 'Behavior');
			if (!$className) {
				$className = "Cake\ORM\Behavior\\{$behavior}Behavior";
			}

			$annotations[] = '@mixin \\' . $className;
		}

		return $annotations;
	}

	/**
	 * @param array<string> $virtualFields
	 *
	 * @return void
	 */
	public function setVirtualFields(array $virtualFields): void {
		$this->virtualFields = $virtualFields;
	}

	/**
	 * @return array<string>
	 */
	public function getVirtualFields(): array {
		return $this->virtualFields;
	}

}
