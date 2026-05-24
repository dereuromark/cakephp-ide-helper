<?php
declare(strict_types=1);

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\ModelAnnotator;
use IdeHelper\Console\Io;
use ReflectionMethod;
use ReflectionProperty;
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
	 * Build an annotator together with the ConsoleOutput it writes to, so
	 * report() output can be asserted.
	 *
	 * @param array<string, mixed> $params
	 * @return array{0: \IdeHelper\Annotator\ModelAnnotator, 1: \Shim\TestSuite\ConsoleOutput}
	 */
	protected function annotatorWithOutput(array $params): array {
		$out = new ConsoleOutput();
		$annotator = new ModelAnnotator(new Io(new ConsoleIo($out, $out)), $params);

		return [$annotator, $out];
	}

	/**
	 * report() surfaces the removable count so a plain run (no -r) makes
	 * outdated annotations discoverable instead of silently ignoring them.
	 *
	 * @return void
	 */
	public function testReportSurfacesRemovableCount() {
		[$annotator, $out] = $this->annotatorWithOutput([]);

		$counter = new ReflectionProperty($annotator, '_counter');
		$counter->setValue($annotator, [
			AbstractAnnotator::COUNT_ADDED => 0,
			AbstractAnnotator::COUNT_UPDATED => 0,
			AbstractAnnotator::COUNT_REMOVED => 0,
			AbstractAnnotator::COUNT_REMOVABLE => 3,
			AbstractAnnotator::COUNT_SKIPPED => 0,
		]);

		$report = new ReflectionMethod($annotator, 'report');
		$report->invoke($annotator);

		$this->assertStringContainsString('3 annotations outdated (run with -r to remove)', $out->output());
	}

	/**
	 * @param \IdeHelper\Annotator\ModelAnnotator $annotator
	 * @param string $content
	 * @return bool
	 */
	protected function isSuperseded(ModelAnnotator $annotator, string $content): bool {
		$method = new ReflectionMethod($annotator, 'methodSupersededByParent');

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
