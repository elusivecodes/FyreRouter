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
        Router::patch('home', 'Home');

        $request = new ServerRequest([
            'method' => 'patch',
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
            '\Tests\Mock\Controller\HomeController',
            $route->getController()
        );

        $this->assertSame(
            'index',
            $route->getAction()
        );
    }

    public function testPatchAction(): void
    {
        Router::patch('home/alternate', 'Home::altMethod');

        $request = new ServerRequest([
            'method' => 'patch',
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
            '\Tests\Mock\Controller\HomeController',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testPatchDeep(): void
    {
        Router::patch('example', 'Deep\Example');

        $request = new ServerRequest([
            'method' => 'patch',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/example'
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
            '\Tests\Mock\Controller\Deep\ExampleController',
            $route->getController()
        );
    }

    public function testPatchDeepAction(): void
    {
        Router::patch('example/alternate', 'Deep\Example::altMethod');

        $request = new ServerRequest([
            'method' => 'patch',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/example/alternate'
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
            '\Tests\Mock\Controller\Deep\ExampleController',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testPatchArguments(): void
    {
        Router::patch('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $request = new ServerRequest([
            'method' => 'patch',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/example/alternate/test/a/2'
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
            '\Tests\Mock\Controller\Deep\ExampleController',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );

        $this->assertSame(
            [
                'test',
                '2'
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

    public function testPatchClosureArguments(): void
    {
        $callback = function() {};

        Router::patch('test/(.*)/(.*)', $callback);

        $request = new ServerRequest([
            'method' => 'patch',
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
