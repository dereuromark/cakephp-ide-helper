<?php

namespace IdeHelper\Test\TestCase\Illuminator\Task;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\AbstractAnnotator;
use IdeHelper\Console\Io;
use IdeHelper\Illuminator\Task\ControllerDefaultTableTask;
use Shim\TestSuite\ConsoleOutput;

class ControllerDefaultTableTaskTest extends TestCase {

	protected ConsoleOutput $out;

	protected ConsoleOutput $err;

	protected Io $io;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->out = new ConsoleOutput();
		$this->err = new ConsoleOutput();
		$consoleIo = new ConsoleIo($this->out, $this->err);
		$this->io = new Io($consoleIo);
	}

	/**
	 * @return void
	 */
	public function testShouldRun() {
		$task = $this->_getTask();

		$result = $task->shouldRun('src/Controller/NoTableController.php');
		$this->assertTrue($result);

		$result = $task->shouldRun('src/Controller/AppController.php');
		$this->assertFalse($result);

		$result = $task->shouldRun('src/Model/Table/Wheels.php');
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testShouldRunWithDifferentPathSeparators() {
		$task = $this->_getTask();

		// Forward slashes
		$result = $task->shouldRun('src/Controller/TestController.php');
		$this->assertTrue($result);

		// Backslashes (Windows-style)
		$result = $task->shouldRun('src\\Controller\\TestController.php');
		$this->assertTrue($result);

		// Should fail without Controller in path
		$result = $task->shouldRun('src/TestController.php');
		$this->assertFalse($result);
	}

	/**
	 * @return void
	 */
	public function testRunWithExistingTable() {
		$task = $this->_getTask();

		$path = APP . 'Controller/FoosController.php';
		$this->assertFileExists($path);

		$content = file_get_contents($path);
		$result = $task->run($content, $path);

		// Should not add defaultTable if table exists
		$this->assertSame($content, $result);
	}

	/**
	 * @return void
	 */
	public function testRunWithoutTable() {
		$task = $this->_getTask();

		$content = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

class NoTableController extends Controller {
}

PHP;

		$expected = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

class NoTableController extends Controller {

	protected ?string $defaultTable = '';
}

PHP;

		$path = 'src/Controller/NoTableController.php';
		$result = $task->run($content, $path);

		$this->assertTextEquals($expected, $result);
		$this->assertStringContainsString("protected ?string \$defaultTable = '';", $result);
	}

	/**
	 * @return void
	 */
	public function testRunWithExistingProperty() {
		$task = $this->_getTask();

		$content = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

class CustomController extends Controller {

	protected ?string $defaultTable = 'CustomTable';
}

PHP;

		$path = 'src/Controller/CustomController.php';
		$result = $task->run($content, $path);

		// Should not add if property already exists
		$this->assertSame($content, $result);
	}

	/**
	 * @return void
	 */
	public function testRunWithOtherProperties() {
		$task = $this->_getTask();

		$content = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

class PropertiesController extends Controller {

	protected string $myProperty = 'test';

	public function index() {
	}
}

PHP;

		$expected = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

class PropertiesController extends Controller {

	protected ?string $defaultTable = '';

	protected string $myProperty = 'test';

	public function index() {
	}
}

PHP;

		$path = 'src/Controller/PropertiesController.php';
		$result = $task->run($content, $path);

		$this->assertTextEquals($expected, $result);
		$this->assertStringContainsString("protected ?string \$defaultTable = '';", $result);
	}

	/**
	 * @return void
	 */
	public function testRunWithPluginController() {
		$task = $this->_getTask();

		$content = <<<'PHP'
<?php
namespace MyPlugin\Controller;

use Cake\Controller\Controller;

class CustomController extends Controller {
}

PHP;

		$expected = <<<'PHP'
<?php
namespace MyPlugin\Controller;

use Cake\Controller\Controller;

class CustomController extends Controller {

	protected ?string $defaultTable = '';
}

PHP;

		$path = 'plugins/MyPlugin/src/Controller/CustomController.php';
		$result = $task->run($content, $path);

		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testRunWithNestedPluginNamespace() {
		$task = $this->_getTask();

		$content = <<<'PHP'
<?php
namespace Vendor\MyPlugin\Controller;

use Cake\Controller\Controller;

class CustomController extends Controller {
}

PHP;

		$expected = <<<'PHP'
<?php
namespace Vendor\MyPlugin\Controller;

use Cake\Controller\Controller;

class CustomController extends Controller {

	protected ?string $defaultTable = '';
}

PHP;

		$path = 'plugins/Vendor/MyPlugin/src/Controller/CustomController.php';
		$result = $task->run($content, $path);

		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testRunWithFastPropertyCheck() {
		$task = $this->_getTask([
			'fastPropertyCheck' => true,
		]);

		$content = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

class ExistingController extends Controller {

	protected ?string $defaultTable = '';
}

PHP;

		$path = 'src/Controller/ExistingController.php';
		$result = $task->run($content, $path);

		// Should not add if property already exists (using fast regex check)
		$this->assertSame($content, $result);
	}

	/**
	 * @return void
	 */
	public function testRunWithPropertyInCommentDefaultCheck() {
		$task = $this->_getTask();

		$content = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * Controller with defaultTable in comment
 * protected ?string $defaultTable = '';
 */
class CommentController extends Controller {
}

PHP;

		$expected = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * Controller with defaultTable in comment
 * protected ?string $defaultTable = '';
 */
class CommentController extends Controller {

	protected ?string $defaultTable = '';
}

PHP;

		$path = 'src/Controller/CommentController.php';
		$result = $task->run($content, $path);

		// Token-based check correctly identifies no actual property exists
		$this->assertTextEquals($expected, $result);
	}

	/**
	 * @return void
	 */
	public function testRunWithPropertyInCommentFastCheck() {
		$task = $this->_getTask([
			'fastPropertyCheck' => true,
		]);

		$content = <<<'PHP'
<?php
namespace App\Controller;

use Cake\Controller\Controller;

/**
 * Controller with defaultTable in comment
 * protected ?string $defaultTable = '';
 */
class CommentController extends Controller {
}

PHP;

		$path = 'src/Controller/CommentController.php';
		$result = $task->run($content, $path);

		// Fast regex check has false-positive on comment (acceptable trade-off for speed)
		// If you need 100% accuracy, don't enable fastPropertyCheck
		$this->assertSame($content, $result);
	}

	/**
	 * @param array $params
	 * @return \IdeHelper\Illuminator\Task\ControllerDefaultTableTask
	 */
	protected function _getTask(array $params = []) {
		$params += [
			AbstractAnnotator::CONFIG_DRY_RUN => true,
			AbstractAnnotator::CONFIG_VERBOSE => true,
		];

		return new ControllerDefaultTableTask($params);
	}

}
