<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait GroupTest
{

    public function testGroup(): void
    {
        Router::group('prefix', function() {
            Router::get('home', 'Home');
        });

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('prefix/home', 'get')
        );
    }

    public function testGroupDeep(): void
    {
        Router::group('prefix', function() {
            Router::group('deep', function() {
                Router::get('home', 'Home');
            });
        });

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('prefix/deep/home', 'get')
        );
    }

    public function testGroupLeadingSlash(): void
    {
        Router::group('/prefix', function() {
            Router::get('home', 'Home');
        });

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('prefix/home', 'get')
        );
    }

    public function testGroupTrailingSlash(): void
    {
        Router::group('prefix/', function() {
            Router::get('home', 'Home');
        });

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('prefix/home', 'get')
        );
    }

}
