<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Server\ServerRequest;

trait ServerRequestTest
{

    public function testLoadRouteRequest(): void
    {
        Router::get('(.*)', 'Home');

        $request = new ServerRequest;

        Router::loadRoute($request);

        $this->assertSame(
            $request,
            Router::getRequest()
        );
    }

    public function testSetRequest(): void
    {
        $request = new ServerRequest;

        Router::setRequest($request);

        $this->assertSame(
            $request,
            Router::getRequest()
        );
    }

}
