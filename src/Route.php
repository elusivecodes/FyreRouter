<?php
declare(strict_types=1);

namespace Fyre\Router;

use Closure;
use Fyre\Container\Container;
use Fyre\Server\ClientResponse;
use Fyre\Server\ServerRequest;

use function array_key_exists;
use function array_shift;
use function in_array;
use function is_string;
use function preg_match;
use function preg_match_all;
use function preg_replace_callback;
use function strtolower;

use const PREG_SET_ORDER;

/**
 * Route
 */
abstract class Route
{
    protected array $arguments = [];

    protected array $bindingFields = [];

    protected array $methods = [];

    protected array $middleware = [];

    protected array $placeholders = [];

    /**
     * New Route constructor.
     *
     * @param Container $container The Container.
     * @param array|Closure|string $destination The destination.
     * @param string $path The path.
     * @param array $options The route options.
     */
    public function __construct(
        protected Container $container,
        protected array|Closure|string $destination,
        protected string $path = '',
        array $options = []
    ) {
        $this->methods = $options['methods'] ?? [];
        $this->middleware = $options['middleware'] ?? [];
        $this->placeholders = $options['placeholders'] ?? [];
    }

    /**
     * Check if the route matches a test method and path.
     *
     * @param string $method The test method.
     * @param string $path The test path.
     * @return bool TRUE if the method and path match, otherwise FALSE.
     */
    public function checkRoute(string $method = 'get', string $path = ''): bool
    {
        return $this->checkMethod($method) && $this->checkPath($path);
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
     * Get the route binding fields.
     *
     * @return array The route binding fields.
     */
    public function getBindingFields(): array
    {
        return $this->bindingFields;
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
     * Get the reflection parameters.
     *
     * @return array The reflection parameters.
     */
    public function getParameters(): array
    {
        return [];
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
     * Get the route placeholders.
     *
     * @return array The route placeholders.
     */
    public function getPlaceholders(): array
    {
        return $this->placeholders;
    }

    /**
     * Handle the route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse The ClientResponse.
     */
    public function handle(ServerRequest $request, ClientResponse $response): ClientResponse
    {
        $result = $this->process($request, $response);

        if (is_string($result)) {
            return $response->setBody($result);
        }

        return $result;
    }

    /**
     * Set the route arguments.
     *
     * @param array $arguments The route arguments.
     * @return Route The Route.
     */
    public function setArguments(array $arguments): static
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Set the route middleware.
     *
     * @param array $middleware The route middleware.
     * @return Route The Route.
     */
    public function setMiddleware(array $middleware): static
    {
        $this->middleware = $middleware;

        return $this;
    }

    /**
     * Set a route placeholder.
     *
     * @param string $placeholder The route placeholder.
     * @param string $regex The route placeholder regex.
     * @return Route The Route.
     */
    public function setPlaceholder(string $placeholder, string $regex): static
    {
        $this->placeholders[$placeholder] = $regex;

        return $this;
    }

    /**
     * Check if the route matches a test method.
     *
     * @param string $method The test method.
     * @return bool TRUE if the method matches, otherwise FALSE.
     */
    protected function checkMethod(string $method): bool
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
    protected function checkPath(string $path): bool
    {
        if (!preg_match($this->getPathRegExp(), $path, $matches)) {
            return false;
        }

        array_shift($matches);

        preg_match_all('/\{([^\}]+)\}/', $this->path, $placeholders, PREG_SET_ORDER);

        $this->arguments = [];
        $this->bindingFields = [];

        foreach ($placeholders as $i => $placeholder) {
            if (!array_key_exists($i, $matches)) {
                continue;
            }

            $name = $placeholder[1];

            if (str_contains($name, ':')) {
                [$name, $field] = explode(':', $name, 2);
                $this->bindingFields[$name] = $field;
            }

            $this->arguments[$name] = $matches[$i];
        }

        return true;
    }

    /**
     * Get the route path regular rexpression.
     *
     * @return string The route path regular expression.
     */
    protected function getPathRegExp(): string
    {
        $path = preg_replace_callback('/\{([^\}]+)\}/', function(array $match): string {
            $placeholder = $match[1];

            return array_key_exists($placeholder, $this->placeholders) ?
                '('.$this->placeholders[$placeholder].')' :
                '([^/]+)';
        }, $this->path);

        return '`^'.$path.'$`u';
    }

    /**
     * Process the route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse|string The ClientResponse or string response.
     */
    abstract protected function process(ServerRequest $request, ClientResponse $response): ClientResponse|string;
}
