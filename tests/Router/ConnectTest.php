<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait ConnectTest
{

    public function testConnectLeadingSlash(): void
    {
        Router::connect('/home', 'Home');

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
    }

    public function testConnectTrailingSlash(): void
    {
        Router::connect('home/', 'Home');

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
    }

}
