<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Router;

trait FindRouteTest
{

    public function testFindRoute(): void
    {
        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home')
        );
    }

    public function testFindRouteMethod(): void
    {
        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('home/alt-method')
        );
    }

    public function testFindRouteDeep(): void
    {
        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('deep/example')
        );
    }

    public function testFindRouteDeepMethod(): void
    {
        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('deep/example/alt-method')
        );
    }

    public function testFindRouteArguments(): void
    {
        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => [
                    'test',
                    'a',
                    '2'
                ]
            ],
            Router::findRoute('deep/example/alt-method/test/a/2')
        );
    }

    public function testFindRouteLeadingSlash(): void
    {
        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('/home')
        );
    }

    public function testFindRouteTrailingSlash(): void
    {
        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home/')
        );
    }

    public function testFindRouteDefaultNamespace(): void
    {
        Router::clear();
        Router::setDefaultNamespace('Tests\Controller');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('deep/example/alt-method')
        );
    }

    public function testFindRouteInvalid(): void
    {
        $this->expectException(RouterException::class);

        Router::findRoute('invalid');
    }

}
