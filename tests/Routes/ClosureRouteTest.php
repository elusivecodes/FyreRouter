<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Router\Route;
use Fyre\Router\Routes\ClosureRoute;
use PHPUnit\Framework\TestCase;

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

        $route1 = new ClosureRoute($function, 'test/(.*)/(.*)');
        $route2 = $route1->setArgumentsFromPath('test/a/1');

        $this->assertEmpty(
            $route1->getArguments()
        );

        $this->assertSame(
            [
                'a',
                '1'
            ],
            $route2->getArguments()
        );
    }

}
