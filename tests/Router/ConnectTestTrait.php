<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;

trait ConnectTestTrait
{
    public function testConnectLeadingSlash(): void
    {
        $router = $this->container->use(Router::class);

        $router->connect('/home', HomeController::class);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/home',
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

    public function testConnectTrailingSlash(): void
    {
        $router = $this->container->use(Router::class);

        $router->connect('home/', HomeController::class);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/home',
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
