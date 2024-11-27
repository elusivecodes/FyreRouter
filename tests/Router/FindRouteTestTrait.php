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
    public function testInvalidAction(): void
    {
        $this->expectException(RouterException::class);

        $router = $this->container->use(Router::class);

        $router->get('test', TestController::class);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'method' => 'post',
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test',
                    ],
                ],
            ],
        ]);

        $router->loadRoute($request);
    }

    public function testInvalidRoute(): void
    {
        $this->expectException(RouterException::class);

        $router = $this->container->use(Router::class);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test',
                    ],
                ],
            ],
        ]);

        $router->loadRoute($request);
    }

    public function testRouteOrder(): void
    {
        $router = $this->container->use(Router::class);

        $router->get('{a}', HomeController::class);
        $router->get('test', TestController::class);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

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
