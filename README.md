# routing
A simple and fast route.Use tree structure storage, fast matching! 一个简单快速的路由，使用树形结构存储匹配更快！

[中文说明](./README_CN.md)

[![Latest Stable Version](https://poser.pugx.org/heropoo/routing/v/stable)](https://packagist.org/packages/heropoo/routing)
[![Total Downloads](https://poser.pugx.org/heropoo/routing/downloads)](https://packagist.org/packages/heropoo/routing)
[![License](https://poser.pugx.org/heropoo/routing/license)](https://packagist.org/packages/heropoo/routing)

## install
To install it via `composer`
```sh
composer require heropoo/routing
```

## feature
- Support restful style route
- Support route group and add some attributes (like namespace,middleware,prefix..)
- Support route params and limit param's type
- Support regex
- Use tree structure storage, fast matching! 

## example:
```php
<?php

require_once './vendor/autoload.php';

use Moon\Routing\Router;
use Moon\Routing\UrlMatchException;

$router = new Router([
    'namespace'=>'app\\controllers',    //support controller namespace
    'middleware'=>[                     //support middleware
        'startSession',
        'verifyCSRFToken',
        'auth'
    ],
    'prefix'=>''                        //support prefix
]);

// action also can be a Closure
$router->get('/', function(){
    return 'Welcome ＼( ^▽^ )／';
});

//route parameter
$router->get('/hello/{name}', function($name){ // auto pick route param to Closure 
    return 'Hello '.$name;
});

$router->get('/login', 'UserController::login', 'login'); // name your route
$router->post('login', 'UserController::post_login');

//use route group
$router->group(['prefix'=>'user'], function(Router $router){
    $router->post('delete/{id:\d+}', 'UserController::delete'); // {param:type pattern}
});

// match GET or POST request method
$router->match(['get', 'post'], '/api', 'ApiController::index');

// match all request method
$router->any('/other', 'ApiController::other');

// get all routes
var_dump($router->getRoutes());

/**
 * match request
 * @param string $path Request path, eg： /user/list
 * @param string $method Request method, 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS''GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'
 * @return array If not matched throw a UrlMatchException
 * return [
 *   'route' => $route,  // Route
 *   'params' => $params // array
 * ];
 *
 */
$res = $router->dispatch($path, $method);

var_dump($res);

```

Now use matched result to handle your controller's method or Closure! ＼( ^▽^ )／

## Tests
```
# Unix like OS
./vendor/bin/phpunit
# Windows
.\vendor\bin\phpunit
```

## Sponsor

<a href="https://www.jetbrains.com/?from=heropoo/routing"><img src="https://www.ioio.pw/static-assets/jetbrains-blackandwhite.png" height=100 alt="Jetbrains" title="Jetbrains"></a>
