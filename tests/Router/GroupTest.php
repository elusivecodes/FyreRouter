<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait GroupTest
{

    public function testGroup(): void
    {
        Router::group('prefix', function() {
            Router::get('home', 'Home');
        });

        $request = new ServerRequest;
        $request->getUri()->setPath('prefix/home');

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
    }

    public function testGroupDeep(): void
    {
        Router::group('prefix', function() {
            Router::group('deep', function() {
                Router::get('home', 'Home');
            });
        });

        $request = new ServerRequest;
        $request->getUri()->setPath('prefix/deep/home');

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
    }

    public function testGroupLeadingSlash(): void
    {
        Router::group('/prefix', function() {
            Router::get('home', 'Home');
        });

        $request = new ServerRequest;
        $request->getUri()->setPath('prefix/home');

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
    }

    public function testGroupTrailingSlash(): void
    {
        Router::group('prefix/', function() {
            Router::get('home', 'Home');
        });

        $request = new ServerRequest;
        $request->getUri()->setPath('prefix/home');

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
    }

}
