<?php

namespace TestApp;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\RouteBuilder;

class Application extends BaseApplication
{
	public function bootstrap(): void
	{
	}

	public function routes(RouteBuilder $routes): void
	{
	}

	public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
	{
		return $middlewareQueue;
	}
}
