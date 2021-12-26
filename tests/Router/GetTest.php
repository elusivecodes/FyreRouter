<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait GetTest
{

    public function testGet(): void
    {
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

    public function testGetMethod(): void
    {
        Router::get('home/alternate', 'Home::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('home/alternate', 'get')
        );
    }

    public function testGetDeep(): void
    {
        Router::get('example', 'Deep\Example');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('example', 'get')
        );
    }

    public function testGetDeepMethod(): void
    {
        Router::get('example/alternate', 'Deep\Example::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('example/alternate', 'get')
        );
    }

    public function testGetArguments(): void
    {
        Router::get('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

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
            Router::findRoute('example/alternate/test/a/2', 'get')
        );
    }

    public function testGetCallback(): void
    {
        $function = function() {};

        Router::get('test', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => []
            ],
            Router::findRoute('test', 'get')
        );
    }

    public function testGetCallbackArguments(): void
    {
        $function = function() {};

        Router::get('test/(.*)/(.*)', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => [
                    'a',
                    '2'
                ]
            ],
            Router::findRoute('test/a/2', 'get')
        );
    }

}
