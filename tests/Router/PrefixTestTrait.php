<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;

trait PrefixTestTrait
{
    public function testPrefix(): void
    {
        $router = $this->container->use(Router::class);

        $router->group(['prefix' => 'prefix'], function(Router $router): void {
            $router->get('home', HomeController::class);
        });

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/prefix/home',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }

    public function testPrefixDeep(): void
    {
        $router = $this->container->use(Router::class);

        $router->group(['prefix' => 'prefix'], function(Router $router): void {
            $router->group(['prefix' => 'deep'], function(Router $router): void {
                $router->get('home', HomeController::class);
            });
        });

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/prefix/deep/home',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }

    public function testPrefixEmptyRoute(): void
    {
        $router = $this->container->use(Router::class);

        $router->group(['prefix' => 'prefix'], function(Router $router): void {
            $router->get('', HomeController::class);
        });

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/prefix',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }

    public function testPrefixLeadingSlash(): void
    {
        $router = $this->container->use(Router::class);

        $router->group(['prefix' => '/prefix'], function(Router $router): void {
            $router->get('home', HomeController::class);
        });

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/prefix/home',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }

    public function testPrefixTrailingSlash(): void
    {
        $router = $this->container->use(Router::class);

        $router->group(['prefix' => 'prefix/'], function(Router $router): void {
            $router->get('home', HomeController::class);
        });

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/prefix/home',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }
}
