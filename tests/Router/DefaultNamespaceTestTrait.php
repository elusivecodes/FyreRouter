<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait DefaultNamespaceTestTrait
{

    public function testDefaultNamespaceNotRetroactive(): void
    {
        Router::get('home', 'Home');
        Router::setDefaultNamespace('Invalid');

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
    }

    public function testDefaultNamespaceLeadingSlash(): void
    {
        Router::setDefaultNamespace('\Tests\Mock\Controller');
        Router::get('home', 'Home');

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
    }

    public function testDefaultNamespaceTrailingSlash(): void
    {
        Router::setDefaultNamespace('Tests\Mock\Controller\\');
        Router::get('home', 'Home');

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
    }

}
