<?php
declare(strict_types=1);

namespace Fyre\Router\Routes;

use Closure;
use Fyre\Container\Container;
use Fyre\Router\Route;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;
use ReflectionFunction;

/**
 * ClosureRoute
 */
class ClosureRoute extends Route
{
    /**
     * New ClosureRoute constructor.
     *
     * @param Container $container The Container.
     * @param Closure $destination The destination.
     * @param string $path The path.
     * @param array $options The route options.
     */
    public function __construct(Container $container, Closure $destination, string $path = '', array $options = [])
    {
        parent::__construct($container, $destination, $path, $options);
    }

    /**
     * Get the reflection parameters.
     *
     * @return array The reflection parameters.
     */
    public function getParameters(): array
    {
        return (new ReflectionFunction($this->destination))->getParameters();
    }

    /**
     * Process the route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse|string The ClientResponse or string response.
     */
    protected function process(ServerRequest $request, ClientResponse $response): ClientResponse|string
    {
        $arguments = ['request' => $request, 'response' => $response, ...$this->arguments];

        return $this->container->call($this->destination, $arguments);
    }
}
