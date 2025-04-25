<?php

namespace IdeHelper\Generator\Task;

use Cake\Http\ServerRequest;
use Cake\Http\Session;
use Cake\Routing\Route\Route;
use Cake\Routing\Router;
use IdeHelper\Generator\Directive\ExpectedArguments;
use IdeHelper\Generator\Directive\Override;
use IdeHelper\Utility\Plugin;
use IdeHelper\ValueObject\ClassName;
use IdeHelper\ValueObject\StringName;

class RequestTask implements TaskInterface {

	public const CLASS_REQUEST = ServerRequest::class;

	/**
	 * @var array<string>
	 */
	protected static array $paramKeys = [
		'controller',
		'action',
		'plugin',
		'prefix',
		'pass',
		'_matchedRoute',
		'_ext',
	];

	/**
	 * @var array<string, string>
	 */
	protected static array $attributeKeys = [
		'csrfToken' => 'string',
		'params' => 'array',
		'webroot' => 'string',
		'base' => 'string',
		'here' => 'string',
		'cspScriptNonce' => 'string',
		'cspStyleNonce' => 'string',
		'formTokenData' => 'array',
		'paging' => 'array',
	];

	/**
	 * @return array<string, \IdeHelper\Generator\Directive\BaseDirective>
	 */
	public function collect(): array {
		$result = [];

		$list = $this->collectParamKeys();

		$method = '\\' . static::CLASS_REQUEST . '::getParam()';
		$directive = new ExpectedArguments($method, 0, $list);
		$result[$directive->key()] = $directive;

		$map = $this->collectAttributes();

		$method = '\\' . static::CLASS_REQUEST . '::getAttribute(0)';
		$directive = new Override($method, $map);
		$result[$directive->key()] = $directive;

		return $result;
	}

	/**
	 * @return array<string, \IdeHelper\ValueObject\StringName>
	 */
	protected function collectParamKeys(): array {
		$keys = [];
		foreach (static::$paramKeys as $key) {
			$keys[$key] = StringName::create($key);
		}

		ksort($keys);

		return $keys;
	}

	/**
	 * @return array<string, mixed>
	 */
	protected function collectAttributes(): array {
		$request = Router::getRequest() ?: new ServerRequest();
		$defaults = array_keys($request->getAttributes());

		$attributes = [
			'route' => ClassName::create(Route::class),
			'session' => ClassName::create(Session::class),
		];
		foreach (static::$attributeKeys as $key => $type) {
			$attributes[$key] = StringName::create($type);
		}

		$attributes = $this->addAttributesFromPlugins($attributes);

		foreach ($defaults as $default) {
			if (isset($attributes[$default])) {
				continue;
			}

			$attributes[$default] = StringName::create('');
		}

		ksort($attributes);

		return $attributes;
	}

	/**
	 * @param array<string, mixed> $attributes
	 * @return array<string, mixed>
	 */
	protected function addAttributesFromPlugins(array $attributes): array {
		if (Plugin::isLoaded('Authorization')) {
			$attributes['identity'] = ClassName::create('Authorization\IdentityInterface');
		} elseif (Plugin::isLoaded('Authentication')) {
			$attributes['identity'] = ClassName::create('Authentication\IdentityInterface');
		}
		if (Plugin::isLoaded('Authentication')) {
			$attributes['authentication'] = ClassName::create('Authentication\AuthenticationService');
			$attributes['authenticationResult'] = ClassName::create('Authentication\Authenticator\Result');
		}

		return $attributes;
	}

}
