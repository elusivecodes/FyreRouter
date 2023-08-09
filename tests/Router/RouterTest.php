<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Server\ServerRequest;
use PHPUnit\Framework\TestCase;

final class RouterTest extends TestCase
{

    use BaseUriTestTrait;
    use BuildFromPathTestTrait;
    use BuildTestTrait;
    use ConnectTestTrait;
    use DefaultNamespaceTestTrait;
    use DeleteTestTrait;
    use FindRouteTestTrait;
    use GetTestTrait;
    use GroupTestTrait;
    use PatchTestTrait;
    use PostTestTrait;
    use PutTestTrait;
    use RedirectTestTrait;
    use ServerRequestTestTrait;

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

        $request = new ServerRequest();

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

    public function testGetNamespaces(): void
    {
        Router::addNamespace('Tests\Mock\Controller');

        $this->assertSame(
            [
                '\Tests\Mock\Controller\\' => '/'
            ],
            Router::getNamespaces()
        );
    }

    public function tesHasNamespaces(): void
    {
        Router::addNamespace('Tests\Mock\Controller');

        $this->assertTrue(
            Router::hasNamespace('Tests\Mock\Controller')
        );

    }

    public function testHasNamespaceInvalid(): void
    {
        $this->assertFalse(
            Router::hasNamespace('Tests\Mock\Invalid')
        );
    }

    public function tesRemoveNamespaces(): void
    {
        Router::addNamespace('Tests\Mock\Controller');

        $this->assertTrue(
            Router::removeNamespace('Tests\Mock\Controller')
        );
    }

    public function testRemoveNamespaceInvalid(): void
    {
        $this->assertFalse(
            Router::removeNamespace('Tests\Mock\Invalid')
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
