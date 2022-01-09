<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\RedirectRoute,
    Fyre\Server\ServerRequest;

trait RedirectTest
{

    public function testRedirect(): void
    {
        Router::redirect('test', 'https://test.com/');

        $request = new ServerRequest;
        $request->getUri()->setPath('test');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            RedirectRoute::class,
            $route
        );

        $this->assertSame(
            'https://test.com/',
            $route->getDestination()
        );
    }

    public function testRedirectArguments(): void
    {
        Router::redirect('test/(.*)/(.*)', 'https://test.com/$1/$2');

        $request = new ServerRequest;
        $request->getUri()->setPath('test/a/2');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            RedirectRoute::class,
            $route
        );

        $this->assertSame(
            'https://test.com/a/2',
            $route->getDestination()
        );
    }

}
