<?php
declare(strict_types=1);

namespace Fyre\Router\Exceptions;

use RuntimeException;

/**
 * RouterException
 */
class RouterException extends RuntimeException
{
    public static function forInvalidController(string $controller): static
    {
        return new static('Invalid controller class: '.$controller);
    }

    public static function forInvalidMethod(string $controller, string $method): static
    {
        return new static('Invalid controller method: '.$controller.'::'.$method);
    }

    public static function forInvalidRoute(string $path): static
    {
        return new static('Route not found: '.$path, 404);
    }

    public static function forInvalidRouteAlias(string $alias): static
    {
        return new static('Route alias not found: '.$alias, 404);
    }

    public static function forInvalidRouteParameter(string $name): static
    {
        return new static('Invalid route parameter: '.$name);
    }

    public static function forMissingRouteParameter(string $name): static
    {
        return new static('Missing route parameter: '.$name);
    }
}
