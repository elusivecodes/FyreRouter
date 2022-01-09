<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest,
    PHPUnit\Framework\TestCase;

final class AutoRouteTest extends TestCase
{

    use
        FindRouteTest,
        PrefixTest,
        UrlTest;

    public function testDelimiterFindRoute(): void
    {
        Router::setDelimiter('_');

        $request = new ServerRequest;
        $request->getUri()->setPath('home/alt_method');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Home',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }
    
    public function testDelimiterUrl(): void
    {
        Router::setDelimiter('_');

        $this->assertSame(
            '/home/example_method',
            Router::url('Home::exampleMethod')
        );
    }

    protected function setUp(): void
    {
        Router::clear();
        Router::setAutoRoute(true);
        Router::setDefaultNamespace('');
        Router::setDelimiter('-');
        Router::addNamespace('Tests\Controller');
    }

}
