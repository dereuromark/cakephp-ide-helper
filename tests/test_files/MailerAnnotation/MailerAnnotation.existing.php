<?php
namespace TestApp\Foo;

use TestApp\Mailer\NotificationMailer;

class MailerAnnotation {

	public function test($notificationMailer) {
		/** @uses \TestApp\Mailer\NotificationMailer::notify() */
		$notificationMailer->send('notify', []);
	}
}
