<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ClosureRoute;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait PutTestTrait
{

    public function testPut(): void
    {
        Router::put('home', HomeController::class);

        $request = new ServerRequest([
            'method' => 'put',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home'
                ]
            ]
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

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

    public function testPutAction(): void
    {
        Router::put('home/alternate', [HomeController::class, 'altMethod']);

        $request = new ServerRequest([
            'method' => 'put',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home/alternate'
                ]
            ]
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

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

    public function testPutArguments(): void
    {
        Router::put('home/alternate/(.*)/(.*)/(.*)', [HomeController::class, 'altMethod']);

        $request = new ServerRequest([
            'method' => 'put',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home/alternate/test/a/2'
                ]
            ]
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

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
                '2'
            ],
            $route->getArguments()
        );
    }

    public function testPutClosure(): void
    {
        $callback = function() {};

        Router::put('test', $callback);

        $request = new ServerRequest([
            'method' => 'put',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test'
                ]
            ]
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ClosureRoute::class,
            $route
        );

        $this->assertSame(
            $callback,
            $route->getDestination()
        );
    }

    public function testPutClosureArguments(): void
    {
        $callback = function() {};

        Router::put('test/(.*)/(.*)', $callback);

        $request = new ServerRequest([
            'method' => 'put',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/a/2'
                ]
            ]
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

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
                '2'
            ],
            $route->getArguments()
        );
    }

}
