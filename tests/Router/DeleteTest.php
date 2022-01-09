<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ClosureRoute,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait DeleteTest
{

    public function testDelete(): void
    {
        Router::delete('home', 'Home');

        $request = new ServerRequest;
        $request->getUri()->setPath('home');
        $request->setMethod('delete');

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

    public function testDeleteAction(): void
    {
        Router::delete('home/alternate', 'Home::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('home/alternate');
        $request->setMethod('delete');

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

    public function testDeleteDeep(): void
    {
        Router::delete('example', 'Deep\Example');

        $request = new ServerRequest;
        $request->getUri()->setPath('example');
        $request->setMethod('delete');

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

    public function testDeleteDeepAction(): void
    {
        Router::delete('example/alternate', 'Deep\Example::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate');
        $request->setMethod('delete');

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

    public function testDeleteArguments(): void
    {
        Router::delete('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate/test/a/2');
        $request->setMethod('delete');

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

    public function testDeleteClosure(): void
    {
        $callback = function() {};

        Router::delete('test', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');
        $request->setMethod('delete');

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

    public function testDeleteClosureArguments(): void
    {
        $callback = function() {};

        Router::delete('test/(.*)/(.*)', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test/a/2');
        $request->setMethod('delete');

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
