<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Router\Route;
use Fyre\Router\Routes\RedirectRoute;
use PHPUnit\Framework\TestCase;

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

        $this->assertSame(
            'https://test.com/',
            $route->getDestination()
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $function = function() { };

        $route1 = new RedirectRoute('https://test.com/$1/$2', 'test/(.*)/(.*)');
        $route2 = $route1->setArgumentsFromPath('test/a/1');

        $this->assertSame(
            'https://test.com/$1/$2',
            $route1->getDestination()
        );

        $this->assertSame(
            'https://test.com/a/1',
            $route2->getDestination()
        );
    }

}
