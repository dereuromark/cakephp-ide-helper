<?php

namespace IdeHelper\Generator\Task;

use Bake\Utility\TableScanner;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;
use IdeHelper\ValueObject\StringName;
use Throwable;

/**
 * This task is useful when using Migrations plugin and creating Migration files.
 */
class DatabaseTableTask implements TaskInterface {

	/**
	 * @var string
	 */
	public const SET_TABLE_NAMES = 'tableNames';

	/**
	 * @var array<string>|null
	 */
	protected static $tables;

	/**
	 * @var array<string>
	 */
	protected $aliases = [
		'\Migrations\AbstractMigration::table()',
		'\Migrations\AbstractSeed::table()',
		'\Phinx\Seed\AbstractSeed::table()',
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$list = [];

		$tables = $this->collectTables();
		foreach ($tables as $table) {
			$list[$table] = StringName::create($table);
		}

		$result = [];

		ksort($list);
		$registerArgumentsSet = new RegisterArgumentsSet(static::SET_TABLE_NAMES, $list);
		$result[$registerArgumentsSet->key()] = $registerArgumentsSet;

		foreach ($this->aliases as $alias) {
			$directive = new ExpectedArguments($alias, 0, [$registerArgumentsSet]);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return array<string>
	 */
	protected function collectTables(): array {
		if (static::$tables !== null) {
			$tables = static::$tables;
		} else {
			$db = $this->getConnection();
			try {
				$tables = (new TableScanner($db))->listAll();
			} catch (Throwable $exception) {
				$tables = [];
			}
			static::$tables = $tables;
		}

		foreach ($tables as $key => $table) {
			if (stripos($table, 'phinxlog') !== false) {
				unset($tables[$key]);
			}
		}

		$blacklist = (array)Configure::read('IdeHelper.skipDatabaseTables');
		foreach ($tables as $key => $table) {
			foreach ($blacklist as $regex) {
				if ((bool)preg_match($regex, $table)) {
					unset($tables[$key]);
				}
			}
		}

		return $tables;
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
