<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait DeleteTest
{

    public function testDelete(): void
    {
        Router::delete('home', 'Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home', 'delete')
        );
    }

    public function testDeleteMethod(): void
    {
        Router::delete('home/alternate', 'Home::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('home/alternate', 'delete')
        );
    }

    public function testDeleteDeep(): void
    {
        Router::delete('example', 'Deep\Example');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('example', 'delete')
        );
    }

    public function testDeleteDeepMethod(): void
    {
        Router::delete('example/alternate', 'Deep\Example::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('example/alternate', 'delete')
        );
    }

    public function testDeleteArguments(): void
    {
        Router::delete('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => [
                    'test',
                    '2'
                ]
            ],
            Router::findRoute('example/alternate/test/a/2', 'delete')
        );
    }

    public function testDeleteCallback(): void
    {
        $function = function() {};

        Router::delete('test', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => []
            ],
            Router::findRoute('test', 'delete')
        );
    }

    public function testDeleteCallbackArguments(): void
    {
        $function = function() {};

        Router::delete('test/(.*)/(.*)', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => [
                    'a',
                    '2'
                ]
            ],
            Router::findRoute('test/a/2', 'delete')
        );
    }

}
