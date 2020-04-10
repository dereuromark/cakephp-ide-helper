<?php

namespace IdeHelper\Generator\Task;

use Bake\Utility\TableScanner;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use IdeHelper\Generator\Directive\ExpectedArguments;

/**
 * This task is useful when using Migrations plugin and creating Migration files.
 */
class DatabaseTableTask implements TaskInterface {

	/**
	 * @var string[]
	 */
	protected $aliases = [
		'\Migrations\AbstractMigration::table()',
	];

	/**
	 * @return \IdeHelper\Generator\Directive\BaseDirective[]
	 */
	public function collect(): array {
		$list = [];

		$tables = $this->collectTables();
		foreach ($tables as $table) {
			$list[$table] = "'$table'";
		}

		ksort($list);

		$result = [];
		foreach ($this->aliases as $alias) {
			$directive = new ExpectedArguments($alias, 0, $list);
			$result[$directive->key()] = $directive;
		}

		return $result;
	}

	/**
	 * @return string[]
	 */
	protected function collectTables(): array {
		$db = $this->getConnection();

		$tables = (new TableScanner($db))->listAll();
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
