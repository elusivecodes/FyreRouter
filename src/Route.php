<?php
declare(strict_types=1);

namespace Fyre\Router;

use Closure;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function array_slice;
use function in_array;
use function preg_match;
use function strtolower;

/**
 * Route
 */
abstract class Route
{

    protected Closure|string $destination;

    protected string $path;

    protected array $methods;

    protected array $arguments = [];

    /**
     * New Route constructor.
     * @param Closure|string $destination The route destination.
     * @param string $path The route path.
     * @param array $methods The Route methods.
     */
    public function __construct(Closure|string $destination, string $path = '', array $methods = [])
    {
        $this->destination = $destination;
        $this->path = $path;

        $this->methods = array_map(
            fn(string $method): string => strtolower($method),
            $methods
        );
    }

    /**
     * Check if the route matches a test method.
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
     * @param string $path The test path.
     * @return bool TRUE if the path matches, otherwise FALSE.
     */
    public function checkPath(string $path): bool
    {
        return !!preg_match($this->getPathRegExp(), $path);
    }

    /**
     * Get the route arguments.
     * @return array The route arguments.
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get the route destination.
     * @return Closure|string The route destination.
     */
    public function getDestination(): Closure|string
    {
        return $this->destination;
    }

    /**
     * Get the route path.
     * @return string The route path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Process the route.
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse The ClientResponse.
     */
    abstract public function process(ServerRequest $request, ClientResponse $response): ClientResponse;


    /**
     * Set the route arguments.
     * @param array $arguments The route arguments.
     * @return Route A bew Route.
     */
    public function setArguments(array $arguments): static
    {
        $temp = clone $this;

        $temp->arguments = $arguments;

        return $temp;
    }

    /**
     * Set the route arguments from a path.
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
     * Get the route path regulat rexpression.
     * @return string The route path regular expression.
     */
    protected function getPathRegExp(): string
    {
        return '`^'.$this->path.'$`';
    }

}
