<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;

trait BaseUriTestTrait
{

    public function testRouteBaseUri()
    {
        Router::setBaseUri('https://test.com/deep/');
        Router::get('test', 'Test');

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
            '\Tests\Mock\Controller\TestController',
            $route->getController()
        );
    }

}

