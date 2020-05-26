<?php

namespace TestApp\Generator\Task;

use IdeHelper\Generator\Task\FixtureTask;

class TestFixtureTask extends FixtureTask {

	/**
	 * @return \IdeHelper\ValueObject\StringName[]
	 */
	protected function getFixtures(): array {
		$list = parent::getFixtures();

		$list = [
			'app.Houses' => $list['app.Houses'],
			'core.Posts' => $list['core.Posts'],
			'plugin.IdeHelper.Cars' => $list['plugin.IdeHelper.Cars'],
			'plugin.MyNamespace/MyPlugin.Sub/My' => $list['plugin.MyNamespace/MyPlugin.Sub/My'],
		];

		return $list;
	}

}
