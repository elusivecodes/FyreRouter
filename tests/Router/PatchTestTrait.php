<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ClosureRoute;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;

trait PatchTestTrait
{
    public function testPatch(): void
    {
        $router = $this->container->use(Router::class);

        $router->patch('home', HomeController::class);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'method' => 'patch',
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/home',
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

        $this->assertSame(
            'index',
            $route->getAction()
        );
    }

    public function testPatchAction(): void
    {
        $router = $this->container->use(Router::class);

        $router->patch('home/alternate', [HomeController::class, 'altMethod']);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'method' => 'patch',
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/home/alternate',
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

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testPatchArguments(): void
    {
        $router = $this->container->use(Router::class);

        $router->patch('home/alternate/{a}/{b}/{c}', [HomeController::class, 'altMethod']);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'method' => 'patch',
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/home/alternate/test/a/2',
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

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );

        $this->assertSame(
            [
                'a' => 'test',
                'b' => 'a',
                'c' => '2',
            ],
            $route->getArguments()
        );
    }

    public function testPatchClosure(): void
    {
        $callback = function() {};

        $router = $this->container->use(Router::class);

        $router->patch('test', $callback);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'method' => 'patch',
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ClosureRoute::class,
            $route
        );

        $this->assertSame(
            $callback,
            $route->getDestination()
        );
    }

    public function testPatchClosureArguments(): void
    {
        $callback = function() {};

        $router = $this->container->use(Router::class);

        $router->patch('test/{a}/{b}', $callback);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'method' => 'patch',
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/a/2',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ClosureRoute::class,
            $route
        );

        $this->assertSame(
            $callback,
            $route->getDestination()
        );

        $this->assertSame(
            [
                'a' => 'a',
                'b' => '2',
            ],
            $route->getArguments()
        );
    }
}
