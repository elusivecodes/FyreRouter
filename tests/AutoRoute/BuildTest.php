<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Router;

trait BuildTest
{

    public function testBuild(): void
    {
        $this->assertSame(
            '/home',
            Router::build([
                'controller' => 'Tests\Controller\Home'
            ])
        );
    }

    public function testBuildAction(): void
    {
        $this->assertSame(
            '/home/alt-method',
            Router::build([
                'controller' => 'Tests\Controller\Home',
                'action' => 'altMethod'
            ])
        );
    }

    public function testBuildDeep(): void
    {
        $this->assertSame(
            '/deep/example',
            Router::build([
                'controller' => 'Tests\Controller\Deep\Example'
            ])
        );
    }

    public function testBuildDeepAction(): void
    {
        $this->assertSame(
            '/deep/example/alt-method',
            Router::build([
                'controller' => 'Tests\Controller\Deep\Example',
                'action' => 'altMethod'
            ])
        );
    }

    public function testBuildArguments(): void
    {
        $this->assertSame(
            '/deep/example/alt-method/test/a/2',
            Router::build([
                'controller' => 'Tests\Controller\Deep\Example',
                'action' => 'altMethod',
                'test',
                'a',
                '2'
            ])
        );
    }

    public function testBuildQuery(): void
    {
        $this->assertSame(
            '/home?test=value',
            Router::build([
                'controller' => 'Tests\Controller\Home',
                '?' => [
                    'test' => 'value'
                ]
            ])
        );
    }

    public function testBuildFragment(): void
    {
        $this->assertSame(
            '/home#test',
            Router::build([
                'controller' => 'Tests\Controller\Home',
                '#' => 'test'
            ])
        );
    }

    public function testBuildFullBase(): void
    {
        Router::setBaseUri('https://test.com/');

        $this->assertSame(
            'https://test.com/home',
            Router::build([
                'controller' => 'Tests\Controller\Home'
            ], [
                'fullBase' => true
            ])
        );
    }

    public function testBuildFullBaseDeep(): void
    {
        Router::setBaseUri('https://test.com/deep/');

        $this->assertSame(
            'https://test.com/deep/home',
            Router::build([
                'controller' => 'Tests\Controller\Home'
            ], [
                'fullBase' => true
            ])
        );
    }

    public function testBuildLeadingSlash(): void
    {
        $this->assertSame(
            '/deep/example/alt-method/test/a/2',
            Router::build([
                'controller' => '\Tests\Controller\Deep\Example',
                'action' => 'altMethod',
                'test',
                'a',
                '2'
            ])
        );
    }

    public function testBuildDefaultNamespace(): void
    {
        Router::clear();
        Router::setDefaultNamespace('Tests\Controller');

        $this->assertSame(
            '/deep/example/alt-method',
            Router::build([
                'controller' => '\Tests\Controller\Deep\Example',
                'action' => 'altMethod'
            ])
        );
    }

    public function testBuildInvalid(): void
    {
        $this->assertSame(
            '/example/alt-method',
            Router::build([
                'controller' => 'Example',
                'action' => 'altMethod'
            ])
        );
    }

}
