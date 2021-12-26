# FyreRouter

**FyreRouter** is a free, URL routing library for *PHP*.


## Table Of Contents
- [Installation](#installation)
- [Methods](#methods)
- [Destinations](#destinations)



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

**Find Route**

Find a route.

- `$path` is a string representing the route path.
- `$method` is a string representing the request method, and will default to `$_SERVER['REQUEST_METHOD']`.

```php
$route = Router::findRoute($path, $method);
```

**Get Default Route**

Get the default route.

```php
$defaultRoute = Router::getDefaultRoute();
```

**Get Error Route**

Get the error route.

```php
$errorRoute = Router::getErrorRoute();
```

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

**URL**

Find a route path for a destination string.

- `$destination` is a string representing the route destination.
- `$arguments` is an array containing the method arguments.

```php
$path = Router::url($destination, $arguments);
```


## Destinations

Route destinations can be expressed in the following formats:

```php
Router::get('my-class', 'MyClass');
Router::get('my-class/custom-method', 'MyClass::customMethod');
Router::get('my-class/custom-method/(.*)/(.*)', 'MyClass::customMethod/$1/$2');
Router::get('my-namespace/my-class/custom-method', '\MyNamespace\MyClass::customMethod');
Router::connect('my-callback/(.*)/(.*)', function($arg1, $arg2) {});
Router::redirect('my-redirect', 'https://test.com/');
Router::url('MyClass::customMethod', ['arg1', 'arg2']);
```