<?php
declare(strict_types=1);

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ModelAnnotator;
use IdeHelper\Console\Io;
use ReflectionMethod;
use Shim\TestSuite\ConsoleOutput;

class AbstractAnnotatorTest extends TestCase {

	/**
	 * @param array<string, mixed> $params
	 * @return \IdeHelper\Annotator\ModelAnnotator
	 */
	protected function annotator(array $params): ModelAnnotator {
		$io = new Io(new ConsoleIo(new ConsoleOutput(), new ConsoleOutput()));

		return new ModelAnnotator($io, $params);
	}

	/**
	 * @param \IdeHelper\Annotator\ModelAnnotator $annotator
	 * @param string $content
	 * @return bool
	 */
	protected function isSuperseded(ModelAnnotator $annotator, string $content): bool {
		$method = new ReflectionMethod($annotator, 'methodSupersededByParent');
		$method->setAccessible(true);

		return $method->invoke($annotator, $content);
	}

	/**
	 * No list configured: nothing is superseded (non-model annotators).
	 *
	 * @return void
	 */
	public function testFalseWhenNoSupersededListConfigured() {
		$annotator = $this->annotator([]);

		$this->assertFalse(
			$this->isSuperseded($annotator, 'saveOrFail(\App\Model\Entity\Foo $entity, array $options = [])'),
		);
	}

	/**
	 * A name in the list is superseded (in-use guard must not protect it).
	 *
	 * @return void
	 */
	public function testTrueForNameInSupersededList() {
		$annotator = $this->annotator([
			AbstractAnnotator::CONFIG_SUPERSEDED_METHODS => ['save', 'saveOrFail', 'patchEntity'],
		]);

		$this->assertTrue(
			$this->isSuperseded($annotator, 'saveOrFail(\App\Model\Entity\Foo $entity, array $options = [])'),
		);
		$this->assertTrue(
			$this->isSuperseded($annotator, 'patchEntity(\App\Model\Entity\Foo $entity, array $data, array $options = [])'),
		);
	}

	/**
	 * Always-emitted batch variants stay protected (not in the list).
	 *
	 * @return void
	 */
	public function testFalseForNameNotInSupersededList() {
		$annotator = $this->annotator([
			AbstractAnnotator::CONFIG_SUPERSEDED_METHODS => ['save', 'saveOrFail'],
		]);

		$this->assertFalse(
			$this->isSuperseded($annotator, 'saveManyOrFail(iterable $entities, array $options = [])'),
		);
	}

	/**
	 * Unparseable content never matches.
	 *
	 * @return void
	 */
	public function testFalseForUnparseableContent() {
		$annotator = $this->annotator([
			AbstractAnnotator::CONFIG_SUPERSEDED_METHODS => ['save'],
		]);

		$this->assertFalse($this->isSuperseded($annotator, 'not a signature'));
	}

}
