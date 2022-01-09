<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Router,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Server\ServerRequest;

trait PrefixTest
{

    public function testNamespacePrefix(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Controller', 'prefix');

        $request = new ServerRequest;
        $request->getUri()->setPath('prefix/deep/example/alt-method');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Deep\Example',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testNamespacePrefixLeadingSlash(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Controller', '/prefix');

        $request = new ServerRequest;
        $request->getUri()->setPath('prefix/deep/example/alt-method');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Deep\Example',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testNamespacePrefixTrailingSlash(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Controller', 'prefix/');

        $request = new ServerRequest;
        $request->getUri()->setPath('prefix/deep/example/alt-method');

        Router::loadRoute($request);

        $route = Router::getRoute();

        $this->assertInstanceOf(
            ControllerRoute::class,
            $route
        );

        $this->assertSame(
            '\Tests\Controller\Deep\Example',
            $route->getController()
        );

        $this->assertSame(
            'altMethod',
            $route->getAction()
        );
    }

    public function testNamespaceUrlPrefix(): void
    {
        Router::clear();
        Router::addNamespace('Tests\Controller', 'prefix');

        $this->assertSame(
            '/prefix/deep/example/alt-method',
            Router::url('\Tests\Controller\Deep\Example::altMethod')
        );
    }

}
