<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Middleware\ClosureMiddleware;
use Fyre\Middleware\Middleware;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\MiddlewareRegistry;
use Fyre\Middleware\RequestHandler;
use Fyre\Router\Middleware\RouterMiddleware;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;

trait MiddlewareTestTrait
{
    public function testGroupMiddleware(): void
    {
        $results = [];

        Router::group([
            'middleware' => [
                function(ServerRequest $request, RequestHandler $handler) use (&$results): ClientResponse {
                    $results[] = 'test1';

                    return $handler->handle($request);
                },
            ],
        ], function() use (&$results): void {
            Router::connect('test', HomeController::class, [
                'middleware' => [
                    function(ServerRequest $request, RequestHandler $handler) use (&$results): ClientResponse {
                        $results[] = 'test2';

                        return $handler->handle($request);
                    },
                ],
            ]);
        });

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test',
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

        Router::group([
            'middleware' => [
                function(ServerRequest $request, RequestHandler $handler) use (&$results): ClientResponse {
                    $results[] = 'test1';

                    return $handler->handle($request);
                },
            ],
        ], function() use (&$results): void {
            Router::group([
                'middleware' => [
                    function(ServerRequest $request, RequestHandler $handler) use (&$results): ClientResponse {
                        $results[] = 'test2';

                        return $handler->handle($request);
                    },
                ],
            ], function() use (&$results): void {
                Router::connect('test', HomeController::class, [
                    'middleware' => [
                        function(ServerRequest $request, RequestHandler $handler) use (&$results): ClientResponse {
                            $results[] = 'test3';

                            return $handler->handle($request);
                        },
                    ],
                ]);
            });
        });

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test',
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

        Router::connect('test', HomeController::class, [
            'middleware' => function(ServerRequest $request, RequestHandler $handler) use (&$ran) {
                $ran = true;

                return $handler->handle($request);
            },
        ]);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test',
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
        $results = null;

        MiddlewareRegistry::map('test', function() use (&$results): Middleware {
            return new ClosureMiddleware(function(ServerRequest $request, RequestHandler $handler, string ...$args) use (&$results) {
                $results = $args;

                return $handler->handle($request);
            });
        });

        Router::connect('test/(:segment)', HomeController::class, [
            'middleware' => [
                'test:{1},1',
            ],
        ]);

        $queue = new MiddlewareQueue([
            RouterMiddleware::class,
        ]);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/2',
                ],
            ],
        ]);

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );

        $this->assertSame(
            ['2', '1'],
            $results
        );
    }
}
