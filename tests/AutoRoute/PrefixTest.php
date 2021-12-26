<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Router;

trait PrefixTest
{

    public function testNamespacePrefix(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Controller', 'prefix');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('prefix/deep/example/alt-method')
        );
    }

    public function testNamespacePrefixLeadingSlash(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Controller', '/prefix');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('prefix/deep/example/alt-method')
        );
    }

    public function testNamespacePrefixTrailingSlash(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Controller', 'prefix/');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('prefix/deep/example/alt-method')
        );
    }

    public function testNamespaceUrlPrefix(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Controller', 'prefix');

        $this->assertEquals(
            '/prefix/deep/example/alt-method',
            Router::url('\Tests\Controller\Deep\Example::altMethod')
        );
    }

}
