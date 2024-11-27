<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Config\Config;
use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\TestController;

trait BaseUriTestTrait
{
    public function testRouteBaseUri(): void
    {
        $this->container->use(Config::class)->set('App.baseUri', 'https://test.com/deep/');

        $router = $this->container->build(Router::class);
        $router->get('test', TestController::class);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/deep/test',
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
            TestController::class,
            $route->getController()
        );
    }
}
