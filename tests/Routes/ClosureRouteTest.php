<?php
declare(strict_types=1);

namespace Tests\Routes;

use
    Fyre\Router\Route,
    Fyre\Router\Routes\ClosureRoute,
    PHPUnit\Framework\TestCase;

final class ClosureRouteTest extends TestCase
{

    public function testRoute(): void
    {
        $function = function() { };

        $this->assertInstanceOf(
            Route::class,
            new ClosureRoute($function)
        );
    }

    public function testGetDestination(): void
    {
        $function = function() { };

        $route = new ClosureRoute($function);

        $this->assertSame(
            $function,
            $route->getDestination()
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $function = function() { };

        $route = new ClosureRoute($function, 'test/(.*)/(.*)');

        $this->assertSame(
            $route,
            $route->setArgumentsFromPath('test/a/1')
        );

        $this->assertSame(
            [
                'a',
                '1'
            ],
            $route->getArguments()
        );
    }

}
