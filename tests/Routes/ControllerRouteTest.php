<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Container\Container;
use Fyre\Router\Route;
use Fyre\Router\Routes\ControllerRoute;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Controller\TestController;

final class ControllerRouteTest extends TestCase
{
    protected Container $container;

    public function testGetAction(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
        ]);

        $this->assertSame(
            'test',
            $route->getAction()
        );
    }

    public function testGetController(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
        ]);

        $this->assertSame(
            TestController::class,
            $route->getController()
        );
    }

    public function testGetDestination(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
        ]);

        $this->assertSame(
            [TestController::class, 'test'],
            $route->getDestination()
        );
    }

    public function testRoute(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
        ]);

        $this->assertInstanceOf(
            Route::class,
            $route
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
            'path' => 'test/{a}/{b}',
        ]);

        $route->checkRoute(path: 'test/a/1');

        $this->assertSame(
            [
                'a' => 'a',
                'b' => '1',
            ],
            $route->getArguments()
        );
    }

    protected function setUp(): void
    {
        $this->container = new Container();
    }
}
