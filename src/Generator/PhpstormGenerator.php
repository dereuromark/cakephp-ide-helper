<?php

namespace IdeHelper\Generator;

use IdeHelper\Console\Io;
use IdeHelper\Generator\Directive\RegisterArgumentsSet;

class PhpstormGenerator implements GeneratorInterface {

	protected TaskCollection $taskCollection;

	protected ?Io $io = null;

	/**
	 * @param \IdeHelper\Generator\TaskCollection $taskCollection
	 * @param \IdeHelper\Console\Io|null $io
	 */
	public function __construct(TaskCollection $taskCollection, ?Io $io = null) {
		$this->taskCollection = $taskCollection;
		$this->io = $io;
	}

	/**
	 * @return string
	 */
	public function generate(): string {
		if (method_exists($this->taskCollection, 'getMapped')) {
			$map = $this->taskCollection->getMapped();
		} else {
			// @codeCoverageIgnoreStart
			$map = $this->taskCollection->getMap();
			// @codeCoverageIgnoreEnd
		}

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
		if (!$this->io) {
			return;
		}

		$sets = [];
		foreach ($map as $directive) {
			if ($directive instanceof RegisterArgumentsSet) {
				$sets[] = $directive->toArray()['set'];
			}
		}

		$this->io->verbose('The following sets are available for re-use:');
		foreach ($sets as $set) {
			$this->io->verbose('- ' . $set);
		}
	}

}
