<?php
declare(strict_types=1);

namespace Tests\Mock\Controller;

use Tests\Mock\Entity\Item;

class ItemsController
{
    public function index(Item $item): string
    {
        return $item->name;
    }

    public function test(Item|null $item = null): string
    {
        return '';
    }
}
