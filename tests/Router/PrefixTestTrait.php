<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;

trait PrefixTestTrait
{
    public function testPrefix(): void
    {
        Router::group(['prefix' => 'prefix'], function() {
            Router::get('home', HomeController::class);
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/home',
                ],
            ],
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }

    public function testPrefixDeep(): void
    {
        Router::group(['prefix' => 'prefix'], function() {
            Router::group(['prefix' => 'deep'], function() {
                Router::get('home', HomeController::class);
            });
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/deep/home',
                ],
            ],
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }

    public function testPrefixEmptyRoute(): void
    {
        Router::group(['prefix' => 'prefix'], function() {
            Router::get('', HomeController::class);
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix',
                ],
            ],
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }

    public function testPrefixLeadingSlash(): void
    {
        Router::group(['prefix' => '/prefix'], function() {
            Router::get('home', HomeController::class);
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/home',
                ],
            ],
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }

    public function testPrefixTrailingSlash(): void
    {
        Router::group(['prefix' => 'prefix/'], function() {
            Router::get('home', HomeController::class);
        });

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/prefix/home',
                ],
            ],
        ]);

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );
    }
}
