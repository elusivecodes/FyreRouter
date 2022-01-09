<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait UrlTest
{

    public function testUrl(): void
    {
        Router::get('home', 'Home');

        $this->assertSame(
            '/home',
            Router::url('Home')
        );
    }

    public function testUrlAction(): void
    {
        Router::get('home/alternate', 'Home::altMethod');

        $this->assertSame(
            '/home/alternate',
            Router::url('Home::altMethod')
        );
    }

    public function testUrlDeep(): void
    {
        Router::get('home', 'Deep\Example');

        $this->assertSame(
            '/home',
            Router::url('Deep\Example')
        );
    }

    public function testUrlDeepAction(): void
    {
        Router::get('home/alternate', 'Deep\Example::altMethod');

        $this->assertSame(
            '/home/alternate',
            Router::url('Deep\Example::altMethod')
        );
    }

    public function testUrlArguments(): void
    {
        Router::get('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $this->assertSame(
            '/example/alternate/test/a/2',
            Router::url('Deep\Example::altMethod', ['test', 'a', '2'])
        );
    }

}
