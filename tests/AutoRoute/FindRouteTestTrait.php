<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait FindRouteTestTrait
{

    public function testFindRoute(): void
    {
        $request = new ServerRequest([
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

    public function testFindRouteAction(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home/alt-method'
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

    public function testFindRouteDeep(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/deep/example'
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
            'index',
            $route->getAction()
        );
    }

    public function testFindRouteDeepAction(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/deep/example/alt-method'
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

    public function testFindRouteArguments(): void
    {
        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/deep/example/alt-method/test/a/2'
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
                'a',
                '2'
            ],
            $route->getArguments()
        );
    }

    public function testFindRouteDefaultNamespace(): void
    {
        Router::clear();
        Router::setDefaultNamespace('Tests\Mock\Controller');

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/deep/example/alt-method'
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

    public function testFindRouteInvalid(): void
    {
        $this->expectException(RouterException::class);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/invalid'
                ]
            ]
        ]);

        Router::loadRoute($request);
    }

}
