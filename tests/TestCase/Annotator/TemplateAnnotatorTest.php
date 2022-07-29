<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\Database\Schema\TableSchema;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Annotator\TemplateAnnotator;
use IdeHelper\Console\Io;
use Shim\TestSuite\ConsoleOutput;
use Shim\TestSuite\TestTrait;
use TestApp\Model\Table\FooTable;

class TemplateAnnotatorTest extends TestCase {

	use DiffHelperTrait;
	use TestTrait;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $out;

	/**
	 * @var \Shim\TestSuite\ConsoleOutput
	 */
	protected $err;

	/**
	 * @var \IdeHelper\Console\Io
	 */
	protected $io;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$consoleIo = new ConsoleIo($this->out, $this->err);
		$this->io = new Io($consoleIo);

		$x = TableRegistry::get('IdeHelper.Foo', ['className' => FooTable::class]);
		$columns = [
			'id' => [
				'type' => 'integer',
				'length' => 11,
				'unsigned' => false,
				'null' => false,
				'default' => null,
				'comment' => '',
				'autoIncrement' => true,
				'baseType' => null,
				'precision' => null,
			],
		];
		$schema = new TableSchema('Foo', $columns);
		$x->setSchema($schema);
		TableRegistry::set('Foo', $x);

		Configure::delete('IdeHelper');
		Configure::write('IdeHelper.preemptive', true);
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void {
		Configure::delete('IdeHelper');

		parent::tearDown();
	}

	/**
	 * @return void
	 */
	public function testGetVariableAnnotations() {
		Configure::write('IdeHelper.autoCollect', function(array $variable) {
			if ($variable['name'] === 'date') {
				return 'Cake\I18n\FrozenTime';
			}

			return 'mixed';
		});

		$annotator = $this->_getAnnotatorMock([]);

		$variable = [
			'name' => 'date',
			'type' => 'object',
		];
		/** @uses \IdeHelper\Annotator\TemplateAnnotator::_getVariableAnnotation() */
		$result = $this->invokeMethod($annotator, 'getVariableAnnotation', [$variable]);
		$this->assertSame('@var Cake\I18n\FrozenTime $date', (string)$result);
	}

	/**
	 * @return void
	 */
	public function testNeedsViewAnnotation() {
		Configure::write('IdeHelper.preemptive', false);

		$annotator = $this->_getAnnotatorMock([]);

		$content = '';
		/** @uses \IdeHelper\Annotator\TemplateAnnotator::_needsViewAnnotation() */
		$result = $this->invokeMethod($annotator, 'needsViewAnnotation', [$content]);
		$this->assertFalse($result);

		$content = 'Foo Bar';
		/** @uses \IdeHelper\Annotator\TemplateAnnotator::_needsViewAnnotation() */
		$result = $this->invokeMethod($annotator, 'needsViewAnnotation', [$content]);
		$this->assertFalse($result);

		$content = 'Foo <?php echo $this->Foo->bar(); ?>';
		/** @uses \IdeHelper\Annotator\TemplateAnnotator::_needsViewAnnotation() */
		$result = $this->invokeMethod($annotator, 'needsViewAnnotation', [$content]);
		$this->assertTrue($result);

		$content = 'Foo <?= $x; ?>';
		/** @uses \IdeHelper\Annotator\TemplateAnnotator::_needsViewAnnotation() */
		$result = $this->invokeMethod($annotator, 'needsViewAnnotation', [$content]);
		$this->assertTrue($result);
	}

	/**
	 * Tests create() parsing part and creating a new PHP tag in first line.
	 *
	 * @return void
	 */
	public function testAnnotate() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/edit.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/edit.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 2 annotations added.', $output);
	}

	/**
	 * Tests loop and entity->field, as well as writing into an existing PHP tag.
	 *
	 * @return void
	 */
	public function testAnnotateLoop() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/loop.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/loop.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 3 annotations added.', $output);
	}

	/**
	 * Tests loop and entity->field, as well as writing into an existing PHP tag.
	 *
	 * @return void
	 */
	public function testAnnotatePhpLine() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/phpline.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/phpline.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 3 annotations added.', $output);
	}

	/**
	 * Tests merging with existing PHP tag and doc block.
	 *
	 * @return void
	 */
	public function testAnnotateExistingBasic() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/existing.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/existing.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 2 annotations added.', $output);
	}

	/**
	 * Tests merging with existing PHP tag and doc block and replacing outdated annotations.
	 *
	 * @return void
	 */
	public function testAnnotateExistingOutdated() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/outdated.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/outdated.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 2 annotations updated, 1 annotation removed, 1 annotation skipped.', $output);
	}

	/**
	 * Tests merging with existing PHP tag and doc block - PHP strict_types mode.
	 *
	 * @return void
	 */
	public function testAnnotateExistingStrict() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/existing_strict.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/existing_strict.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * Tests with empty template
	 *
	 * @return void
	 */
	public function testAnnotateEmptyPreemptive() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/empty.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/empty.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * Tests with template variables.
	 *
	 * @return void
	 */
	public function testAnnotateVars() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/vars.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/vars.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 6 annotations added.', $output);
	}

	/**
	 * Tests with empty template
	 *
	 * @return void
	 */
	public function testAnnotateEmpty() {
		Configure::write('IdeHelper.preemptive', false);

		$annotator = $this->_getAnnotatorMock([]);

		$callback = function($value) {
		};
		$annotator->expects($this->never())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/empty.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextEquals('', $output);
	}

	/**
	 * Tests merging with existing inline doc block.
	 *
	 * @return void
	 */
	public function testAnnotateInline() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/inline.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/inline.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * Tests that a docblock with a following inline one works.
	 *
	 * @return void
	 */
	public function testAnnotateWithFollowingInline() {
		$annotator = $this->_getAnnotatorMock([]);

		$expectedContent = str_replace("\r\n", "\n", file_get_contents(TEST_FILES . 'templates/following_inline.php'));
		$callback = function($value) use ($expectedContent) {
			$value = str_replace(["\r\n", "\r"], "\n", $value);
			if ($value !== $expectedContent) {
				$this->_displayDiff($expectedContent, $value);
			}

			return $value === $expectedContent;
		};
		$annotator->expects($this->once())->method('storeFile')->with($this->anything(), $this->callback($callback));

		$path = TEST_ROOT . 'templates/Foos/following_inline.php';
		$annotator->annotate($path);

		$output = $this->out->output();

		$this->assertTextContains('   -> 1 annotation added.', $output);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Annotator\TemplateAnnotator|\PHPUnit\Framework\MockObject\MockObject
	 */
	protected function _getAnnotatorMock(array $params) {
		$params += [
			AbstractAnnotator::CONFIG_REMOVE => true,
			AbstractAnnotator::CONFIG_DRY_RUN => true,
		];

		return $this->getMockBuilder(TemplateAnnotator::class)->setMethods(['storeFile'])->setConstructorArgs([$this->io, $params])->getMock();
	}

}
