<?php
namespace TestApp\Foo;

class MailerAnnotation {

	public function test() {
		$this->getMailer('Notification')
			->send('notify', []);
	}
}
