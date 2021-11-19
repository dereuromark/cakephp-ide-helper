<?php
namespace TestApp\Foo;

class MailerAnnotation {

	public function test() {
		$notificationMailer = $this->getMailer('Notification');

		$notificationMailer->send('notify', []);
	}
}
