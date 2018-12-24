<?php
	return [
		/*
		|--------------------------------------------------------------------------
		| Providers
		|--------------------------------------------------------------------------
		|
		| Don't forget to set this env file.
		| You can add providers to load in app
		|
		| Global: register the key service providers for the app
		| Production: register service providers only if the development environment is production
		| Staging: register service providers only if the development environment is staging
		| Develop: register service providers only if the development environment is develop
		|
		*/
		'global' => [
			'core' => \Core\Providers\CoreServiceProvider::class,
			'auth' => \Core\Providers\AuthServiceProvider::class,
			'graphql' => \Core\Providers\GraphQLServiceProvider::class,
			'app' => App\Providers\AppServiceProvider::class,
		],

		'production' => [
			//
		],

		'staging' => [
			//
		],

		'local' => [
			//
		],

		/*
		|--------------------------------------------------------------------------
		| Load Alias and Middleware
		|--------------------------------------------------------------------------
		|
		| Alias: create alias of class ('aliasName' => PathClass::class)
		| Middlewares: register middleware for all application routes (PathClass::class)
		| Route middlewares: register middleware in the application that can then be used to protect routes or/and controllers ( 'middlewareName' => PathClass::class)
		*/
		'alias' => [],

		'middlewares' => [
			\Core\Http\Middleware\CorsMiddleware::class
		],

		'route_middlewares' => [],

		'handler' => \ResponseHTTP\Response\Laravel\Exceptions\LumenHandler::class,
	];