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
        $router = $this->container->use(Router::class);

        $router->redirect('test', 'https://test.com/');

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

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
        $router = $this->container->use(Router::class);

        $router->redirect('test/{a}/{b}', 'https://test.com/{a}/{b}');

        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/a/2',
                    ],
                ],
            ],
        ]);

        $route = $router->loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            RedirectRoute::class,
            $route
        );

        $response = $route->handle($request, new ClientResponse());

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
