# routing
一个简单的路由用法和"laravel/routing"一样简单

[README](./README.md)

通过`composer`安装

```shell
composer require heropoo/routing
```

### example:
```php
<?php

require_once './vendor/autoload.php';

use Moon\Routing\Router;

$router = new Router(null, [
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
$router->get('/hello/{name}', function($name){
    return 'Hello '.$name;
});

$router->get('/login', 'UserController::login')->name('login'); // 支持给你的路由自定义名称
$router->post('login', 'UserController::post_login');

//支持路由组
$router->group(['prefix'=>'user'], function($router){
    /**
     * @var Router $router
     */
    $router->post('delete/{id:\d+}', 'UserController::delete'); //路由参数 支持正则类型 {param:type}
});

// match GET or POST request method
$router->match(['get', 'post'], '/api', 'ApiController::index');

// match all request method
$router->any('/other', 'ApiController::other');

echo '<pre>';
var_dump($router->getRoutes());


/**
 * 匹配请求
 */

echo '$_SERVER[\'REQUEST_URI\']: ' . $_SERVER['REQUEST_URI'].PHP_EOL;
echo '$_SERVER[\'PHP_SELF\']: ' . $_SERVER['PHP_SELF'].PHP_EOL;
echo '$_SERVER[\'SCRIPT_NAME\']: ' . $_SERVER['SCRIPT_NAME'].PHP_EOL;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = substr($uri, -(strlen($uri) - strlen(dirname($_SERVER['SCRIPT_NAME']))));
$path = str_replace('//', '/', '/' . $path);
$method = $_SERVER['REQUEST_METHOD'];

echo 'path: '.$path.PHP_EOL;
echo 'method: '.$method.PHP_EOL;

/**
 * 匹配到返回一个数组, 匹配不到抛出一个异常 UrlMatchException
 * return [
 *   'route' => $route,  // Route 对象
 *   'params' => $params // array 路由参数
 * ];
 *
 */
$res = $router->dispatch($path, $method);

var_dump($res);

// 该有的都有了,调用你的控制器吧

```

Now use matched result to handle your controller's method or Closure! ＼( ^▽^ )／