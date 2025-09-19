<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Container\Container;
use Fyre\Router\Route;
use Fyre\Router\Routes\RedirectRoute;
use PHPUnit\Framework\TestCase;

final class RedirectRouteTest extends TestCase
{
    protected Container $container;

    public function testGetDestination(): void
    {
        $route = $this->container->build(RedirectRoute::class, [
            'destination' => 'https://test.com/',
        ]);

        $this->assertSame(
            'https://test.com/',
            $route->getDestination()
        );
    }

    public function testRoute(): void
    {
        $route = $this->container->build(RedirectRoute::class, [
            'destination' => 'https://test.com/',
        ]);

        $this->assertInstanceOf(
            Route::class,
            $route
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $route = $this->container->build(RedirectRoute::class, [
            'destination' => 'https://test.com/{a}/{b}',
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
