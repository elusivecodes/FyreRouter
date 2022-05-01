<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router,
    Fyre\Server\ServerRequest;

trait BuildTest
{

    public function testBuild(): void
    {
        Router::get('home', 'Home');

        $this->assertSame(
            '/home',
            Router::build([
                'controller' => 'Home'
            ])
        );
    }

    public function testBuildAction(): void
    {
        Router::get('home/alternate', 'Home::altMethod');

        $this->assertSame(
            '/home/alternate',
            Router::build([
                'controller' => 'Home',
                'action' => 'altMethod'
            ])
        );
    }

    public function testBuildDeep(): void
    {
        Router::get('home', 'Deep\Example');

        $this->assertSame(
            '/home',
            Router::build([
                'controller' => 'Deep\Example'
            ])
        );
    }

    public function testBuildDeepAction(): void
    {
        Router::get('home/alternate', 'Deep\Example::altMethod');

        $this->assertSame(
            '/home/alternate',
            Router::build([
                'controller' => 'Deep\Example',
                'action' => 'altMethod'
            ])
        );
    }

    public function testBuildArguments(): void
    {
        Router::get('example/alternate/(.*)/(.*)/(.*)', 'Deep\Example::altMethod/$1/$3');

        $this->assertSame(
            '/example/alternate/test/a/2',
            Router::build([
                'controller' => 'Deep\Example',
                'action' => 'altMethod',
                'test',
                'a',
                '2'
            ])
        );
    }

    public function testBuildQuery(): void
    {
        Router::get('home', 'Home');

        $this->assertSame(
            '/home?test=value',
            Router::build([
                'controller' => 'Home',
                '?' => [
                    'test' => 'value'
                ]
            ])
        );
    }

    public function testBuildFragment(): void
    {
        Router::get('home', 'Home');

        $this->assertSame(
            '/home#test',
            Router::build([
                'controller' => 'Home',
                '#' => 'test'
            ])
        );
    }

    public function testBuildFullBase(): void
    {
        Router::setBaseUri('https://test.com/');
        Router::get('home', 'Home');

        $this->assertSame(
            'https://test.com/home',
            Router::build([
                'controller' => 'Home'
            ], [
                'fullBase' => true
            ])
        );
    }

    public function testBuildFullBaseDeep(): void
    {
        Router::setBaseUri('https://test.com/deep/');
        Router::get('home', 'Home');

        $this->assertSame(
            'https://test.com/deep/home',
            Router::build([
                'controller' => 'Home'
            ], [
                'fullBase' => true
            ])
        );
    }

    public function testBuildString(): void
    {
        $this->assertSame(
            '/assets/images/test.jpg',
            Router::build('assets/images/test.jpg')
        );
    }

    public function testBuildStringFullBase(): void
    {
        Router::setBaseUri('https://test.com/');

        $this->assertSame(
            'https://test.com/assets/images/test.jpg',
            Router::build('assets/images/test.jpg', [
                'fullBase' => true
            ])
        );
    }

    public function testBuildStringFullBaseDeep(): void
    {
        Router::setBaseUri('https://test.com/deep/');

        $this->assertSame(
            'https://test.com/deep/assets/images/test.jpg',
            Router::build('assets/images/test.jpg', [
                'fullBase' => true
            ])
        );
    }

    public function testBuildDefaultController(): void
    {
        Router::get('home', 'Home');
        Router::get('home/alternate', 'Home::altMethod');

        $request = new ServerRequest;
        $request->getUri()->setPath('home');

        Router::loadRoute($request);

        $this->assertSame(
            '/home/alternate',
            Router::build([
                'action' => 'altMethod'
            ])
        );
    }

}
