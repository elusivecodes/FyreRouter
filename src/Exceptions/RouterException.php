<?php
declare(strict_types=1);

namespace Fyre\Router\Exceptions;

use
    RuntimeException;

/**
 * RouterException
 */
class RouterException extends RuntimeException
{

    public static function forInvalidRoute(string $path): static
    {
        return new static('Route not found: '.$path, 404);
    }

}