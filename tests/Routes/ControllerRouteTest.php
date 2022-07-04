<?php
declare(strict_types=1);

namespace Tests\Routes;

use
    Fyre\Router\Route,
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    PHPUnit\Framework\TestCase;

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
        $route = new ControllerRoute('Controller::test/$1');

        $this->assertSame(
            '\Tests\Mock\Controller\ControllerController',
            $route->getController()
        );
    }

    public function testGetDestination(): void
    {
        $route = new ControllerRoute('Controller::test/$1');

        $this->assertSame(
            '\Tests\Mock\Controller\Controller::test/$1',
            $route->getDestination()
        );
    }

    public function testGetAction(): void
    {
        $route = new ControllerRoute('Controller::test/$1');

        $this->assertSame(
            'test',
            $route->getAction()
        );
    }

    public function testSetArgumentsFromPath(): void
    {
        $function = function() { };

        $route = new ControllerRoute('Controller::test/$1/$2', 'test/(.*)/(.*)');

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

    protected function setUp(): void
    {
        Router::setDefaultNamespace('Tests\Mock\Controller');
    }

}
