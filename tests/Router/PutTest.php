<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ClosureRoute,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait PutTest
{

    public function testPut(): void
    {
        Router::put('home', 'Home');

        $request = new ServerRequest;
        $request->getUri()->setPath('home');
        $request->setMethod('put');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertEquals(
            '\Tests\Controller\Home',
            $route->getController()
        );

        $this->assertEquals(
            'index',
            $route->getAction()
        );
    }

    public function testPutAction(): void
    {
        Router::put('home/alternate', 'Home::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('home/alternate');
        $request->setMethod('put');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertEquals(
            '\Tests\Controller\Home',
            $route->getController()
        );

        $this->assertEquals(
            'altMethod',
            $route->getAction()
        );
    }

    public function testPutDeep(): void
    {
        Router::put('example', 'Deep\Example');

        $request = new ServerRequest;
        $request->getUri()->setPath('example');
        $request->setMethod('put');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertEquals(
            '\Tests\Controller\Deep\Example',
            $route->getController()
        );
    }

    public function testPutDeepAction(): void
    {
        Router::put('example/alternate', 'Deep\Example::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate');
        $request->setMethod('put');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertEquals(
            '\Tests\Controller\Deep\Example',
            $route->getController()
        );

        $this->assertEquals(
            'altMethod',
            $route->getAction()
        );
    }

    public function testPutArguments(): void
    {
        Router::put('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate/test/a/2');
        $request->setMethod('put');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertEquals(
            '\Tests\Controller\Deep\Example',
            $route->getController()
        );

        $this->assertEquals(
            'altMethod',
            $route->getAction()
        );

        $this->assertEquals(
            [
                'test',
                '2'
            ],
            $route->getArguments()
        );
    }

    public function testPutClosure(): void
    {
        $callback = function() {};

        Router::put('test', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');
        $request->setMethod('put');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ClosureRoute::class,
            $route
        );

        $this->assertEquals(
            $callback,
            $route->getDestination()
        );
    }

    public function testPutClosureArguments(): void
    {
        $callback = function() {};

        Router::put('test/(.*)/(.*)', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test/a/2');
        $request->setMethod('put');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ClosureRoute::class,
            $route
        );

        $this->assertEquals(
            $callback,
            $route->getDestination()
        );

        $this->assertEquals(
            [
                'a',
                '2'
            ],
            $route->getArguments()
        );
    }

}
