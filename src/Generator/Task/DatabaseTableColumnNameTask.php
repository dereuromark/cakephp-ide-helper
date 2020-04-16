<?php

namespace IdeHelper\Generator\Task;

use Cake\Datasource\ConnectionManager;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;

/**
 * This task is useful when using Migrations plugin and creating Migration files.
 */
class DatabaseTableColumnNameTask extends DatabaseTableTask {

	public const SET_TABLE_NAMES = 'tableNames';

	/**
	 * @var string[]
	 */
	protected $aliases = [
		'\Migrations\Table::addColumn()',
		'\Migrations\Table::changeColumn()',
		'\Migrations\Table::removeColumn()',
		'\Migrations\Table::renameColumn()',
		'\Migrations\Table::hasColumn()',
	];

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$list = [];

		$names = $this->collectTableColumnNames();
		foreach ($names as $type) {
			$list[$type] = StringName::create($type);
		}

		ksort($list);

		$result = [];
		$registerArgumentsSet = new RegisterArgumentsSet(static::SET_TABLE_NAMES, $list);
		$result[$registerArgumentsSet->key()] = $registerArgumentsSet;

		foreach ($this->aliases as $alias) {
			$directive = new ExpectedArguments($alias, 0, [$registerArgumentsSet]);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function collectTableColumnNames(): array {
		$schema = $this->getConnection()->getSchemaCollection();

		$tables = $this->collectTables();

		$columns = [];
		foreach ($tables as $table) {
			$tableSchema = $schema->describe($table);
			$columns = array_merge($columns, $tableSchema->columns());
		}

		return array_unique($columns);
	}

	/**
	 * @param string $name
	 *
	 * @return \Cake\Database\Connection
	 */
	protected function getConnection(string $name = 'default') {
		/** @var \Cake\Database\Connection $connection */
		$connection = ConnectionManager::get($name);

		return $connection;
	}

}
