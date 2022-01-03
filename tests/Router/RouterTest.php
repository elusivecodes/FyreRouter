<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest,
    PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{

    use
        ConnectTest,
        DefaultNamespaceTest,
        DeleteTest,
        FindRouteTest,
        GetTest,
        GroupTest,
        PatchTest,
        PostTest,
        PutTest,
        RedirectTest,
        UrlTest;

    public function testDefaultRoute(): void
    {
        Router::setDefaultRoute('Home');

        $defaultRoute = Router::getDefaultRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $defaultRoute
        );

        $this->assertEquals(
            '\Tests\Controller\Home',
            $defaultRoute->getController()
        );
    }

    public function testDefaultRouteFind(): void
    {
        Router::setDefaultRoute('Home');

        $request = new ServerRequest;

        Router::loadRoute($request);

        $this->assertEquals(
            Router::getDefaultRoute(),
            Router::getRoute()
        );
    }

    public function testErrorRoute(): void
    {
        Router::setErrorRoute('Error');

        $errorRoute = Router::getErrorRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $errorRoute
        );

        $this->assertEquals(
            '\Tests\Controller\Error',
            $errorRoute->getController()
        );
    }

    protected function setUp(): void
    {
        Router::clear();
        Router::setAutoRoute(false);
        Router::setDefaultNamespace('Tests\Controller');
        Router::setDelimiter('-');
    }

}
