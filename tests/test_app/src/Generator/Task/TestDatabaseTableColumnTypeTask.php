<?php

namespace IdeHelper\Test\test_app\src\Generator\Task;

use IdeHelper\Generator\Task\DatabaseTableColumnTypeTask;

class TestDatabaseTableColumnTypeTask extends DatabaseTableColumnTypeTask
{
	/**
	 * @param string $name
	 *
	 * @return \Phinx\Db\Adapter\AdapterInterface
	 */
	protected function getAdapter(string $name = 'test') {
		return parent::getAdapter($name);
	}

}
