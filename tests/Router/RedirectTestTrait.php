<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\RedirectRoute;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

trait RedirectTestTrait
{
    public function testRedirect(): void
    {
        Router::redirect('test', 'https://test.com/');

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

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
                    'REQUEST_URI' => '/test/a/2',
                ],
            ],
        ]);
        $route = Router::loadRoute($request)->getParam('route');
        $this->assertInstanceOf(
            RedirectRoute::class,
            $route
        );

        $response = $route->process($request, new ClientResponse());

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );

        $this->assertSame(
            302,
            $response->getStatusCode()
        );

        $this->assertSame(
            'https://test.com/a/2',
            $response->getHeaderValue('Location')
        );
    }
}
