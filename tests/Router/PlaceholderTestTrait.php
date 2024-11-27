<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;

trait PlaceholderTestTrait
{
    public function testPlaceholders(): void
    {
        $router = $this->container->use(Router::class);

        $router->get('home/alternate/{a}/{b}/{c}', [HomeController::class, 'altMethod'], [
            'placeholders' => [
                'a' => '[^/]+',
                'b' => '[a-z]+',
                'c' => '\d+',
            ],
        ]);

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/home/alternate/test/a/2',
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

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );

        $this->assertSame(
            [
                'a' => 'test',
                'b' => 'a',
                'c' => '2',
            ],
            $route->getArguments()
        );
    }
}
