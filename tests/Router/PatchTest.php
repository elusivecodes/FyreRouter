<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ClosureRoute,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait PatchTest
{

    public function testPatch(): void
    {
        Router::patch('home', 'Home');

        $request = new ServerRequest;
        $request->getUri()->setPath('home');
        $request->setMethod('patch');

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

    public function testPatchAction(): void
    {
        Router::patch('home/alternate', 'Home::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('home/alternate');
        $request->setMethod('patch');

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

    public function testPatchDeep(): void
    {
        Router::patch('example', 'Deep\Example');

        $request = new ServerRequest;
        $request->getUri()->setPath('example');
        $request->setMethod('patch');

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

    public function testPatchDeepAction(): void
    {
        Router::patch('example/alternate', 'Deep\Example::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate');
        $request->setMethod('patch');

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

    public function testPatchArguments(): void
    {
        Router::patch('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate/test/a/2');
        $request->setMethod('patch');

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

    public function testPatchClosure(): void
    {
        $callback = function() {};

        Router::patch('test', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');
        $request->setMethod('patch');

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

    public function testPatchClosureArguments(): void
    {
        $callback = function() {};

        Router::patch('test/(.*)/(.*)', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test/a/2');
        $request->setMethod('patch');

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
