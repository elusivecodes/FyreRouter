<?php
declare(strict_types=1);

namespace Tests\Mock\Model;

use Fyre\ORM\Model;

class ItemsModel extends Model
{
    public function initialize(): void
    {
        $this->hasMany('Contains');
    }
}
