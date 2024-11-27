<?php
declare(strict_types=1);

namespace Tests\Mock\Middleware;

use Closure;
use Fyre\Middleware\Middleware;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

class ArgsMiddleware extends Middleware
{
    public function handle(ServerRequest $request, Closure $next, string ...$args): ClientResponse
    {
        return $next($request)->setJson($args);
    }
}
