# FyreRouter

**FyreRouter** is a free, URL routing library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
- [Routes](#routes)
    - [Closure Routes](#closure-routes)
    - [Controller Routes](#controller-routes)
    - [Redirect Routes](#redirect-routes)
- [Router Middleware](#router-middleware)



## Installation

**Using Composer**

```
composer require fyre/router
```

In PHP:

```php
use Fyre\Router\Router;
```


## Methods

**Add Namespace**

Add a namespace for auto routing.

- `$namespace` is a string representing the namespace.
- `$pathPrefix` is a string representing the path prefix, and will default to "".

```php
Router::addNamespace($namespace, $pathPrefix);
```

**Build**

Generate a URL for a destination.

- `$destination` is a string or array containing the destination.
- `$options` is an array containing the route options.
    - `fullBase` is a boolean indicating whether to use the full base URI and will default to *false*.

```php
$url = Router::build($destination, $options);
```

Route destinations can be expressed in the following formats:

```php
Router::build([
    'controller' => 'MyClass'
]);
Router::build([
    'controller' => 'MyClass',
    'action' => 'myMethod'
]);
Router::build([
    'controller' => 'MyClass',
    'action' => 'myMethod',
    'arg1',
    'arg2'
]);
Router::build([
    'controller' => '\MyNamespace\MyClass',
    'action' => 'myMethod'
]);
Router::build([
    'controller' => 'MyClass',
    'action' => 'myMethod',
    '?' => [
        'key' => 'value'
    ]
]);
Router::build([
    'controller' => 'MyClass',
    'action' => 'myMethod',
    '#' => 'anchor'
]);
Router::build('assets/file.ext');
```

**Build From Path**

Generate a URL for a destination path.

- `$destination` is a string representing the destination path.
- `$options` is an array containing the route options.
    - `fullBase` is a boolean indicating whether to use the full base URI and will default to *false*.

```php
$url = Router::build($destination, $options);
```

Route destinations can be expressed in the following formats:

```php
Router::buildFromPath('MyClass');
Router::buildFromPath('MyClass::myMethod');
Router::buildFromPath('MyClass::myMethod/arg1/arg2');
Router::buildFromPath('\MyNamespace\MyClass::myMethod');
```

**Clear**

Clear all routes and namespaces.

```php
Router::clear();
```

**Connect**

Connect a route.

- `$path` is a string representing the route path, and can include regular expressions.
- `$destination` can be either a string representing the route destination, or a *Closure*.
- `$options` is an array containing configuration options.
    - `method` is an array of strings representing the matching methods, and will default to *[]*.
    - `redirect` is a boolean indicating whether the route is a redirect, and will default to *false*.

```php
Router::connect($path, $destination, $options);
```

You can generate the following helper methods to connect specific routes.

```php
Router::delete($path, $destination, $options);
Router::get($path, $destination, $options);
Router::patch($path, $destination, $options);
Router::post($path, $destination, $options);
Router::put($path, $destination, $options);
Router::redirect($path, $destination, $options);
```

See the [Routes](#routes) section for supported destination formats.

**Load Route**

Load a route.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).

```php
Router::loadRoute($request);
```

**Get Base Uri**

Get the base uri.

```php
$baseUri = Router::getBaseUri();
```

**Get Default Namespace**

Get the default namespace.

```php
$defaultNamespace = Router::getDefaultNamespace();
```

**Get Default Route**

Get the default route.

```php
$defaultRoute = Router::getDefaultRoute();
```

This method will return a *Route*.

**Get Error Route**

Get the error route.

```php
$errorRoute = Router::getErrorRoute();
```

This method will return a *Route*.

**Get Route**

Get the loaded route.

```php
$route = Router::getRoute();
```

This method will return a *Route*.

**Group**

Create a group of routes.

- `$pathPrefix` is a string representing the path prefix.
- `$callback` is a *Closure* where routes can be defined using the prefix.

```php
Router::group($pathPrefix, $callback);
```

**Set Auto Route**

Configure whether auto-routing will be used.

- `$autoRoute` is a boolean indicating whether to enable auto-routes, and will default to *true*.

```php
Router::setAutoRoute($autoRoute);
```

**Set Base Uri**

Set the base uri.

- `$baseUri` is a string representing the base uri.

```php
Router::getBaseUri($baseUri);
```

**Set Default Namespace**

- `$namespace` is a string representing the namespace.

Set the default namespace.

```php
Router::setDefaultNamespace($namespace);
```

**Set Default Route**

Set the default route.

- `$destination` is a string representing the route destination.

```php
Router::setDefaultRoute($destination);
```

See the [Controller Routes](#controller-routes) section for supported destination formats.

**Set Delimiter**

Set the auto-routing delimiter.

- `$delimiter` is a string representing the delimiter.

```php
Router::setDelimiter($delimiter);
```

**Set Error Route**

Set the error route.

- `$destination` is a string representing the route destination.

```php
Router::setErrorRoute($destination);
```

See the [Controller Routes](#controller-routes) section for supported destination formats.


## Routes

All routes extend the `Fyre\Router\Route` class, and include the following methods.

**Check Method**

Check if the route matches a test method.

- `$method` is a string representing the method to test.

```php
$checkMethod = $route->checkMethod($method);
```

**Check Path**

Check if the route matches a test path.

- `$path` is a string representing the path to test.

```php
$checkPath = $route->checkPath($path);
```

**Get Arguments**

Get the route arguments.

```php
$arguments = $route->getArguments();
```

**Get Destination**

Get the route destination.

```php
$destination = $route->getDestination();
```

**Get Path**

Get the route path.

```php
$path = $route->getPath();
```

**Process**

Process the route.

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$response` is a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

```php
$response = $route->process($request, $response);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).

**Set Arguments**

Set the route arguments.

- `$arguments` is an array containing the route arguments.

```php
$route->setArguments($arguments);
```

**Set Arguments From Path**

Set the route arguments from a path.

- `$path` is a string representing the path.

```php
$route->setArgumentsFromPath($path);
```


### Closure Routes

```php
use Fyre\Router\Routes\ClosureRoute;
```

- `$destination` is a *Closure*.
- `$path` is a string representing the route path, and will default to "".
- `$methods` is an array containing the route methods.

```php
$route = new ClosureRoute($destination, $path, $methods);
```

The `$destination` should be expressed in the following format:

```php
$destination = function(ServerRequest $request, ClientResponse $response, ...$args) {
    return $response;
};
```


### Controller Routes

```php
use Fyre\Router\Routes\ControllerRoute;
```

- `$destination` is a string representing the destination.
- `$path` is a string representing the route path, and will default to "".
- `$methods` is an array containing the route methods.

```php
$route = new ControllerRoute($destination, $path, $methods);
```

The `$destination` can be expressed in the following formats:

```php
$destination = 'MyClass';
$destination = 'MyClass::myMethod';
$destination = 'MyClass::myMethod/$1/$2';
$destination = '\MyNamespace\MyClass::myMethod';
```

**Get Action**

Get the route controller action.

```php
$action = $route->getAction();
```

**Get Controller**

Get the route controller class name.

```php
$controller = $route->getController();
```


### Redirect Routes

```php
use Fyre\Router\Routes\RedirectRoute;
```

- `$destination` is a string representing the destination.
- `$path` is a string representing the route path, and will default to "".
- `$methods` is an array containing the route methods.

```php
$route = new RedirectRoute($destination, $path, $methods);
```

The `$destination` can be expressed in the following formats:

```php
$destination = 'https://test.com/';
$destination = 'https://test.com/$1';
```


## Router Middleware

```php
use Fyre\Router\Middleware\RouterMiddleware;
```

```php
$middleware = new RouterMiddleware();
```

**Process**

- `$request` is a [*ServerRequest*](https://github.com/elusivecodes/FyreServer#server-requests).
- `$handler` is a [*RequestHandler*](https://github.com/elusivecodes/FyreMiddleware#request-handlers).

```php
$response = $middleware->process($request, $handler);
```

This method will return a [*ClientResponse*](https://github.com/elusivecodes/FyreServer#client-responses).