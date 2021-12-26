<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Router;

trait UrlTest
{

    public function testUrl(): void
    {
        $this->assertEquals(
            '/home',
            Router::url('Tests\Controller\Home')
        );
    }

    public function testUrlMethod(): void
    {
        $this->assertEquals(
            '/home/alt-method',
            Router::url('Tests\Controller\Home::altMethod')
        );
    }

    public function testUrlDeep(): void
    {
        $this->assertEquals(
            '/deep/example',
            Router::url('Tests\Controller\Deep\Example')
        );
    }

    public function testUrlDeepMethod(): void
    {
        $this->assertEquals(
            '/deep/example/alt-method',
            Router::url('Tests\Controller\Deep\Example::altMethod')
        );
    }

    public function testUrlArguments(): void
    {
        $this->assertEquals(
            '/deep/example/alt-method/test/a/2',
            Router::url('Tests\Controller\Deep\Example::altMethod', ['test', 'a', '2'])
        );
    }

    public function testUrlLeadingSlash(): void
    {
        $this->assertEquals(
            '/deep/example/alt-method/test/a/2',
            Router::url('\Tests\Controller\Deep\Example::altMethod', ['test', 'a', '2'])
        );
    }

    public function testUrlDefaultNamespace(): void
    {
        Router::clear();
        Router::setDefaultNamespace('Tests\Controller');

        $this->assertEquals(
            '/deep/example/alt-method',
            Router::url('\Tests\Controller\Deep\Example::altMethod')
        );
    }

    public function testUrlInvalid(): void
    {
        $this->assertEquals(
            '/example/alt-method',
            Router::url('Example::altMethod')
        );
    }

}
