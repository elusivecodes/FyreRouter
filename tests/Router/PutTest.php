<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait PutTest
{

    public function testPut(): void
    {
        Router::put('home', 'Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home', 'put')
        );
    }

    public function testPutMethod(): void
    {
        Router::put('home/alternate', 'Home::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('home/alternate', 'put')
        );
    }

    public function testPutDeep(): void
    {
        Router::put('example', 'Deep\Example');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('example', 'put')
        );
    }

    public function testPutDeepMethod(): void
    {
        Router::put('example/alternate', 'Deep\Example::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('example/alternate', 'put')
        );
    }

    public function testPutArguments(): void
    {
        Router::put('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

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
            Router::findRoute('example/alternate/test/a/2', 'put')
        );
    }

    public function testPutCallback(): void
    {
        $function = function() {};

        Router::put('test', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => []
            ],
            Router::findRoute('test', 'put')
        );
    }

    public function testPutCallbackArguments(): void
    {
        $function = function() {};

        Router::put('test/(.*)/(.*)', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => [
                    'a',
                    '2'
                ]
            ],
            Router::findRoute('test/a/2', 'put')
        );
    }

}
