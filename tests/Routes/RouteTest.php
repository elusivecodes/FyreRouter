<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Router\Routes\ControllerRoute;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Controller\TestController;

final class RouteTest extends TestCase
{
    public function testCheckMethod(): void
    {
        $route = (new ControllerRoute([TestController::class]))->setMethods(['get']);

        $this->assertTrue(
            $route->checkMethod('get')
        );
    }

    public function testCheckMethodInvalid(): void
    {
        $route = (new ControllerRoute([TestController::class]))->setMethods(['get']);

        $this->assertFalse(
            $route->checkMethod('post')
        );
    }

    public function testCheckMethodNoMethods(): void
    {
        $route = new ControllerRoute([TestController::class]);

        $this->assertTrue(
            $route->checkMethod('get')
        );
    }

    public function testCheckPath(): void
    {
        $route = new ControllerRoute([TestController::class], 'test/(.*)');

        $this->assertTrue(
            $route->checkPath('test/a')
        );
    }

    public function testCheckPathInvalid(): void
    {
        $route = new ControllerRoute([TestController::class], 'test/(.*)');

        $this->assertFalse(
            $route->checkPath('invalid')
        );
    }

    public function testGetPath(): void
    {
        $route = new ControllerRoute([TestController::class], 'test/(.*)');

        $this->assertSame(
            'test/(.*)',
            $route->getPath()
        );
    }
}
