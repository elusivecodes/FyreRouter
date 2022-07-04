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
        BaseUriTest,
        BuildFromPathTest,
        BuildTest,
        ConnectTest,
        DefaultNamespaceTest,
        DeleteTest,
        FindRouteTest,
        GetTest,
        GroupTest,
        PatchTest,
        PostTest,
        PutTest,
        RedirectTest;

    public function testDefaultRoute(): void
    {
        Router::setDefaultRoute('Home');

        $defaultRoute = Router::getDefaultRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $defaultRoute
        );

        $this->assertSame(
            '\Tests\Mock\Controller\HomeController',
            $defaultRoute->getController()
        );
    }

    public function testDefaultRouteFind(): void
    {
        Router::setDefaultRoute('Home');

        $request = new ServerRequest;

        Router::loadRoute($request);

        $this->assertSame(
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

        $this->assertSame(
            '\Tests\Mock\Controller\ErrorController',
            $errorRoute->getController()
        );
    }

    protected function setUp(): void
    {
        Router::clear();
        Router::setAutoRoute(false);
        Router::setDefaultNamespace('Tests\Mock\Controller');
        Router::setDelimiter('-');
    }

}
