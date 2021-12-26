<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait RouteTest
{

    public function testConnectLeadingSlash(): void
    {
        Router::connect('/home', 'Home');

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

    public function testConnectTrailingSlash(): void
    {
        Router::connect('home/', 'Home');

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

    public function testFindRouteLeadingSlash(): void
    {
        Router::get('home', 'Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('/home', 'get')
        );
    }

    public function testFindRouteTrailingSlash(): void
    {
        Router::get('home', 'Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute('home/', 'get')
        );
    }

}
