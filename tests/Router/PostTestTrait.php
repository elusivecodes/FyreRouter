<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ClosureRoute;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use Tests\Mock\Controller\HomeController;

trait PostTestTrait
{
    public function testPost(): void
    {
        Router::post('home', HomeController::class);

        $request = new ServerRequest([
            'method' => 'post',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );

        $this->assertSame(
            'index',
            $route->getAction()
        );
    }

    public function testPostAction(): void
    {
        Router::post('home/alternate', [HomeController::class, 'altMethod']);

        $request = new ServerRequest([
            'method' => 'post',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home/alternate',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testPostArguments(): void
    {
        Router::post('home/alternate/(.*)/(.*)/(.*)', [HomeController::class, 'altMethod']);

        $request = new ServerRequest([
            'method' => 'post',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/home/alternate/test/a/2',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            HomeController::class,
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );

        $this->assertSame(
            [
                'test',
                'a',
                '2',
            ],
            $route->getArguments()
        );
    }

    public function testPostClosure(): void
    {
        $callback = function() {};

        Router::post('test', $callback);

        $request = new ServerRequest([
            'method' => 'post',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ClosureRoute::class,
            $route
        );

        $this->assertSame(
            $callback,
            $route->getDestination()
        );
    }

    public function testPostClosureArguments(): void
    {
        $callback = function() {};

        Router::post('test/(.*)/(.*)', $callback);

        $request = new ServerRequest([
            'method' => 'post',
            'globals' => [
                'server' => [
                    'REQUEST_URI' => '/test/a/2',
                ],
            ],
        ]);

        $route = Router::loadRoute($request)->getParam('route');

        $this->assertInstanceOf(
            ClosureRoute::class,
            $route
        );

        $this->assertSame(
            $callback,
            $route->getDestination()
        );

        $this->assertSame(
            [
                'a',
                '2',
            ],
            $route->getArguments()
        );
    }
}
