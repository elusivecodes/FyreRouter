<?php
declare(strict_types=1);

namespace Fyre\Router;

use
    Closure,
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Routes\ClosureRoute,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Router\Routes\RedirectRoute,
    Fyre\Server\ServerRequest,
    Fyre\URI\Uri;

use function
    array_filter,
    array_map,
    array_merge,
    array_pop,
    array_shift,
    array_slice,
    array_unshift,
    class_exists,
    count,
    explode,
    implode,
    is_array,
    is_string,
    lcfirst,
    method_exists,
    preg_match_all,
    preg_replace,
    preg_replace_callback,
    rawurldecode,
    rawurlencode,
    str_replace,
    str_starts_with,
    strlen,
    strtolower,
    substr,
    trim,
    ucwords;

/**
 * Router
 */
abstract class Router
{

    protected static array $routes = [];

    protected static array $pathPrefixes = [];

    protected static array $namespaces = [];

    protected static string $defaultNamespace = '\\';

    protected static ServerRequest|null $request = null;

    protected static Uri|null $baseUri = null;

    protected static Route|null $route = null;

    protected static Route|null $defaultRoute = null;

    protected static Route|null $errorRoute = null;

    protected static string $delimiter = '-';

    protected static bool $autoRoute = true;

    /**
     * Add a namespace for auto routing.
     * @param string $namespace The namespace.
     * @param string $pathPrefix The path prefix.
     */
    public static function addNamespace(string $namespace, string $pathPrefix = ''): void
    {
        $namespace = static::normalizeNamespace($namespace);

        static::$namespaces[$namespace] = static::normalizePath($pathPrefix, true);
    }

    /**
     * Generate a URL for a destination.
     * @param string|array $destination The destination.
     * @param array $options The route options.
     * @return string|null The URL.
     */
    public static function build(string|array $destination, array $options = []): string|null
    {
        $options['fullBase'] ??= false;

        $query = null;
        $fragment = null;

        if (is_array($destination)) {
            $controller = $destination['controller'] ?? null;
            $action = $destination['action'] ?? null;
            $query = $destination['?'] ?? null;
            $fragment = $destination['#'] ?? null;

            unset($destination['controller']);
            unset($destination['action']);
            unset($destination['?']);
            unset($destination['#']);

            if (static::$route && static::$route instanceof ControllerRoute) {
                $controller ??= preg_replace('/Controller$/', '', static::$route->getController());        
            }

            $arguments = $destination;

            if ($action) {
                $destination = $controller.'::'.$action;
            } else {
                $destination = $controller;
            }

            $destinationRoute = new ControllerRoute($destination);
            $destinationRoute->setArguments($arguments);

            $destination = static::reversePath($destinationRoute);

            if ($destination === null) {
                return $destination;
            }    
        }

        if ($options['fullBase'] && static::$baseUri) {
            $uri = static::$baseUri->resolveRelativeUri($destination);
        } else {
            $uri = Uri::create($destination);
        }

        if ($query) {
            $uri->setQuery($query);
        }

        if ($fragment) {
            $uri->setFragment($fragment);
        }

        return $uri->getUri();
    }

    /**
     * Generate a URL for a destination path.
     * @param string $destination The destination path.
     * @param array $options The route options.
     * @return string|null The URL.
     */
    public static function buildFromPath(string $destination, array $options = []): string|null
    {
        $destination = explode('::', $destination, 2);

        $controller = array_shift($destination);
        $action = array_shift($destination) ?? 'index';

        $destination = explode('/', $action);
        $destination['action'] = array_shift($destination);
        $destination['controller'] = $controller;

        return static::build($destination, $options);
    }

    /**
     * Clear all routes and namespaces.
     */
    public static function clear(): void
    {
        static::$routes = [];
        static::$namespaces = [];
        static::$route = null;
        static::$defaultRoute = null;
        static::$errorRoute = null;
        static::$baseUri = null;
    }

    /**
     * Connect a route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function connect(string $path, Closure|string $destination, array $options = []): void
    {
        $options['method'] ??= [];
        $options['redirect'] ??= false;

        $path = static::normalizePath($path, true);

        if (static::$pathPrefixes !== []) {
            $path = '/'.implode('/', static::$pathPrefixes).$path;
        }

        $methods = (array) $options['method'];

        if ($options['redirect']) {
            $route = new RedirectRoute($destination, $path, $methods);
        } else if (is_string($destination)) {
            $route = new ControllerRoute($destination, $path, $methods);
        } else {
            $route = new ClosureRoute($destination, $path, $methods);
        }

        static::$routes[] = $route;
    }

    /**
     * Connect a DELETE route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function delete(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'delete']);
    }

    /**
     * Connect a GET route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function get(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'get']);
    }

    /**
     * Get the base uri.
     * @return string|null The base uri.
     */
    public static function getBaseUri(): string|null
    {
        return static::$baseUri ?
            static::$baseUri->getUri() :
            null;
    }

    /**
     * Get the default namespace.
     * @return string The default namespace.
     */
    public static function getDefaultNamespace(): string
    {
        return static::$defaultNamespace;
    }

    /**
     * Get the default route.
     * @return ControllerRoute|null The default route.
     */
    public static function getDefaultRoute(): ControllerRoute|null
    {
        return static::$defaultRoute;
    }

    /**
     * Get the error route.
     * @return ControllerRoute|null The error route.
     */
    public static function getErrorRoute(): ControllerRoute|null
    {
        return static::$errorRoute;
    }

    /**
     * Get the ServerRequest.
     * @return ServerRequest The ServerRequest.
     */
    public static function getRequest(): ServerRequest|null
    {
        return static::$request;
    }

    /**
     * Get the current route.
     * @return Route|null The current route.
     */
    public static function getRoute(): Route|null
    {
        return static::$route;
    }

    /**
     * Create a group of routes.
     * @param string $prefix The route prefix.
     * @param Closure $callback The callback to define routes.
     */
    public static function group(string $pathPrefix, Closure $callback): void
    {
        static::$pathPrefixes[] = trim($pathPrefix, '/');

        $callback();

        array_pop(static::$pathPrefixes);
    }

    /**
     * Load a route.
     * @param ServerRequest $request The ServerRequest.
     */
    public static function loadRoute(ServerRequest $request): void
    {
        static::$request = $request;

        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $path = static::normalizePath($path);

        if (static::$baseUri) {
            $basePath = static::$baseUri->getPath();

            $basePath = static::normalizePath($basePath);

            if (str_starts_with($path, $basePath)) {
                $path = substr($path, strlen($basePath));
                $path = static::normalizePath($path);
            }
        }

        if ($path === '/' && static::$defaultRoute) {
            static::$route = static::$defaultRoute;

            return;
        }

        foreach (static::$routes AS $route) {
            if (!$route->checkMethod($method) || !$route->checkPath($path)) {
                continue;
            }

            $route->setArgumentsFromPath($path);

            static::$route = $route;

            return;
        }

        if (static::$autoRoute) {
            static::autoLoadRoute($path);

            return;
        }

        throw RouterException::forInvalidRoute($path);
    }

    /**
     * Connect a PATCH route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function patch(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'patch']);
    }

    /**
     * Connect a POST route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function post(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'post']);
    }

    /**
     * Connect a PUT route.
     * @param string $path The route path.
     * @param Closure|string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function put(string $path, Closure|string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['method' => 'put']);
    }

    /**
     * Connect a redirect route.
     * @param string $path The route path.
     * @param string $destination The route destination.
     * @param array $options Options for configuring the route.
     */
    public static function redirect(string $path, string $destination, array $options = []): void
    {
        static::connect($path, $destination, $options + ['redirect' => true]);
    }

    /**
     * Configure whether auto-routing will be used.
     * @param bool $autoRoute Whether auto-routing will be used.
     */
    public static function setAutoRoute(bool $autoRoute = true): void
    {
        static::$autoRoute = $autoRoute;
    }

    /**
     * Set the base uri.
     * @param string $baseUri The uri.
     */
    public static function setBaseUri(string $baseUri): void
    {
        static::$baseUri = Uri::create($baseUri);
    }

    /**
     * Set the default namespace.
     * @param string $namespace The namespace.
     */
    public static function setDefaultNamespace(string $namespace): void
    {
        static::$defaultNamespace = static::normalizeNamespace($namespace);
    }

    /**
     * Set the default route.
     * @param string $destination The destination route.
     */
    public static function setDefaultRoute(string $destination): void
    {
        static::$defaultRoute = new ControllerRoute($destination);
    }

    /**
     * Set the auto-routing delimiter.
     * @param string $delimiter The delimiter.
     */
    public static function setDelimiter(string $delimiter): void
    {
        static::$delimiter = $delimiter;
    }

    /**
     * Set the error route.
     * @param string $destination The destination string.
     */
    public static function setErrorRoute(string $destination): void
    {
        static::$errorRoute = new ControllerRoute($destination);
    }

    /**
     * Set the ServerRequest.
     * @param ServerRequest $request
     */
    public static function setRequest(ServerRequest $request): void
    {
        static::$request = $request;
    }

    /**
     * Find an auto-route for a path.
     * @param string $path The route path.
     * @throws RouterException if the route was not found.
     */
    protected static function autoLoadRoute(string $path): void
    {
        $namespaces = static::getAllNamespaces();

        $segments = explode('/', $path);
        $segments = array_filter($segments);
        $segments[] = 'index';

        $segments = array_map(
            fn(string $segment): string => static::classify($segment),
            $segments
        );

        $indexTest = true;

        $arguments = [];

        while ($segments !== []) {
            $method = array_pop($segments);
            $method = lcfirst($method);

            foreach ($namespaces AS $namespace => $pathPrefix) {
                $classSegments = $segments;

                if (!str_starts_with($path, $pathPrefix)) {
                    continue;
                }

                if ($pathPrefix !== '/') {
                    $prefixSegments = explode('/', $pathPrefix);
                    $prefixCount = count($prefixSegments);
                    $classSegments = array_slice($classSegments, $prefixCount - 1);
                }

                if ($classSegments === []) {
                    continue;
                }

                $classSuffix = implode('\\', $classSegments);
                $class = $namespace.$classSuffix.'Controller';

                if (!class_exists($class) || !method_exists($class, $method)) {
                    continue;
                }

                $route = new ControllerRoute($namespace.$classSuffix.'::'.$method);
                $route->setArguments($arguments);

                static::$route = $route;

                return;
            }

            if ($indexTest) {
                $indexTest = false;
            } else {
                $segments[] = 'index';
                $indexTest = true;

                array_unshift($arguments, $method);
            }
        }

        throw RouterException::forInvalidRoute($path);
    }

    /**
     * Build an auto-route path from a destination route.
     * @param ControllerRoute $destinationRoute The destination route.
     * @return string|null The route path.
     */
    protected static function autoLoadPath(ControllerRoute $destinationRoute): string|null
    {
        $controller = $destinationRoute->getController();
        $action = $destinationRoute->getAction();
        $arguments = $destinationRoute->getArguments();
        $namespaces = static::getAllNamespaces();

        $segments = [];

        foreach ($namespaces AS $namespace => $pathPrefix) {
            if ($namespace === '\\') {
                continue;
            }

            if (!str_starts_with($controller, $namespace)) {
                continue;
            }

            $namespaceLength = strlen($namespace);
            $controller = substr($controller, $namespaceLength);

            if ($pathPrefix) {
                $segments = explode('/', $pathPrefix);
            }
        }

        $controller = preg_replace('/Controller$/', '', $controller);

        $controllerSegments = explode('\\', $controller);

        if ($action !== 'index') {
            $controllerSegments[] = $action;
        }

        $controllerSegments = array_map(
            fn(string $segment): string => static::slug($segment),
            $controllerSegments
        );

        $segments = array_merge($segments, $controllerSegments);
        $segments = array_filter($segments);

        $segments = array_merge($segments, $arguments);

        $segments = array_map(
            fn(string $segment): string => rawurlencode($segment),
            $segments
        );

        return implode('/', $segments);
    }

    /**
     * Convert a string as a class name.
     * @param string $string The input string.
     * @return string The class name.
     */
    protected static function classify(string $string): string
    {
        $string = str_replace(static::$delimiter, ' ', $string);
        $string = ucwords($string);
        $string = str_replace(' ', '', $string);

        return $string;
    }

    /**
     * Get all defined namespaces.
     * @return array The defined namespaces.
     */
    protected static function getAllNamespaces(): array
    {
        $namespaces = [
            static::$defaultNamespace => '/'
        ];

        return array_merge($namespaces, static::$namespaces);
    }

    /**
     * Normalize a namespace
     * @param string $namespace The namespace.
     * @return string The normalized namespace.
     */
    protected static function normalizeNamespace(string $namespace): string
    {
        $namespace = trim($namespace, '\\');

        return $namespace ?
            '\\'.$namespace.'\\' :
            '\\';
    }

    /**
     * Normalize a path
     * @param string $path The path.
     * @param bool $decoded Whether to decode the path.
     * @return string The normalized path.
     */
    protected static function normalizePath(string $path, bool $decode = false): string
    {
        $path = trim($path, '/');

        if ($decode) {
            $path = rawurldecode($path);
        }

        return '/'.$path;
    }

    /**
     * Generate a reverse path for a destination route.
     * @param ControllerRoute $destinationRoute The destination route.
     * @return string|null The URL path.
     */
    protected static function reversePath(ControllerRoute $destinationRoute): string|null
    {
        $controller = $destinationRoute->getController();
        $action = $destinationRoute->getAction();
        $arguments = $destinationRoute->getArguments();
        $argumentCount = count($arguments);

        if (
            static::$defaultRoute &&
            static::$defaultRoute->getController() === $controller &&
            static::$defaultRoute->getAction() === $action &&
            $argumentCount === 0
        ) {
            return '/';
        }

        foreach (static::$routes AS $route) {
            if (
                !($route instanceof ControllerRoute) ||
                $route->getController() !== $controller ||
                $route->getAction() !== $action ||
                preg_match_all('/\(.*?\)/', $route->getPath()) !== $argumentCount
            ) {
                continue;
            }

            $index = 0;
            $url = preg_replace_callback(
                '/\(.*?\)/',
                function(array $matches) use ($arguments, &$index): string {
                    return $arguments[$index++];
                },
                $route->getPath()
            );

            $segments = array_map(
                fn(string $segment): string => rawurlencode($segment),
                explode('/', $url)
            );

            $segments = array_filter($segments);

            return implode('/', $segments);
        }

        if (static::$autoRoute) {
            return static::autoLoadPath($destinationRoute);
        }

        return null;
    }

    /**
     * Convert a string as a URL slug.
     * @param string $string The input string.
     * @return string The URL slug.
     */
    protected static function slug(string $string): string
    {
        $string = lcfirst($string);
        $string = preg_replace('/[A-Z]/', ' \0', $string);
        $string = str_replace(' ', static::$delimiter, $string);

        return strtolower($string);
    }

}
