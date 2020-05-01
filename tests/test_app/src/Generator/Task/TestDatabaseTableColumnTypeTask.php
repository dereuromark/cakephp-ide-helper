<?php

namespace TestApp\Generator\Task;

use IdeHelper\Generator\Task\DatabaseTableColumnTypeTask;

class TestDatabaseTableColumnTypeTask extends DatabaseTableColumnTypeTask
{
	/**
	 * @param string $name
	 *
	 * @return \Phinx\Db\Adapter\AdapterInterface
	 */
	protected function getAdapter($name = 'test') {
		return parent::getAdapter($name);
	}

}
