<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Router\Route;
use Fyre\Router\Routes\RedirectRoute;
use PHPUnit\Framework\TestCase;

final class RedirectRouteTest extends TestCase
{
    public function testGetDestination(): void
    {
        $route = new RedirectRoute('https://test.com/');

        $this->assertSame(
            'https://test.com/',
            $route->getDestination()
        );
    }

    public function testRoute(): void
    {
        $this->assertInstanceOf(
            Route::class,
            new RedirectRoute('')
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $route1 = new RedirectRoute('https://test.com/$1/$2', 'test/(.*)/(.*)');
        $route2 = $route1->setArgumentsFromPath('test/a/1');

        $this->assertSame(
            [],
            $route1->getArguments()
        );

        $this->assertSame(
            [
                'a',
                '1',
            ],
            $route2->getArguments()
        );
    }
}
