<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\Plugin;
use Cake\Datasource\ConnectionManager;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;
use Migrations\Db\Adapter\AdapterFactory;
use Migrations\Db\Adapter\AdapterInterface;
use Migrations\Migrations;
use Phinx\Db\Adapter\AdapterInterface as PhinxAdapterInterface;

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
	 * @return \Phinx\Db\Adapter\AdapterInterface|\Migrations\Db\Adapter\AdapterInterface
	 */
	protected function getAdapter(string $name = 'default') {
		// Migrations v5+ (no Phinx)
		if (class_exists('Migrations\Db\Adapter\AdapterFactory')) {
			return $this->getAdapterV5($name);
		}

		// Migrations v4 (Phinx-based)
		return $this->getAdapterV4($name);
	}

	/**
	 * @param string $name
	 *
	 * @return \Migrations\Db\Adapter\AdapterInterface
	 */
	protected function getAdapterV5(string $name): AdapterInterface {
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

	/**
	 * @deprecated Will be removed in a future version
	 *
	 * @param string $name
	 *
	 * @return \Phinx\Db\Adapter\AdapterInterface
	 */
	protected function getAdapterV4(string $name): PhinxAdapterInterface {
		$params = [
			'connection' => $name,
		];

		$migrations = new Migrations();
		$input = $migrations->getInput('Migrate', [], $params);
		$migrations->setInput($input);
		$manager = $migrations->getManager($migrations->getConfig());

		$env = $manager->getEnvironment('default');

		return $env->getAdapter();
	}

}
