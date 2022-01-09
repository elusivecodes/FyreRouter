<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ClosureRoute,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait GetTest
{

    public function testGet(): void
    {
        Router::get('home', 'Home');

        $request = new ServerRequest;
        $request->getUri()->setPath('home');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Home',
            $route->getController()
        );

        $this->assertSame(
            'index',
            $route->getAction()
        );
    }

    public function testGetAction(): void
    {
        Router::get('home/alternate', 'Home::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('home/alternate');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Home',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testGetDeep(): void
    {
        Router::get('example', 'Deep\Example');

        $request = new ServerRequest;
        $request->getUri()->setPath('example');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Deep\Example',
            $route->getController()
        );
    }

    public function testGetDeepAction(): void
    {
        Router::get('example/alternate', 'Deep\Example::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Deep\Example',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testGetArguments(): void
    {
        Router::get('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate/test/a/2');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Deep\Example',
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

    public function testGetClosure(): void
    {
        $callback = function() {};

        Router::get('test', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');

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

    public function testGetClosureArguments(): void
    {
        $callback = function() {};

        Router::get('test/(.*)/(.*)', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test/a/2');

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
