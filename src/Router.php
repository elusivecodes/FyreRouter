<?php
declare(strict_types=1);

namespace Fyre\Router;

use Closure;
use Fyre\Http\Uri;
use Fyre\Router\Exceptions\RouterException;
use Fyre\Router\Routes\ClosureRoute;
use Fyre\Router\Routes\ControllerRoute;
use Fyre\Router\Routes\RedirectRoute;
use Fyre\Server\ServerRequest;

use function array_key_exists;
use function array_map;
use function array_merge;
use function array_pop;
use function array_shift;
use function explode;
use function implode;
use function preg_match;
use function rawurldecode;
use function rawurlencode;
use function str_starts_with;
use function strlen;
use function substr;
use function substr_replace;
use function trim;

use const PREG_OFFSET_CAPTURE;

/**
 * Router
 */
abstract class Router
{
    protected static Uri|null $baseUri = null;

    protected static Route|null $currentRoute = null;

    protected static array $groups = [];

    protected static array $placeholders = [
        'alpha' => '[a-zA-Z]+',
        'alphanum' => '[a-zA-Z0-9]+',
        'num' => '\d+',
        'segment' => '[^/]+',
    ];

    protected static ServerRequest|null $request = null;

    protected static array $routeAliases = [];

    protected static array $routes = [];

    /**
     * Add a placeholder.
     *
     * @param string $placeholder The placeholder.
     * @param string $pattern The placeholder pattern.
     */
    public static function addPlaceholder(string $placeholder, string $pattern): void
    {
        static::$placeholders[$placeholder] = $pattern;
    }

    /**
     * Clear all routes and aliases.
     */
    public static function clear(): void
    {
        static::$routes = [];
        static::$routeAliases = [];
        static::$currentRoute = null;
        static::$baseUri = null;
    }

    /**
     * Connect a route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function connect(string $path, array|Closure|string $destination, array $options = []): void
    {
        $options['redirect'] ??= false;

        $alias = $options['as'] ?? null;
        $path = static::normalizePath($path);
        $methods = (array) ($options['method'] ?? []);
        $middleware = (array) ($options['middleware'] ?? []);

        $groupAliases = [];
        $groupPrefixes = [];
        $groupMiddleware = [];

        foreach (static::$groups as $group) {
            if ($group['as']) {
                $groupAliases[] = $group['as'];
            }

            if ($group['prefix'] && $group['prefix'] !== '/') {
                $groupPrefixes[] = static::normalizePath($group['prefix']);
            }

            foreach ((array) $group['middleware'] as $tempMiddleware) {
                $groupMiddleware[] = $tempMiddleware;
            }
        }

        if ($alias && $groupAliases !== []) {
            $alias = implode('', $groupAliases).$alias;
        }

        if ($groupPrefixes !== []) {
            $path = implode('', $groupPrefixes).$path;
            $path = static::normalizePath($path);
        }

        $middleware = array_merge($groupMiddleware, $middleware);

        if ($destination instanceof Closure) {
            $route = new ClosureRoute($destination, $path);
        } else if ($options['redirect']) {
            $route = new RedirectRoute($destination, $path);
        } else {
            $route = new ControllerRoute((array) $destination, $path);
        }

        $route = $route->setMethods($methods);
        $route = $route->setMiddleware($middleware);

        static::$routes[] = $route;

        if ($alias) {
            static::$routeAliases[$alias] = $route;
        }
    }

    /**
     * Connect a DELETE route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function delete(string $path, array|Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'delete']);
    }

    /**
     * Connect a GET route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function get(string $path, array|Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'get']);
    }

    /**
     * Get the base uri.
     *
     * @return string|null The base uri.
     */
    public static function getBaseUri(): string|null
    {
        return static::$baseUri ?
            static::$baseUri->getUri() :
            null;
    }

    /**
     * Get the placeholders.
     *
     * @return array The placeholders.
     */
    public static function getPlaceholders(): array
    {
        return static::$placeholders;
    }

    /**
     * Get the ServerRequest.
     *
     * @return ServerRequest The ServerRequest.
     */
    public static function getRequest(): ServerRequest|null
    {
        return static::$request;
    }

    /**
     * Get the current route.
     *
     * @return Route|null The current route.
     */
    public static function getRoute(): Route|null
    {
        return static::$currentRoute;
    }

    /**
     * Create a group of routes.
     *
     * @param array $options The group options.
     * @param Closure $callback The callback to define routes.
     */
    public static function group(array $options, Closure $callback): void
    {
        $options['prefix'] ??= null;
        $options['as'] ??= null;
        $options['middleware'] ??= [];

        static::$groups[] = $options;

        $callback();

        array_pop(static::$groups);
    }

    /**
     * Load a route.
     *
     * @param ServerRequest $request The ServerRequest.
     *
     * @throws RouterException if the route was not found.
     */
    public static function loadRoute(ServerRequest $request): void
    {
        static::$request = $request;

        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $path = rawurldecode($path);
        $path = static::normalizePath($path);

        if (static::$baseUri) {
            $basePath = static::$baseUri->getPath();

            $basePath = static::normalizePath($basePath);

            if (str_starts_with($path, $basePath)) {
                $path = substr($path, strlen($basePath));
                $path = static::normalizePath($path);
            }
        }

        foreach (static::$routes as $route) {
            if (!$route->checkMethod($method) || !$route->checkPath($path)) {
                continue;
            }

            static::$currentRoute = $route->setArgumentsFromPath($path);

            return;
        }

        throw RouterException::forInvalidRoute($path);
    }

    /**
     * Connect a PATCH route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function patch(string $path, array|Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'patch']);
    }

    /**
     * Connect a POST route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function post(string $path, array|Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'post']);
    }

    /**
     * Connect a PUT route.
     *
     * @param string $path The route path.
     * @param array|Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function put(string $path, array|Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'put']);
    }

    /**
     * Connect a redirect route.
     *
     * @param string $path The route path.
     * @param string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function redirect(string $path, string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['redirect' => true]);
    }

    /**
     * Set the base uri.
     *
     * @param string $baseUri The uri.
     */
    public static function setBaseUri(string $baseUri): void
    {
        static::$baseUri = Uri::fromString($baseUri);
    }

    /**
     * Set the ServerRequest.
     */
    public static function setRequest(ServerRequest $request): void
    {
        static::$request = $request;
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
    public static function url(string $name, array $arguments = [], array $options = []): string
    {
        $options['fullBase'] ??= false;

        if (!array_key_exists($name, static::$routeAliases)) {
            throw RouterException::forInvalidRouteAlias($name);
        }

        $query = $arguments['?'] ?? null;
        $fragment = $arguments['#'] ?? null;

        unset($arguments['?']);
        unset($arguments['#']);

        $route = static::$routeAliases[$name];

        $destination = $route->getPath();

        $offset = 0;
        while (preg_match('/\(([^)]+)\)/', $destination, $matches, PREG_OFFSET_CAPTURE, $offset)) {
            if ($arguments === []) {
                throw RouterException::forMissingRouteParameter();
            }

            $match = $matches[0];
            $placeholder = $match[0];
            $placeholderKey = substr($placeholder, 2, -1);
            $pattern = static::$placeholders[$placeholderKey] ?? $placeholder;
            $value = (string) array_shift($arguments);

            if (!preg_match('`^'.$pattern.'$`u', $value)) {
                throw RouterException::forInvalidRouteParameter();
            }

            $destination = substr_replace($destination, $value, (int) $match[1], strlen($placeholder));
            $offset = $match[1] + strlen($value);
        }

        $segments = explode('/', $destination);
        $segments = array_map(
            fn(string $segment): string => rawurlencode($segment),
            $segments
        );
        $destination = implode('/', $segments);

        if ($options['fullBase'] && static::$baseUri) {
            $uri = static::$baseUri->resolveRelativeUri($destination);
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
    protected static function normalizePath(string $path): string
    {
        return '/'.trim($path, '/');
    }
}
