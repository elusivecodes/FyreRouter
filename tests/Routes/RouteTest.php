<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Container\Container;
use Fyre\Router\Routes\ControllerRoute;
use PHPUnit\Framework\TestCase;
use Tests\Mock\Controller\TestController;

final class RouteTest extends TestCase
{
    protected Container $container;

    public function testCheckMethod(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
            'options' => [
                'methods' => ['get'],
            ],
        ]);

        $this->assertTrue(
            $route->checkMethod('get')
        );
    }

    public function testCheckMethodInvalid(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
            'options' => [
                'methods' => ['get'],
            ],
        ]);

        $this->assertFalse(
            $route->checkMethod('post')
        );
    }

    public function testCheckMethodNoMethods(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
        ]);

        $this->assertTrue(
            $route->checkMethod('get')
        );
    }

    public function testCheckPath(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
            'path' => 'test/{a}',
        ]);

        $this->assertTrue(
            $route->checkPath('test/a')
        );
    }

    public function testCheckPathInvalid(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
            'path' => 'test/{a}',
        ]);

        $this->assertFalse(
            $route->checkPath('invalid')
        );
    }

    public function testGetPath(): void
    {
        $route = $this->container->build(ControllerRoute::class, [
            'destination' => [TestController::class, 'test'],
            'path' => 'test/{a}',
        ]);

        $this->assertSame(
            'test/{a}',
            $route->getPath()
        );
    }

    protected function setUp(): void
    {
        $this->container = new Container();
    }
}
