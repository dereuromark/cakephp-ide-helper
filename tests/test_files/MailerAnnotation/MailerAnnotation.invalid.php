<?php
namespace TestApp\Mailer;

class DebugMailer {

	/**
	 * Usage: $this->getMailer('Debug')->send('test');
	 *
	 * @return self
	 */
	public function test(): self {
		$this->setTransport('gmail')
			->setTo('info@pfiff.me')
			->setSubject('Debug Email')
			->setEmailFormat('html')
			->viewBuilder()
			->setTemplate('default');

		return $this;
	}

}
