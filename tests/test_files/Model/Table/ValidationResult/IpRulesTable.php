<?php

namespace TestApp\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

class IpRulesTable extends Table {

	/**
	 * @param \Cake\Validation\Validator $validator
	 * @return \Cake\Validation\Validator
	 */
	public function validationDefault(Validator $validator): Validator {
		$validator->add('allow', 'range', [
			/** @link verifyIpRanges() */
			'rule' => ['verifyIpRanges', 'allow'],
			'provider' => 'table',
			'message' => 'Please provide valid ip ranges',
		]);

		$validator->add('deny', 'range', [
			/** @link verifyDenyRanges() */
			'rule' => 'verifyDenyRanges',
			'provider' => 'table',
		]);

		// This should NOT get a link (no table provider)
		$validator->add('email', 'valid', [
			'rule' => 'email',
			'message' => 'Please provide a valid email',
		]);

		return $validator;
	}

	/**
	 * @param string $value
	 * @param array $context
	 * @return bool
	 */
	public function verifyIpRanges(string $value, array $context): bool {
		return true;
	}

	/**
	 * @param string $value
	 * @param array $context
	 * @return bool
	 */
	public function verifyDenyRanges(string $value, array $context): bool {
		return true;
	}

}
