<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Router;

trait BuildFromPathTest
{

    public function testBuildFromPath(): void
    {
        $this->assertSame(
            '/home',
            Router::buildFromPath('Tests\Controller\Home')
        );
    }

    public function testBuildFromPathAction(): void
    {
        $this->assertSame(
            '/home/alt-method',
            Router::buildFromPath('Tests\Controller\Home::altMethod')
        );
    }

    public function testBuildFromPathDeep(): void
    {
        $this->assertSame(
            '/deep/example',
            Router::buildFromPath('Tests\Controller\Deep\Example')
        );
    }

    public function testBuildFromPathDeepAction(): void
    {
        $this->assertSame(
            '/deep/example/alt-method',
            Router::buildFromPath('Tests\Controller\Deep\Example::altMethod')
        );
    }

    public function testBuildFromPathArguments(): void
    {
        $this->assertSame(
            '/deep/example/alt-method/test/a/2',
            Router::buildFromPath('Tests\Controller\Deep\Example::altMethod/test/a/2')
        );
    }

    public function testBuildFromPathFullBase(): void
    {
        Router::setBaseUri('https://test.com/');

        $this->assertSame(
            'https://test.com/home',
            Router::buildFromPath('Tests\Controller\Home', [
                'fullBase' => true
            ])
        );
    }

    public function testBuildFromPathFullBaseDeep(): void
    {
        Router::setBaseUri('https://test.com/deep/');

        $this->assertSame(
            'https://test.com/deep/home',
            Router::buildFromPath('Tests\Controller\Home', [
                'fullBase' => true
            ])
        );
    }

    public function testBuildFromPathLeadingSlash(): void
    {
        $this->assertSame(
            '/deep/example/alt-method/test/a/2',
            Router::buildFromPath('\Tests\Controller\Deep\Example::altMethod/test/a/2')
        );
    }

    public function testBuildFromPathDefaultNamespace(): void
    {
        Router::clear();
        Router::setDefaultNamespace('Tests\Controller');

        $this->assertSame(
            '/deep/example/alt-method',
            Router::buildFromPath('\Tests\Controller\Deep\Example::altMethod')
        );
    }

    public function testBuildFromPathInvalid(): void
    {
        $this->assertSame(
            '/example/alt-method',
            Router::buildFromPath('Example::altMethod')
        );
    }

}
