<?php

declare(strict_types=1);

namespace IdeHelper\Generator\Task;

use IdeHelper\Generator\Directive\Override;
use IdeHelper\Generator\Task\TaskInterface;

class AuthServiceLoadIdentifierTask implements TaskInterface
{
    /**
     * @return \IdeHelper\Generator\Directive\BaseDirective[]
     */
    public function collect(): array
    {
        $method = '\Authentication\AuthenticationService::loadIdentifier(0)';

        $map = [
            'Authentication.Password' => '\Authentication\Identifier\PasswordIdentifier::class',
            'Authentication.Token'    => '\Authentication\Identifier\TokenIdentifier::class',
        ];

        $directive = new Override($method, $map);

        return [$directive->key() => $directive];
    }
}
