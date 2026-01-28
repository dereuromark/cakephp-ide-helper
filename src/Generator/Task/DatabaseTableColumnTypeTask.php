<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;
use Migrations\Db\Adapter\AdapterFactory;
use Migrations\Db\Adapter\AdapterInterface;

/**
 * This task is useful when using Migrations plugin and creating Migration files.
 */
class DatabaseTableColumnTypeTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const SET_COLUMN_TYPES = 'columnTypes';

	/**
	 * @var array<string>
	 */
	protected array $aliases = [
		'\Migrations\Db\Table::addColumn()',
		'\Migrations\Db\Table::changeColumn()',
	];

	/**
	 * Used if the Migrations plugin is not loaded
	 *
	 * @var array<string>
	 */
	protected array $defaultTypes = [
		'string',
		'char',
		'text',
		'integer',
		'smallinteger',
		'biginteger',
		'bit',
		'float',
		'decimal',
		'double',
		'datetime',
		'timestamp',
		'time',
		'date',
		'blob',
		'binary',
		'boolean',
		'uuid',
		'year',
		'json',
		'binaryuuid',
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$list = [];

		$types = $this->collectTableColumnTypes();
		foreach ($types as $type) {
			$list[$type] = StringName::create($type);
		}

		ksort($list);

		$result = [];
		$registerArgumentsSet = new RegisterArgumentsSet(static::SET_COLUMN_TYPES, $list);
		$result[$registerArgumentsSet->key()] = $registerArgumentsSet;

		foreach ($this->aliases as $alias) {
			$directive = new ExpectedArguments($alias, 1, [$registerArgumentsSet]);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectTableColumnTypes(): array {
		if (!Plugin::isLoaded('Migrations')) {
			return $this->defaultTypes;
		}

		$adapter = $this->getAdapter();

		return $adapter->getColumnTypes();
	}

	/**
	 * @param string $name
	 *
	 * @return \Migrations\Db\Adapter\AdapterInterface
	 */
	protected function getAdapter(string $name = 'default'): AdapterInterface {
		/** @var \Cake\Database\Connection $connection */
		$connection = ConnectionManager::get($name);
		$driver = $connection->getDriver();
		$driverClass = get_class($driver);
		$driverName = strtolower(substr((string)strrchr($driverClass, '\\'), 1));

		$config = $connection->config();
		$database = $config['database'] ?? null;

		$factory = AdapterFactory::instance();

		return $factory->getAdapter($driverName, [
			'adapter' => $driverName,
			'connection' => $connection,
			'database' => $database,
		]);
	}

}
