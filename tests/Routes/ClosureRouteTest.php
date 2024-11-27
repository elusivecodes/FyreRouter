<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Container\Container;
use Fyre\Router\Route;
use Fyre\Router\Routes\ClosureRoute;
use PHPUnit\Framework\TestCase;

final class ClosureRouteTest extends TestCase
{
    protected Container $container;

    public function testGetDestination(): void
    {
        $function = function() {};

        $route = $this->container->build(ClosureRoute::class, ['destination' => $function]);

        $this->assertInstanceOf(
            Route::class,
            $route
        );

        $this->assertSame(
            $function,
            $route->getDestination()
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $route = $this->container->build(ClosureRoute::class, [
            'destination' => function(): void {},
            'path' => 'test/{a}/{b}',
        ]);

        $route->checkPath('test/a/1');

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
