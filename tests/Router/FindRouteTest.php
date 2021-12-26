<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Router;

trait FindRouteTest
{

    public function testRouteOrder()
    {
        Router::get('(.*)', 'Home');
        Router::get('test', 'Test');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('test', 'get')
        );
    }

    public function testInvalidRoute(): void
    {
        $this->expectException(RouterException::class);

        Router::get('test', 'Test');
        Router::findRoute('test', 'post');
    }

    public function testInvalidMethod(): void
    {
        $this->expectException(RouterException::class);

        Router::findRoute('test', 'post');
    }

}
