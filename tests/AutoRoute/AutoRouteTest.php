<?php
declare(strict_types=1);

namespace Tests\AutoRoute;

use
    Fyre\Router\Router,
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

        $this->assertEquals(
            [
                'type' => 'class',
                'class' => '\Tests\Controller\Home',
                'method' => 'altMethod',
                'arguments' => []
            ],
            Router::findRoute('home/alt_method')
        );
    }
    
    public function testDelimiterUrl(): void
    {
        Router::setDelimiter('_');

        $this->assertEquals(
            '/home/example_method',
            Router::url('Home::exampleMethod')
        );
    }

    public function setUp(): void
    {
        Router::clear();
        Router::setAutoRoute(true);
        Router::setDefaultNamespace('');
        Router::setDelimiter('-');
        Router::addNamespace('Tests\Controller');
    }

}
