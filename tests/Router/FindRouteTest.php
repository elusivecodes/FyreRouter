<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait FindRouteTest
{

    public function testRouteOrder()
    {
        Router::get('(.*)', 'Home');
        Router::get('test', 'Test');

        $request = new ServerRequest;
        $request->getUri()->setPath('test');

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
    }

    public function testInvalidAction(): void
    {
        $this->expectException(RouterException::class);

        Router::get('test', 'Test');

        $request = new ServerRequest;
        $request->getUri()->setPath('test');
        $request->setMethod('post');

        Router::loadRoute($request);
    }

    public function testInvalidRoute(): void
    {
        $this->expectException(RouterException::class);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');

        Router::loadRoute($request);
    }

}
