<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Router,
    PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{

    use
        DefaultNamespaceTest,
        DeleteTest,
        FindRouteTest,
        GetTest,
        GroupTest,
        PatchTest,
        PostTest,
        PutTest,
        RedirectTest,
        RouteTest,
        UrlTest;

    public function testDefaultRoute(): void
    {
        Router::setDefaultRoute('Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::getDefaultRoute()
        );
    }

    public function testDefaultRouteFind(): void
    {
        Router::setDefaultRoute('Home');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'index',
                'arguments' => []
            ],
            Router::findRoute()
        );
    }

    public function testErrorRoute(): void
    {
        Router::setErrorRoute('Error');

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Error',
                'method' => 'index',
                'arguments' => []
            ],
            Router::getErrorRoute()
        );
    }

    public function setUp(): void
    {
        Router::clear();
        Router::setAutoRoute(false);
        Router::setDefaultNamespace('Tests\Controller');
        Router::setDelimiter('-');
    }

}
