<?php
declare(strict_types=1);

namespace Fyre\Router;

use
    Closure,
    Fyre\Router\Exceptions\RouterException,
    Fyre\Router\Routes\ClosureRoute,
    Fyre\Router\Routes\ControllerRoute,
    Fyre\Router\Routes\RedirectRoute,
    Fyre\Server\ServerRequest;

use function
    array_filter,
    array_map,
    array_merge,
    array_pop,
    array_slice,
    array_unshift,
    class_exists,
    count,
    explode,
    implode,
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

        static::$namespaces[$namespace] = static::normalizePath($pathPrefix);
    }

    /**
     * Clear all routes and namespaces.
     */
    public static function clear(): void
    {
        static::$routes = [];
        static::$namespaces = [];
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

        $path = static::normalizePath($path);

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
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        $path = static::normalizePath($path);

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

        if (!static::$autoRoute) {
            throw RouterException::forInvalidRoute($path);
        }

        static::autoLoadRoute($path);
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
     * Find a route path for a destination string.
     * @param string $destination The destination string.
     * @param array $arguments The route arguments.
     * @return string|null The route path.
     */
    public static function url(string $destination, array $arguments = []): string|null
    {
        $testRoute = new ControllerRoute($destination);
        $argumentCount = count($arguments);

        foreach (static::$routes AS $route) {
            if (
                !($route instanceof ControllerRoute) ||
                $route->getController() !== $testRoute->getController() ||
                $route->getAction() !== $testRoute->getAction() ||
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

            return implode('/', $segments);
        }

        if (static::$autoRoute) {
            $testRoute->setArguments($arguments);

            return static::autoLoadUrl($testRoute);
        }

        return null;
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
                $class = $namespace.$classSuffix;

                if (!class_exists($class) || !method_exists($class, $method)) {
                    continue;
                }

                $route = new ControllerRoute($class.'::'.$method);
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
     * Build an auto-route path from a route array.
     * @param ControllerRoute $route The ControllerRoute.
     * @return string|null The route path.
     */
    protected static function autoLoadUrl(ControllerRoute $route): string|null
    {
        $namespaces = static::getAllNamespaces();

        $controller = $route->getController();
        $action = $route->getAction();

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

        $segments = array_merge($segments, $route->getArguments());

        $segments = array_map(
            fn(string $segment): string => rawurlencode($segment),
            $segments
        );

        return '/'.implode('/', $segments);
    }

    /**
     * Conver a string as a class name.
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
     * @return string The normalized path.
     */
    protected static function normalizePath(string $path): string
    {
        $path = trim($path, '/');

        return '/'.rawurldecode($path);
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
