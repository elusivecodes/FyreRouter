<?php
declare(strict_types=1);

namespace Tests\Router;

use Fyre\Router\Router;

trait BuildFromPathTestTrait
{

    public function testBuildFromPath(): void
    {
        Router::get('home', 'Home');

        $this->assertSame(
            '/home',
            Router::buildfromPath('Home')
        );
    }

    public function testBuildFromPathAction(): void
    {
        Router::get('home/alternate', 'Home::altMethod');

        $this->assertSame(
            '/home/alternate',
            Router::buildfromPath('Home::altMethod')
        );
    }

    public function testBuildFromPathDeep(): void
    {
        Router::get('home', 'Deep\Example');

        $this->assertSame(
            '/home',
            Router::buildfromPath('Deep\Example')
        );
    }

    public function testBuildFromPathDeepAction(): void
    {
        Router::get('home/alternate', 'Deep\Example::altMethod');

        $this->assertSame(
            '/home/alternate',
            Router::buildfromPath('Deep\Example::altMethod')
        );
    }

    public function testBuildFromPathArguments(): void
    {
        Router::get('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $this->assertSame(
            '/example/alternate/test/a/2',
            Router::buildfromPath('Deep\Example::altMethod/test/a/2')
        );
    }

    public function testBuildFromPathFullBase(): void
    {
        Router::setBaseUri('https://test.com/');
        Router::get('home', 'Home');

        $this->assertSame(
            'https://test.com/home',
            Router::buildfromPath('Home', [
                'fullBase' => true
            ])
        );
    }

    public function testBuildFromPathFullBaseDeep(): void
    {
        Router::setBaseUri('https://test.com/deep/');
        Router::get('home', 'Home');

        $this->assertSame(
            'https://test.com/deep/home',
            Router::buildfromPath('Home', [
                'fullBase' => true
            ])
        );
    }

}
