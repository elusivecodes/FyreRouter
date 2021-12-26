<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait PatchTest
{

    public function testPatch(): void
    {
        Router::patch('home', 'Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home', 'patch')
        );
    }

    public function testPatchMethod(): void
    {
        Router::patch('home/alternate', 'Home::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('home/alternate', 'patch')
        );
    }

    public function testPatchDeep(): void
    {
        Router::patch('example', 'Deep\Example');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('example', 'patch')
        );
    }

    public function testPatchDeepMethod(): void
    {
        Router::patch('example/alternate', 'Deep\Example::altMethod');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Deep\Example',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('example/alternate', 'patch')
        );
    }

    public function testPatchArguments(): void
    {
        Router::patch('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

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
            Router::findRoute('example/alternate/test/a/2', 'patch')
        );
    }

    public function testPatchCallback(): void
    {
        $function = function() {};

        Router::patch('test', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => []
            ],
            Router::findRoute('test', 'patch')
        );
    }

    public function testPatchCallbackArguments(): void
    {
        $function = function() {};

        Router::patch('test/(.*)/(.*)', $function);

        $this->assertEquals(
            [
                'type' => 'callback',
                'callback' => $function,
                'arguments' => [
                    'a',
                    '2'
                ]
            ],
            Router::findRoute('test/a/2', 'patch')
        );
    }

}
