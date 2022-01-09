<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait DefaultNamespaceTest
{

    public function testDefaultNamespaceNotRetroactive(): void
    {
        Router::get('home', 'Home');
        Router::setDefaultNamespace('Invalid');

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

    public function testDefaultNamespaceLeadingSlash(): void
    {
        Router::setDefaultNamespace('\Tests\Controller');
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
    }

    public function testDefaultNamespaceTrailingSlash(): void
    {
        Router::setDefaultNamespace('Tests\Controller\\');
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
    }

}
