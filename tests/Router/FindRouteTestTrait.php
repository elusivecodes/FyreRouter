<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait FindRouteTestTrait
{

    public function testRouteOrder()
    {
        Router::get('(.*)', 'Home');
        Router::get('test', 'Test');

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test'
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

    public function testInvalidAction(): void
    {
        $this->expectException(RouterException::class);

        Router::get('test', 'Test');

        $request = new ServerRequest([
            'method' => 'post',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test'
                ]
            ]
        ]);

        Router::loadRoute($request);
    }

    public function testInvalidRoute(): void
    {
        $this->expectException(RouterException::class);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test'
                ]
            ]
        ]);

        Router::loadRoute($request);
    }

}
