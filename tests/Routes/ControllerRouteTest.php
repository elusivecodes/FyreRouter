<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Router\Route;
use Fyre\Router\Routes\ControllerRoute;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Controller\TestController;

final class ControllerRouteTest extends TestCase
{

    public function testRoute(): void
    {
        $this->assertInstanceOf(
            Route::class,
            new ControllerRoute([TestController::class])
        );
    }

    public function testGetController(): void
    {
        $route = new ControllerRoute([TestController::class, 'test']);

        $this->assertSame(
            TestController::class,
            $route->getController()
        );
    }

    public function testGetDestination(): void
    {
        $route = new ControllerRoute([TestController::class, 'test']);

        $this->assertSame(
            [TestController::class, 'test'],
            $route->getDestination()
        );
    }

    public function testGetAction(): void
    {
        $route = new ControllerRoute([TestController::class, 'test']);

        $this->assertSame(
            'test',
            $route->getAction()
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $route1 = new ControllerRoute([TestController::class, 'test'], 'test/(.*)/(.*)');
        $route2 = $route1->setArgumentsFromPath('test/a/1');

        $this->assertSame(
            [],
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
