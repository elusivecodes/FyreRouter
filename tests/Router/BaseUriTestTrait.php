<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\TestController;

trait BaseUriTestTrait
{

    public function testRouteBaseUri(): void
    {
        Router::setBaseUri('https://test.com/deep/');
        Router::get('test', TestController::class);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/deep/test'
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
            TestController::class,
            $route->getController()
        );
    }

}

