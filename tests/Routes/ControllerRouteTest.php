<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Router\Route;
use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use PHPUnit\Framework\TestCase;

final class ControllerRouteTest extends TestCase
{

    public function testRoute(): void
    {
        $this->assertInstanceOf(
            Route::class,
            new ControllerRoute('')
        );
    }

    public function testGetController(): void
    {
        $route = new ControllerRoute('Test::test/$1');

        $this->assertSame(
            '\Tests\Mock\Controller\TestController',
            $route->getController()
        );
    }

    public function testGetDestination(): void
    {
        $route = new ControllerRoute('Test::test/$1');

        $this->assertSame(
            '\Tests\Mock\Controller\Test::test/$1',
            $route->getDestination()
        );
    }

    public function testGetAction(): void
    {
        $route = new ControllerRoute('Test::test/$1');

        $this->assertSame(
            'test',
            $route->getAction()
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $route1 = new ControllerRoute('Test::test/$1/$2', 'test/(.*)/(.*)');
        $route2 = $route1->setArgumentsFromPath('test/a/1');

        $this->assertSame(
            [
                '$1',
                '$2'
            ],
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

    protected function setUp(): void
    {
        Router::setDefaultNamespace('Tests\Mock\Controller');
    }

}
