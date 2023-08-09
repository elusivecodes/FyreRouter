<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait GroupTestTrait
{

    public function testGroup(): void
    {
        Router::group('prefix', function() {
            Router::get('home', 'Home');
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/home'
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

    public function testGroupDeep(): void
    {
        Router::group('prefix', function() {
            Router::group('deep', function() {
                Router::get('home', 'Home');
            });
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/deep/home'
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

    public function testGroupLeadingSlash(): void
    {
        Router::group('/prefix', function() {
            Router::get('home', 'Home');
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/home'
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

    public function testGroupTrailingSlash(): void
    {
        Router::group('prefix/', function() {
            Router::get('home', 'Home');
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/home'
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
