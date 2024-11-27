<?php
declare(strict_types=1);

namespace Tests\Mock\Model;

use Fyre\ORM\Model;

class ContainsModel extends Model
{
    public function initialize(): void
    {
        $this->belongsTo('Items');
    }
}
