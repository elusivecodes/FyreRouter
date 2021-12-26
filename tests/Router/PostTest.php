<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait PostTest
{

    public function testPost(): void
    {
        Router::post('home', 'Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home', 'post')
        );
    }

    public function testPostMethod(): void
    {
        Router::post('home/alternate', 'Home::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('home/alternate', 'post')
        );
    }

    public function testPostDeep(): void
    {
        Router::post('example', 'Deep\Example');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('example', 'post')
        );
    }

    public function testPostDeepMethod(): void
    {
        Router::post('example/alternate', 'Deep\Example::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('example/alternate', 'post')
        );
    }

    public function testPostArguments(): void
    {
        Router::post('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

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
            Router::findRoute('example/alternate/test/a/2', 'post')
        );
    }

    public function testPostCallback(): void
    {
        $function = function() {};

        Router::post('test', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => []
            ],
            Router::findRoute('test', 'post')
        );
    }

    public function testPostCallbackArguments(): void
    {
        $function = function() {};

        Router::post('test/(.*)/(.*)', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => [
                    'a',
                    '2'
                ]
            ],
            Router::findRoute('test/a/2', 'post')
        );
    }

}
