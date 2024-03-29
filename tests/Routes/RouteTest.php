<?php
declare(strict_types=1);

namespace Tests\Routes;

use Fyre\Router\Routes\ControllerRoute;
use PHPUnit\Framework\TestCase;

final class RouteTest extends TestCase
{

    public function testCheckMethod(): void
    {
        $route = new ControllerRoute('', '', ['get']);

        $this->assertTrue(
            $route->checkMethod('get')
        );
    }

    public function testCheckMethodInvalid(): void
    {
        $route = new ControllerRoute('', '', ['get']);

        $this->assertFalse(
            $route->checkMethod('post')
        );
    }

    public function testCheckMethodNoMethods(): void
    {
        $route = new ControllerRoute('');

        $this->assertTrue(
            $route->checkMethod('get')
        );
    }

    public function testCheckPath(): void
    {
        $route = new ControllerRoute('', 'test/(.*)');

        $this->assertTrue(
            $route->checkPath('test/a')
        );
    }

    public function testCheckPathInvalid(): void
    {
        $route = new ControllerRoute('', 'test/(.*)');

        $this->assertFalse(
            $route->checkPath('invalid')
        );
    }

    public function testGetPath(): void
    {
        $route = new ControllerRoute('', 'test/(.*)');

        $this->assertSame(
            'test/(.*)',
            $route->getPath()
        );
    }

    public function testSetArguments(): void
    {
        $route1 = new ControllerRoute('');
        $route2 = $route1->setArguments(['a', '2']);

        $this->assertEmpty(
            $route1->getArguments()
        );

        $this->assertSame(
            [
                'a',
                '2'
            ],
            $route2->getArguments()
        );
    }

}
