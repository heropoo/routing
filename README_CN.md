# routing
一个简单快速的路由

[README](./README.md)

[![Latest Stable Version](https://poser.pugx.org/heropoo/routing/v/stable)](https://packagist.org/packages/heropoo/routing)
[![Total Downloads](https://poser.pugx.org/heropoo/routing/downloads)](https://packagist.org/packages/heropoo/routing)
[![License](https://poser.pugx.org/heropoo/routing/license)](https://packagist.org/packages/heropoo/routing)

## 安装
通过`composer`安装
```sh
composer require heropoo/routing
```

## 特性
- 支持restful风格路由
- 支持路由组以及给组定义各种属性（例如：控制器命名空间、中间件、前缀等）
- 支持路由参数以及参数类型限制
- 支持正则表单式 
- 使用树结构存储，匹配速度快

## example:
```php
<?php

require_once './vendor/autoload.php';

use Moon\Routing\Router;
use Moon\Routing\UrlMatchException;

$router = new Router([
    'namespace'=>'app\\controllers',    //支持控制器命名空间
    'middleware'=>[                     //支持中间件
        'startSession',
        'verifyCSRFToken',
        'auth'
    ],
    'prefix'=>''                        //支持前缀
]);

// 方法action可以是一个匿名函数
$router->get('/', function(){
    return 'Welcome ＼( ^▽^ )／';
});

//支持路由参数
$router->get('/hello/{name}', function($name){ //自动匹配路由参数赋予匿名函数
    return 'Hello '.$name;
});

$router->get('/login', 'UserController::login', 'login'); // 支持给你的路由自定义名称
$router->post('login', 'UserController::post_login');

//支持路由组
$router->group(['prefix'=>'user'], function(Router $router){
    $router->post('delete/{id:\d+}', 'UserController::delete'); //路由参数 支持正则类型 {param:type}
});

// 匹配指定的http请求方法，如： GET or POST
$router->match(['get', 'post'], '/api', 'ApiController::index');

// 匹配所有的http请求方法
$router->any('/other', 'ApiController::other');

// 获取所有路由
var_dump($router->getRoutes());

/**
 * 匹配请求
 * @param string $path 请求路径 如： /user/list
 * @param string $method 请求方法 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS''GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'
 * @return array 匹配到返回一个数组, 匹配不到抛出一个异常 UrlMatchException
 * return [
 *   'route' => $route,  // Route 对象
 *   'params' => $params // array 路由参数
 * ];
 *
 */
$res = $router->dispatch($path, $method);

// 该有的都有了,调用你的控制器吧

```

Now use matched result to handle your controller's method or Closure! ＼( ^▽^ )／

## 赞助商

<a href="https://www.jetbrains.com/?from=heropoo/routing"><img src="https://www.ioio.pw/assets/jetbrains-blackandwhite.png" height=100 alt="Jetbrains" title="Jetbrains"></a>

