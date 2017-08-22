# routing
A routing use "symfony/routing" and like "laravel/routing" style

To install it via `composer`

```shell
composer require heropoo/routing
```

### example:
```php
<?php
require '../vendor/autoload.php';

use Moon\Routing\Router;


$router = new Router(null, [
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
$router->get('/hello/{name}', function($name){
    return 'Hello '.$name;
})->setRequirement('name', '([\w\s\x{4e00}-\x{9fa5}]+)?');  //  Perform a regular expression match

$router->get('/login', 'UserController::login')->name('login'); // name your route
$router->post('/login', 'UserController::post_login');

//use route group 
$router->group(['prefix'=>'user'], function($router){
    /**
     * @var Router $router
     */
    $router->post('delete/{id}', 'UserController::delete');
});

// match GET or POST request method 
$router->match(['get', 'post'], '/api', 'ApiController::index');

// match all request method
$router->any('/other', 'ApiController::other');

echo '<pre>';
var_dump($router->getRoutes());


/**
 * match request
 */
$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

$path_info = $request->getPathInfo();

echo '<hr>'.$path_info.'<hr>';

$context = new \Symfony\Component\Routing\RequestContext();
$context->fromRequest($request);

//match
$matcher = new \Symfony\Component\Routing\Matcher\UrlMatcher($routes, $context);
$parameters = $matcher->match($path_info);

//match request
var_dump($parameters);

```

Now use matched result to handle your controller's method or Closure! ＼( ^▽^ )／