<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\Middleware\MiddlewareQueue;
use Fyre\Middleware\RequestHandler;
use Fyre\Router\Middleware\RouterMiddleware;
use Fyre\Router\Router;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Controller\HomeController;

final class RouterMiddlewareTest extends TestCase
{
    protected Container $container;

    protected Router $router;

    public function testProcessClosureRoute(): void
    {
        $ran = false;

        $function = function() use (&$ran): string {
            $ran = true;

            return '';
        };

        $this->router->connect('test', $function);

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

    public function testProcessControllerRoute(): void
    {
        $this->router->connect('test', HomeController::class);

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
    }

    public function testProcessRedirectRoute(): void
    {
        $this->router->redirect('test', 'https://test.com/');

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
        $this->container = new Container();
        $this->container->singleton(Config::class);
        $this->container->singleton(Router::class);

        $this->router = $this->container->use(Router::class);
    }
}
