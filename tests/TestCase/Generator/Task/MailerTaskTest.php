<?php

namespace IdeHelper\Test\TestCase\Generator\Task;

use Cake\TestSuite\TestCase;
use IdeHelper\Generator\Task\MailerTask;

class MailerTaskTest extends TestCase {

	/**
	 * @var \IdeHelper\Generator\Task\MailerTask
	 */
	protected $task;

	/**
	 * @return void
	 */
	protected function setUp(): void {
		parent::setUp();

		$this->task = new MailerTask();
	}

	/**
	 * @return void
	 */
	public function testCollect() {
		$result = $this->task->collect();

		$this->assertCount(1, $result);

		/** @var \IdeHelper\Generator\Directive\Override $directive */
		$directive = array_shift($result);
		$this->assertSame('\Cake\Mailer\MailerAwareTrait::getMailer(0)', $directive->toArray()['method']);

		$map = $directive->toArray()['map'];

		$expected = '\TestApp\Mailer\UserMailer::class';
		$this->assertSame($expected, (string)$map['User']);
	}

}
