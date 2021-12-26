<?php
declare(strict_types=1);

namespace Tests\Router;

use
    Fyre\Router\Router;

trait RedirectTest
{

    public function testRedirect(): void
    {
        Router::redirect('test', 'https://test.com/');

        $this->assertEquals(
            [
                'type' => 'redirect',
                'redirect' => 'https://test.com/',
                'arguments' => []
            ],
            Router::findRoute('test', 'get')
        );
    }

    public function testRedirectArguments(): void
    {
        Router::redirect('test/(.*)/(.*)', 'https://test.com/');

        $this->assertEquals(
            [
                'type' => 'redirect',
                'redirect' => 'https://test.com/',
                'arguments' => [
                    'a',
                    '2'
                ]
            ],
            Router::findRoute('test/a/2', 'get')
        );
    }

}
