<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\Router\Middleware\RouterMiddleware;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;

final class RouterMiddlewareTest extends TestCase
{

    public function testProcessClosureRoute(): void
    {
        $ran = false;

        $function = function(ServerRequest $request, ClientResponse $response) use (&$ran) {
            $ran = true;

            return $response;
        };

        Router::connect('test', $function);

        $queue = new MiddlewareQueue();
        $queue->add(new RouterMiddleware());

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test'
                ]
            ]
        ]);

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );

        $this->assertTrue($ran);
    }

    public function testProcessControllerRoute(): void
    {
        Router::connect('test', 'Home');

        $queue = new MiddlewareQueue();
        $queue->add(new RouterMiddleware());

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test'
                ]
            ]
        ]);

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );
    }

    public function testProcessRedirectRoute(): void
    {
        Router::redirect('test', 'https://test.com/');

        $queue = new MiddlewareQueue();
        $queue->add(new RouterMiddleware());

        $handler = new RequestHandler($queue);

        $request = new ServerRequest([
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test'
                ]
            ]
        ]);

        $response = $handler->handle($request);

        $this->assertInstanceOf(
            ClientResponse::class,
            $response
        );

        $this->assertSame(
            302,
            $response->getStatusCode()
        );

        $this->assertSame(
            'https://test.com/',
            $response->getHeaderValue('Location')
        );
    }

    protected function setUp(): void
    {
        Router::clear();
    }

}
