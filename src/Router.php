<?php
declare(strict_types=1);

namespace Fyre\Router;

use Closure;
use Fyre\Config\Config;
use Fyre\Container\Container;
use Fyre\Entity\Entity;
use Fyre\Http\Uri;
use Fyre\ORM\ModelRegistry;
use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Routes\ClosureRoute;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Router\Routes\RedirectRoute;
use Fyre\Server\ServerRequest;

use function array_key_exists;
use function array_map;
use function array_merge;
use function array_pop;
use function explode;
use function implode;
use function is_object;
use function preg_match;
use function preg_replace_callback;
use function rawurldecode;
use function rawurlencode;
use function str_contains;
use function str_starts_with;
use function strlen;
use function substr;
use function trim;

/**
 * Router
 */
class Router
{
    protected Uri|null $baseUri = null;

    protected Container $container;

    protected array $groups = [];

    protected ModelRegistry $modelRegistry;

    protected array $routeAliases = [];

    protected array $routes = [];

    /**
     * New Router constructor.
     *
     * @param Container $container The Container.
     * @param ModelRegistry $modelRegistry The ModelRegistry.
     * @param Config $config The Config.
     */
    public function __construct(Container $container, ModelRegistry $modelRegistry, Config $config)
    {
        $this->container = $container;
        $this->modelRegistry = $modelRegistry;
        $this->baseUri = new Uri($config->get('App.baseUri', ''));
    }

    /**
     * Clear all routes and aliases.
     */
    public function clear(): void
    {
        $this->routes = [];
        $this->routeAliases = [];
    }

    /**
     * Connect a route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     * @return Route The Route.
     */
    public function connect(string $path, array|Closure|string $destination, array $options = []): Route
    {
        $options['redirect'] ??= false;

        $alias = $options['as'] ?? null;
        $path = static::normalizePath($path);
        $methods = (array) ($options['method'] ?? []);
        $middleware = (array) ($options['middleware'] ?? []);
        $placeholders = $options['placeholders'] ?? [];

        $groupAliases = [];
        $groupPrefixes = [];
        $groupMiddleware = [];
        $groupPlaceholders = [];

        foreach ($this->groups as $group) {
            if ($group['as']) {
                $groupAliases[] = $group['as'];
            }

            if ($group['prefix'] && $group['prefix'] !== '/') {
                $groupPrefixes[] = static::normalizePath($group['prefix']);
            }

            $groupMiddleware = array_merge($groupMiddleware, (array) $group['middleware']);
            $groupPlaceholders = array_merge($groupPlaceholders, $group['placeholders']);
        }

        if ($alias && $groupAliases !== []) {
            $alias = implode('', $groupAliases).$alias;
        }

        if ($groupPrefixes !== []) {
            $path = implode('', $groupPrefixes).$path;
            $path = static::normalizePath($path);
        }

        $middleware = array_merge($groupMiddleware, $middleware);
        $placeholders = array_merge($groupPlaceholders, $placeholders);

        if ($destination instanceof Closure) {
            $className = ClosureRoute::class;
        } else if ($options['redirect']) {
            $className = RedirectRoute::class;
        } else {
            $className = ControllerRoute::class;
        }

        $route = $this->container->build($className, [
            'destination' => $destination,
            'path' => $path,
            'options' => [
                'methods' => $methods,
                'middleware' => $middleware,
                'placeholders' => $placeholders,
            ],
        ]);

        $this->routes[] = $route;

        if ($alias) {
            $this->routeAliases[$alias] = $route;
        }

        return $route;
    }

    /**
     * Connect a DELETE route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     * @return Route The Route.
     */
    public function delete(string $path, array|Closure|string $destination, array $options = []): Route
    {
        return $this->connect($path, $destination, $options + ['method' => 'delete']);
    }

    /**
     * Connect a GET route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     * @return Route The Route.
     */
    public function get(string $path, array|Closure|string $destination, array $options = []): Route
    {
        return $this->connect($path, $destination, $options + ['method' => 'get']);
    }

    /**
     * Get the base uri.
     *
     * @return string|null The base uri.
     */
    public function getBaseUri(): string|null
    {
        return $this->baseUri ?
            $this->baseUri->getUri() :
            null;
    }

    /**
     * Create a group of routes.
     *
     * @param array $options The group options.
     * @param Closure $callback The callback to define routes.
     */
    public function group(array $options, Closure $callback): void
    {
        $options['prefix'] ??= null;
        $options['as'] ??= null;
        $options['middleware'] ??= [];
        $options['placeholders'] ??= [];

        $this->groups[] = $options;

        $this->container->call($callback, ['router' => $this]);

        array_pop($this->groups);
    }

    /**
     * Load a route.
     *
     * @param ServerRequest $request The ServerRequest.
     * @return ServerRequest The ServerRequest.
     *
     * @throws RouterException if the route was not found.
     */
    public function loadRoute(ServerRequest $request): ServerRequest
    {
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $path = rawurldecode($path);
        $path = static::normalizePath($path);

        if ($this->baseUri) {
            $basePath = $this->baseUri->getPath();

            $basePath = static::normalizePath($basePath);

            if (str_starts_with($path, $basePath)) {
                $path = substr($path, strlen($basePath));
                $path = static::normalizePath($path);
            }
        }

        foreach ($this->routes as $route) {
            if (
                !$route->checkMethod($method) ||
                !$route->checkPath($path)
            ) {
                continue;
            }

            return $request->setParam('route', $route);
        }

        throw RouterException::forInvalidRoute($path);
    }

    /**
     * Connect a PATCH route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     * @return Route The Route.
     */
    public function patch(string $path, array|Closure|string $destination, array $options = []): Route
    {
        return $this->connect($path, $destination, $options + ['method' => 'patch']);
    }

    /**
     * Connect a POST route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     * @return Route The Route.
     */
    public function post(string $path, array|Closure|string $destination, array $options = []): Route
    {
        return $this->connect($path, $destination, $options + ['method' => 'post']);
    }

    /**
     * Connect a PUT route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     * @return Route The Route.
     */
    public function put(string $path, array|Closure|string $destination, array $options = []): Route
    {
        return $this->connect($path, $destination, $options + ['method' => 'put']);
    }

    /**
     * Connect a redirect route.
     *
     * @param string $path The route path.
     * @param string $destination The route destination.
     * @param array $options Options for configuring the route.
     * @return Route The Route.
     */
    public function redirect(string $path, string $destination, array $options = []): Route
    {
        return $this->connect($path, $destination, $options + ['redirect' => true]);
    }

    /**
     * Generate a URL for a named route.
     *
     * @param string $name The name.
     * @param array $arguments The route arguments
     * @param array $options The route options.
     * @return string The URL.
     *
     * @throws RouterException for invalid alias, or invalid arguments.
     */
    public function url(string $name, array $arguments = [], array $options = []): string
    {
        $options['fullBase'] ??= false;

        if (!array_key_exists($name, $this->routeAliases)) {
            throw RouterException::forInvalidRouteAlias($name);
        }

        $query = $arguments['?'] ?? null;
        $fragment = $arguments['#'] ?? null;

        unset($arguments['?']);
        unset($arguments['#']);

        $route = $this->routeAliases[$name];

        $destination = $route->getPath();
        $placeholders = $route->getPlaceholders();

        $destination = preg_replace_callback('/\{([^\}]+)\}/', function(array $match) use ($arguments, $placeholders): string {
            $name = $match[1];

            if (str_contains($name, ':')) {
                [$name, $field] = explode(':', $name, 2);
            } else {
                $field = null;
            }

            if (!array_key_exists($name, $arguments)) {
                throw RouterException::forMissingRouteParameter($name);
            }

            $value = $arguments[$name];

            if (is_object($value) && $value instanceof Entity) {
                $alias = $value->getSource();
                $Model = $this->modelRegistry->use($alias);
                $field ??= $Model->getRouteKey();

                $value = $value->get($field);
            }

            $value = (string) $value;

            $pattern = $placeholders[$name] ?? '([^/]+)';

            if (!preg_match('`^'.$pattern.'$`u', $value)) {
                throw RouterException::forInvalidRouteParameter($name);
            }

            return $value;
        }, $destination);

        $segments = explode('/', $destination);
        $segments = array_map(
            fn(string $segment): string => rawurlencode($segment),
            $segments
        );
        $destination = implode('/', $segments);

        if ($options['fullBase'] && $this->baseUri) {
            $uri = $this->baseUri->resolveRelativeUri($destination);
        } else {
            $uri = Uri::fromString($destination);
        }

        if ($query) {
            $uri = $uri->setQuery($query);
        }

        if ($fragment) {
            $uri = $uri->setFragment($fragment);
        }

        return $uri->getUri();
    }

    /**
     * Normalize a path
     *
     * @param string $path The path.
     * @return string The normalized path.
     */
    protected function normalizePath(string $path): string
    {
        return '/'.trim($path, '/');
    }
}
