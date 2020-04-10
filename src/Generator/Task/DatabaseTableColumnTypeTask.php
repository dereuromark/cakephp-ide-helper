<?php

namespace IdeHelper\Generator\Task;

use Cake\Core\Plugin;
use IdeHelper\Generator\Directive\ExpectedArguments;
use Migrations\Migrations;

/**
 * This task is useful when using Migrations plugin and creating Migration files.
 */
class DatabaseTableColumnTypeTask implements TaskInterface {

	/**
	 * @var string[]
	 */
	protected $aliases = [
		'\Migrations\Table::addColumn()',
		'\Migrations\Table::changeColumn()',
	];

	/**
	 * Used if the Migrations plugin is not loaded
	 *
	 * @var string[]
	 */
	protected $defaultTypes = [
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
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$list = [];

		$types = $this->collectTableColumnTypes();
		foreach ($types as $type) {
			$list[$type] = "'$type'";
		}

		ksort($list);

		$result = [];
		foreach ($this->aliases as $alias) {
			$directive = new ExpectedArguments($alias, 1, $list);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return string[]
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
	 * @return \Phinx\Db\Adapter\AdapterInterface
	 */
	protected function getAdapter(string $name = 'default') {
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