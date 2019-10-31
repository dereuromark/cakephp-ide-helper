<?php

namespace IdeHelper\Test\TestCase\CodeCompletion\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\CodeCompletion\Task\BehaviorTask;

class BehaviorTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\CodeCompletion\Task\BehaviorTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	public function setUp(): void {
		parent::setUp();

		$this->task = new BehaviorTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->create();

		$expected = <<<TXT
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

	/**
	 * Tools.AfterSave behavior.
	 *
	 * @var \Tools\Model\Behavior\AfterSaveBehavior
	 */
	public \$AfterSave;

	/**
	 * Tools.Bitmasked behavior.
	 *
	 * @var \Tools\Model\Behavior\BitmaskedBehavior
	 */
	public \$Bitmasked;

	/**
	 * Tools.Confirmable behavior.
	 *
	 * @var \Tools\Model\Behavior\ConfirmableBehavior
	 */
	public \$Confirmable;

	/**
	 * Tools.Jsonable behavior.
	 *
	 * @var \Tools\Model\Behavior\JsonableBehavior
	 */
	public \$Jsonable;

	/**
	 * Tools.Neighbor behavior.
	 *
	 * @var \Tools\Model\Behavior\NeighborBehavior
	 */
	public \$Neighbor;

	/**
	 * Tools.Passwordable behavior.
	 *
	 * @var \Tools\Model\Behavior\PasswordableBehavior
	 */
	public \$Passwordable;

	/**
	 * Tools.Reset behavior.
	 *
	 * @var \Tools\Model\Behavior\ResetBehavior
	 */
	public \$Reset;

	/**
	 * Tools.Slugged behavior.
	 *
	 * @var \Tools\Model\Behavior\SluggedBehavior
	 */
	public \$Slugged;

	/**
	 * Tools.String behavior.
	 *
	 * @var \Tools\Model\Behavior\StringBehavior
	 */
	public \$String;

	/**
	 * Tools.Toggle behavior.
	 *
	 * @var \Tools\Model\Behavior\ToggleBehavior
	 */
	public \$Toggle;

	/**
	 * Tools.TypeMap behavior.
	 *
	 * @var \Tools\Model\Behavior\TypeMapBehavior
	 */
	public \$TypeMap;

	/**
	 * Tools.Typographic behavior.
	 *
	 * @var \Tools\Model\Behavior\TypographicBehavior
	 */
	public \$Typographic;

}

TXT;
		$this->assertTextEquals($expected, $result);
	}

}
