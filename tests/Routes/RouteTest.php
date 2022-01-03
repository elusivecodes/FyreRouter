<?php
declare(strict_types=1);

namespace Tests\Routes;

use
    Fyre\Router\Route,
    Fyre\Router\Routes\ControllerRoute,
    PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{

    public function testCheckMethod(): void
    {
        $route = new ControllerRoute('', '', ['get']);

        $this->assertEquals(
            true,
            $route->checkMethod('get')
        );
    }

    public function testCheckMethodInvalid(): void
    {
        $route = new ControllerRoute('', '', ['get']);

        $this->assertEquals(
            false,
            $route->checkMethod('post')
        );
    }

    public function testCheckMethodNoMethods(): void
    {
        $route = new ControllerRoute('');

        $this->assertEquals(
            true,
            $route->checkMethod('get')
        );
    }

    public function testCheckPath(): void
    {
        $route = new ControllerRoute('', 'test/(.*)');

        $this->assertEquals(
            true,
            $route->checkPath('test/a')
        );
    }

    public function testCheckPathInvalid(): void
    {
        $route = new ControllerRoute('', 'test/(.*)');

        $this->assertEquals(
            false,
            $route->checkPath('invalid')
        );
    }

    public function testGetPath(): void
    {
        $route = new ControllerRoute('', 'test/(.*)');

        $this->assertEquals(
            'test/(.*)',
            $route->getPath()
        );
    }

    public function testSetArguments(): void
    {
        $route = new ControllerRoute('');

        $this->assertEquals(
            $route,
            $route->setArguments(['a', '2'])
        );

        $this->assertEquals(
            [
                'a',
                '2'
            ],
            $route->getArguments()
        );
    }

}
