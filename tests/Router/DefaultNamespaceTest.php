<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait DefaultNamespaceTest
{

    public function testDefaultNamespaceNotRetroactive(): void
    {
        Router::get('home', 'Home');
        Router::setDefaultNamespace('Invalid');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home', 'get')
        );
    }

    public function testDefaultNamespaceLeadingSlash(): void
    {
        Router::setDefaultNamespace('\Tests\Controller');
        Router::get('home', 'Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home', 'get')
        );
    }

    public function testDefaultNamespaceTrailingSlash(): void
    {
        Router::setDefaultNamespace('Tests\Controller\\');
        Router::get('home', 'Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home', 'get')
        );
    }

}
