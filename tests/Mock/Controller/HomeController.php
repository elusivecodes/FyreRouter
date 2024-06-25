<?php
declare(strict_types=1);

namespace Tests\Mock\Controller;

class HomeController
{
    public function altMethod(): string
    {
        return '';
    }

    public function index(): string
    {
        return '';
    }
}
