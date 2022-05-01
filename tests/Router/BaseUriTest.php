<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait BaseUriTest
{

    public function testRouteBaseUri()
    {
        Router::setBaseUri('https://test.com/deep/');
        Router::get('test', 'Test');

        $request = new ServerRequest;
        $request->getUri()->setPath('deep/test');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Test',
            $route->getController()
        );
    }

}

