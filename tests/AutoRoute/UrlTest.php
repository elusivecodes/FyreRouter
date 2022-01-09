<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Router;

trait UrlTest
{

    public function testUrl(): void
    {
        $this->assertSame(
            '/home',
            Router::url('Tests\Controller\Home')
        );
    }

    public function testUrlAction(): void
    {
        $this->assertSame(
            '/home/alt-method',
            Router::url('Tests\Controller\Home::altMethod')
        );
    }

    public function testUrlDeep(): void
    {
        $this->assertSame(
            '/deep/example',
            Router::url('Tests\Controller\Deep\Example')
        );
    }

    public function testUrlDeepAction(): void
    {
        $this->assertSame(
            '/deep/example/alt-method',
            Router::url('Tests\Controller\Deep\Example::altMethod')
        );
    }

    public function testUrlArguments(): void
    {
        $this->assertSame(
            '/deep/example/alt-method/test/a/2',
            Router::url('Tests\Controller\Deep\Example::altMethod', ['test', 'a', '2'])
        );
    }

    public function testUrlLeadingSlash(): void
    {
        $this->assertSame(
            '/deep/example/alt-method/test/a/2',
            Router::url('\Tests\Controller\Deep\Example::altMethod', ['test', 'a', '2'])
        );
    }

    public function testUrlDefaultNamespace(): void
    {
        Router::clear();
        Router::setDefaultNamespace('Tests\Controller');

        $this->assertSame(
            '/deep/example/alt-method',
            Router::url('\Tests\Controller\Deep\Example::altMethod')
        );
    }

    public function testUrlInvalid(): void
    {
        $this->assertSame(
            '/example/alt-method',
            Router::url('Example::altMethod')
        );
    }

}
