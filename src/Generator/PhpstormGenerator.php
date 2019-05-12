<?php
namespace IdeHelper\Generator;

class PhpstormGenerator implements GeneratorInterface {

	/**
	 * @var \IdeHelper\Generator\TaskCollection
	 */
	protected $taskCollection;

	/**
	 * @param \IdeHelper\Generator\TaskCollection $taskCollection
	 */
	public function __construct(TaskCollection $taskCollection) {
		$this->taskCollection = $taskCollection;
	}

	/**
	 * @return string
	 */
	public function generate(): string {
		$map = $this->taskCollection->getMap();

		return $this->build($map);
	}

	/**
	 * @param string[][] $map
	 *
	 * @return string
	 */
	protected function build(array $map): string {
		$overrides = [];
		foreach ($map as $method => $array) {
			$mapDefinitions = $this->buildMapDefinitions($array);

			$overrides[] = <<<TXT
	override(
		$method,
		map([
$mapDefinitions
		])
	);
TXT;

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
	 * @param string[] $array
	 *
	 * @return string
	 */
	protected function buildMapDefinitions(array $array): string {
		$result = [];
		foreach ($array as $alias => $value) {
			$result[] = "\t\t\t" . "'" . str_replace("'", "\'", $alias) . "' => " . $value . ',';
		}

		return implode(PHP_EOL, $result);
	}

}
