<?php

namespace IdeHelper\Test\TestCase\CodeCompletion;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\CodeCompletionGenerator;
use IdeHelper\CodeCompletion\TaskCollection;

class CodeCompletionGeneratorTest extends TestCase {

	/**
	 * @var \IdeHelper\CodeCompletion\CodeCompletionGenerator
	 */
	protected $generator;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$taskCollection = new TaskCollection();
		$this->generator = new CodeCompletionGenerator($taskCollection);

		$file = TMP . 'CodeCompletionCakeORM.php';
		if (file_exists($file)) {
			unlink($file);
		}
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->generator->generate();

		$expected = [
			'Cake\ORM',
		];

		$this->assertSame($expected, $result);
		$this->assertFileExists(TMP . 'CodeCompletionCakeORM.php');

		$result = file_get_contents(TMP . 'CodeCompletionCakeORM.php');
		$expected = <<<TXT
<?php
namespace Cake\ORM;

/**
 * Only for code completion - regenerate using `bin/cake code_completion generate`.
 */
abstract class BehaviorRegistry extends \Cake\Core\ObjectRegistry {

	/**
	 * MyNamespace/MyPlugin.My behavior.
	 *
	 * @var \MyNamespace\MyPlugin\Model\Behavior\MyBehavior
	 */
	public \$My;

	/**
	 * Shim.Nullable behavior.
	 *
	 * @var \Shim\Model\Behavior\NullableBehavior
	 */
	public \$Nullable;

}

TXT;

		$this->assertTextEquals($expected, $result);
	}

}
