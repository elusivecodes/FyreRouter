<?php
declare(strict_types=1);

namespace Fyre\Router;

use
    Closure,
    Fyre\Server\ClientResponse,
    Fyre\Server\ServerRequest;


use function
    in_array,
    strtolower;

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
            fn($method) => strtolower($method),
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
     * @return Route The Route.
     */
    public function setArguments(array $arguments): static
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Set the route arguments from a path.
     * @param string $path The path.
     * @return Route The Route.
     */
    public function setArgumentsFromPath(string $path): static
    {
        preg_match($this->getPathRegExp(), $path, $match);

        $this->arguments = array_slice($match, 1);

        return $this;
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
