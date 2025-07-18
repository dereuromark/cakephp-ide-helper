<?php

namespace App\Controlller;

use Cake\ORM\Locator\LocatorAwareTrait;

/** @property \Cake\ORM\Table $Residents */
class TestMeController
{
	use LocatorAwareTrait;

	public function test(): void {
		$residentsTable = $this->fetchTable('Residents');
		$resident = $residentsTable->find(
			'all',
			[
				'contain' => ['Units', 'Rooms'],
			],
		)->first();

		$residentOther = $residentsTable->find(
			'all',
			[
				'contain' => ['Units', 'Rooms'],
			],
		)->firstOrFail();

		$residentX = $this->Residents->find(
			'all',
			[
				'contain' => ['Units', 'Rooms'],
			],
		)->first();

		$residentY = $this->Residents->find(
			'all',
			[
				'contain' => ['Units', 'Rooms'],
			],
		)->firstOrFail();
	}

}
