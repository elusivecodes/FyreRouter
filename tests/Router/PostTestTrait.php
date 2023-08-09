<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ClosureRoute;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait PostTestTrait
{

    public function testPost(): void
    {
        Router::post('home', 'Home');

        $request = new ServerRequest([
            'method' => 'post',
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

    public function testPostAction(): void
    {
        Router::post('home/alternate', 'Home::altMethod');

        $request = new ServerRequest([
            'method' => 'post',
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

    public function testPostDeep(): void
    {
        Router::post('example', 'Deep\Example');

        $request = new ServerRequest([
            'method' => 'post',
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

    public function testPostDeepAction(): void
    {
        Router::post('example/alternate', 'Deep\Example::altMethod');

        $request = new ServerRequest([
            'method' => 'post',
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

    public function testPostArguments(): void
    {
        Router::post('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $request = new ServerRequest([
            'method' => 'post',
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

    public function testPostClosure(): void
    {
        $callback = function() {};

        Router::post('test', $callback);

        $request = new ServerRequest([
            'method' => 'post',
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

    public function testPostClosureArguments(): void
    {
        $callback = function() {};

        Router::post('test/(.*)/(.*)', $callback);

        $request = new ServerRequest([
            'method' => 'post',
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
