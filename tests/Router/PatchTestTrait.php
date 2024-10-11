<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ClosureRoute;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait PatchTestTrait
{
    public function testPatch(): void
    {
        Router::patch('home', HomeController::class);

        $request = new ServerRequest([
            'method' => 'patch',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

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
        Router::patch('home/alternate', [HomeController::class, 'altMethod']);

        $request = new ServerRequest([
            'method' => 'patch',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home/alternate',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

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
        Router::patch('home/alternate/(.*)/(.*)/(.*)', [HomeController::class, 'altMethod']);

        $request = new ServerRequest([
            'method' => 'patch',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home/alternate/test/a/2',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

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
                'test',
                'a',
                '2',
            ],
            $route->getArguments()
        );
    }

    public function testPatchClosure(): void
    {
        $callback = function() {};

        Router::patch('test', $callback);

        $request = new ServerRequest([
            'method' => 'patch',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

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

        Router::patch('test/(.*)/(.*)', $callback);

        $request = new ServerRequest([
            'method' => 'patch',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/a/2',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

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
                'a',
                '2',
            ],
            $route->getArguments()
        );
    }
}
