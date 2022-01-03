<?php
declare(strict_types=1);

namespace Tests\Routes;

use
    Fyre\Router\Route,
    Fyre\Router\Routes\RedirectRoute,
    PHPUnit\Framework\TestCase;

final class RedirectRouteTest extends TestCase
{

    public function testRoute(): void
    {
        $this->assertInstanceOf(
            Route::class,
            new RedirectRoute('')
        );
    }

    public function testGetDestination(): void
    {
        $route = new RedirectRoute('https://test.com/');

        $this->assertEquals(
            'https://test.com/',
            $route->getDestination()
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $function = function() { };

        $route = new RedirectRoute('https://test.com/$1/$2', 'test/(.*)/(.*)');

        $this->assertEquals(
            $route,
            $route->setArgumentsFromPath('test/a/1')
        );

        $this->assertEquals(
            'https://test.com/a/1',
            $route->getDestination()
        );
    }

}
