<?php

namespace IdeHelper\Generator;

use Cake\Console\ConsoleIo;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;

class PhpstormGenerator implements GeneratorInterface {

	/**
	 * @var \IdeHelper\Generator\TaskCollection
	 */
	protected $taskCollection;

	/**
	 * @var \Cake\Console\ConsoleIo|null
	 */
	protected $consoleIo;

	/**
	 * @param \IdeHelper\Generator\TaskCollection $taskCollection
	 * @param \Cake\Console\ConsoleIo|null $consoleIo
	 */
	public function __construct(TaskCollection $taskCollection, ?ConsoleIo $consoleIo = null) {
		$this->taskCollection = $taskCollection;
		$this->consoleIo = $consoleIo;
	}

	/**
	 * @return string
	 */
	public function generate(): string {
		$map = $this->taskCollection->getMap();

		$this->outputSetInfo($map);

		return $this->build($map);
	}

	/**
	 * @param array<\IdeHelper\Generator\Directive\BaseDirective> $map
	 *
	 * @return string
	 */
	protected function build(array $map): string {
		$overrides = [];
		foreach ($map as $directive) {
			$overrides[] = $directive->build();
		}
		$overrides = implode(PHP_EOL . PHP_EOL, $overrides);

		$template = <<<TXT
<?php
// @link https://confluence.jetbrains.com/display/PhpStorm/PhpStorm+Advanced+Metadata
namespace PHPSTORM_META {

$overrides

}

TXT;

		return $template;
	}

	/**
	 * @param array<\IdeHelper\Generator\Directive\BaseDirective> $map
	 *
	 * @return void
	 */
	protected function outputSetInfo(array $map): void {
		if (!$this->consoleIo) {
			return;
		}

		$sets = [];
		foreach ($map as $directive) {
			if ($directive instanceof RegisterArgumentsSet) {
				$sets[] = $directive->toArray()['set'];
			}
		}

		$this->consoleIo->verbose('The following sets are available for re-use:');
		foreach ($sets as $set) {
			$this->consoleIo->verbose('- ' . $set);
		}
	}

}
