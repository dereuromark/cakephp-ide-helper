<?php

declare(strict_types=1);

namespace IdeHelper\Generator\Task;

use IdeHelper\Generator\Directive\Override;

class AuthServiceLoadIdentifierTask implements TaskInterface {

	/**
	 * @return array<\IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$method = '\Authentication\AuthenticationService::loadIdentifier(0)';

		$map = [
			'Authentication.Password' => '\Authentication\Identifier\PasswordIdentifier::class',
			'Authentication.Token' => '\Authentication\Identifier\TokenIdentifier::class',
		];

		$directive = new Override($method, $map);

		return [$directive->key() => $directive];
	}

}
