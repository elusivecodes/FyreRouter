<?php
declare(strict_types=1);

namespace Fyre\Router;

use Closure;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function array_keys;
use function array_map;
use function array_slice;
use function array_values;
use function in_array;
use function preg_match;
use function str_replace;
use function strtolower;

/**
 * Route
 */
abstract class Route
{
    protected array $arguments = [];

    protected array|Closure|string $destination;

    protected array $methods = [];

    protected array $middleware = [];

    protected string $path;

    /**
     * New Route constructor.
     *
     * @param array|Closure|string $destination The route destination.
     * @param string $path The route path.
     */
    public function __construct(array|Closure|string $destination, string $path = '')
    {
        $this->destination = $destination;
        $this->path = $path;
    }

    /**
     * Check if the route matches a test method.
     *
     * @param string $method The test method.
     * @return bool TRUE if the method matches, otherwise FALSE.
     */
    public function checkMethod(string $method): bool
    {
        if ($this->methods === []) {
            return true;
        }

        $method = strtolower($method);

        return in_array($method, $this->methods);
    }

    /**
     * Check if the route matches a test path.
     *
     * @param string $path The test path.
     * @return bool TRUE if the path matches, otherwise FALSE.
     */
    public function checkPath(string $path): bool
    {
        return (bool) preg_match($this->getPathRegExp(), $path);
    }

    /**
     * Get the route arguments.
     *
     * @return array The route arguments.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get the route destination.
     *
     * @return array|Closure|string The route destination.
     */
    public function getDestination(): array|Closure|string
    {
        return $this->destination;
    }

    /**
     * Get the route middleware.
     *
     * @return array The route middleware.
     */
    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    /**
     * Get the route path.
     *
     * @return string The route path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Process the route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse|string The ClientResponse or string response.
     */
    abstract public function process(ServerRequest $request, ClientResponse $response): ClientResponse|string;

    /**
     * Set the route arguments from a path.
     *
     * @param string $path The path.
     * @return Route A new Route.
     */
    public function setArgumentsFromPath(string $path): static
    {
        $temp = clone $this;

        preg_match($temp->getPathRegExp(), $path, $match);

        $temp->arguments = array_slice($match, 1);

        return $temp;
    }

    /**
     * Set the route methods.
     *
     * @param array $methods The route methods.
     * @return Route A new Route.
     */
    public function setMethods(array $methods): static
    {
        $temp = clone $this;

        $temp->methods = array_map(
            fn(string $method): string => strtolower($method),
            $methods
        );

        return $temp;
    }

    /**
     * Set the route middleware.
     *
     * @param array $middleware The route middleware.
     * @return Route A new Route.
     */
    public function setMiddleware(array $middleware): static
    {
        $temp = clone $this;

        $temp->middleware = $middleware;

        return $temp;
    }

    /**
     * Get the route path regular rexpression.
     *
     * @return string The route path regular expression.
     */
    protected function getPathRegExp(): string
    {
        $placeholders = Router::getPlaceholders();

        $placeholderKeys = array_map(
            fn(string $key): string => ':'.$key,
            array_keys($placeholders)
        );
        $placeholderValues = array_values($placeholders);

        $path = str_replace($placeholderKeys, $placeholderValues, $this->path);

        return '`^'.$path.'$`u';
    }
}
