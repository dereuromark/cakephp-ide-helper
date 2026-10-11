<?php

namespace IdeHelper\Test\TestCase\Annotator;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use IdeHelper\Annotator\CallbackAnnotatorTask\TableCallbackAnnotatorTask;
use IdeHelper\Annotator\CallbackAnnotatorTask\VirtualFieldCallbackAnnotatorTask;
use IdeHelper\Annotator\CallbackAnnotatorTaskCollection;
use IdeHelper\Console\Io;

class CallbackAnnotatorTaskCollectionTest extends TestCase {

	protected function tearDown(): void {
		Configure::delete('IdeHelper.callbackAnnotatorTasks');
		parent::tearDown();
	}

	public function testDisabledTaskIsNotInstantiated(): void {
		Configure::write('IdeHelper.callbackAnnotatorTasks', [TableCallbackAnnotatorTask::class => null]);
		$tasks = (new CallbackAnnotatorTaskCollection())->tasks(new Io(new ConsoleIo()), [], '', '');
		$this->assertCount(1, $tasks);
		$this->assertInstanceOf(VirtualFieldCallbackAnnotatorTask::class, $tasks[0]);
	}

	public function testAllTasksCanBeDisabled(): void {
		Configure::write('IdeHelper.callbackAnnotatorTasks', [
			TableCallbackAnnotatorTask::class => null,
			VirtualFieldCallbackAnnotatorTask::class => null,
		]);
		$collection = new CallbackAnnotatorTaskCollection();
		$this->assertSame([], $collection->defaultTasks());
		$this->assertSame([], $collection->tasks(new Io(new ConsoleIo()), [], '', ''));
	}

}
