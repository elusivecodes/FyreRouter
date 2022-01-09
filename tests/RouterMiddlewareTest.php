<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\Middleware\MiddlewareQueue,
    Fyre\Middleware\RequestHandler,
    Fyre\Router\Router,
    Fyre\Router\RouterMiddleware,
    Fyre\Router\Routes\ClosureRoute,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Router\Routes\RedirectRoute,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest,
    PHPUnit\Framework\TestCase;

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

        $queue = new MiddlewareQueue;
        $queue->add(new RouterMiddleware);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );

        $this->assertTrue($ran);
    }

    public function testProcessControllerRoute(): void
    {
        Router::connect('test', 'Home');

        $queue = new MiddlewareQueue;
        $queue->add(new RouterMiddleware);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');

        $this->assertInstanceOf(
            ClientResponse::class,
            $handler->handle($request)
        );
    }

    public function testProcessRedirectRoute(): void
    {
        Router::redirect('test', 'https://test.com/');

        $queue = new MiddlewareQueue;
        $queue->add(new RouterMiddleware);

        $handler = new RequestHandler($queue);

        $request = new ServerRequest;
        $request->getUri()->setPath('test');

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
