<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\RedirectRoute;
use Fyre\Server\ServerRequest;

trait RedirectTestTrait
{

    public function testRedirect(): void
    {
        Router::redirect('test', 'https://test.com/');

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

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/a/2'
                ]
            ]
        ]);

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
