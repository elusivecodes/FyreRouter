<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;
use Tests\Mock\Controller\TestController;

trait FindRouteTestTrait
{

    public function testRouteOrder(): void
    {
        Router::get('(.*)', HomeController::class);
        Router::get('test', TestController::class);

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
            HomeController::class,
            $route->getController()
        );
    }

    public function testInvalidAction(): void
    {
        $this->expectException(RouterException::class);

        Router::get('test', TestController::class);

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
