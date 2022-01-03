<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ClosureRoute,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait PostTest
{

    public function testPost(): void
    {
        Router::post('home', 'Home');

        $request = new ServerRequest;
        $request->getUri()->setPath('home');
        $request->setMethod('post');

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

    public function testPostAction(): void
    {
        Router::post('home/alternate', 'Home::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('home/alternate');
        $request->setMethod('post');

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

    public function testPostDeep(): void
    {
        Router::post('example', 'Deep\Example');

        $request = new ServerRequest;
        $request->getUri()->setPath('example');
        $request->setMethod('post');

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

    public function testPostDeepAction(): void
    {
        Router::post('example/alternate', 'Deep\Example::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate');
        $request->setMethod('post');

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

    public function testPostArguments(): void
    {
        Router::post('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $request = new ServerRequest;
        $request->getUri()->setPath('example/alternate/test/a/2');
        $request->setMethod('post');

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

    public function testPostClosure(): void
    {
        $callback = function() {};

        Router::post('test', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');
        $request->setMethod('post');

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

    public function testPostClosureArguments(): void
    {
        $callback = function() {};

        Router::post('test/(.*)/(.*)', $callback);

        $request = new ServerRequest;
        $request->getUri()->setPath('test/a/2');
        $request->setMethod('post');

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
