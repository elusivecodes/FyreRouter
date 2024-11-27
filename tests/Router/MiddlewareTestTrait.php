<?php
declare(strict_types=1);

namespace Tests\Router;

use Closure;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\MiddlewareRegistry;
use Fyre\Middleware\RequestHandler;
use Fyre\Router\Middleware\RouterMiddleware;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;
use Tests\Mock\Middleware\ArgsMiddleware;

trait MiddlewareTestTrait
{
    public function testGroupMiddleware(): void
    {
        $results = [];

        $router = $this->container->use(Router::class);

        $router->group([
            'middleware' => [
                function(ServerRequest $request, Closure $next) use (&$results): ClientResponse {
                    $results[] = 'test1';

                    return $next($request);
                },
            ],
        ], function(Router $router) use (&$results): void {
            $router->connect('test', HomeController::class, [
                'middleware' => [
                    function(ServerRequest $request, Closure $next) use (&$results): ClientResponse {
                        $results[] = 'test2';

                        return $next($request);
                    },
                ],
            ]);
        });

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test',
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );

        $this->assertSame(
            [
                'test1',
                'test2',
            ],
            $results
        );
    }

    public function testGroupMiddlewareDeep(): void
    {
        $results = [];

        $router = $this->container->use(Router::class);

        $router->group([
            'middleware' => [
                function(ServerRequest $request, Closure $next) use (&$results): ClientResponse {
                    $results[] = 'test1';

                    return $next($request);
                },
            ],
        ], function(Router $router) use (&$results): void {
            $router->group([
                'middleware' => [
                    function(ServerRequest $request, Closure $next) use (&$results): ClientResponse {
                        $results[] = 'test2';

                        return $next($request);
                    },
                ],
            ], function(Router $router) use (&$results): void {
                $router->connect('test', HomeController::class, [
                    'middleware' => [
                        function(ServerRequest $request, Closure $next) use (&$results): ClientResponse {
                            $results[] = 'test3';

                            return $next($request);
                        },
                    ],
                ]);
            });
        });

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test',
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );

        $this->assertSame(
            [
                'test1',
                'test2',
                'test3',
            ],
            $results
        );
    }

    public function testMiddleware(): void
    {
        $ran = false;

        $router = $this->container->use(Router::class);

        $router->connect('test', HomeController::class, [
            'middleware' => function(ServerRequest $request, Closure $next) use (&$ran): ClientResponse {
                $ran = true;

                return $next($request);
            },
        ]);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test',
                    ],
                ],
            ],
        ]);

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );

        $this->assertTrue($ran);
    }

    public function testMiddlewareArgs(): void
    {
        $middlewareRegistry = $this->container->use(MiddlewareRegistry::class);

        $middlewareRegistry->map('test', ArgsMiddleware::class);

        $router = $this->container->use(Router::class);

        $router->connect('test/{a}', HomeController::class, [
            'middleware' => [
                'test:{a},1',
            ],
        ]);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = $this->container->build(RequestHandler::class, ['queue' => $queue]);
        $request = $this->container->build(ServerRequest::class, [
            'options' => [
                'globals' => [
                    'server' => [
                        'REQUEST_URI' => '/test/2',
                    ],
                ],
            ],
        ]);

        $response = $handler->handle($request);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );

        $this->assertSame(
            '[
    "2",
    "1"
]',
            $response->getBody()
        );
    }
}
