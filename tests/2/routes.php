<?php
/**
 * Date: 2019-12-23
 * Time: 17:16
 */


// action also can be a Closure
use Moon\Routing\Router;

$router->get('/', function () {
    return 'Welcome ＼( ^▽^ )／';
});

//route parameter
$router->get('/hello/{name}', function ($name) {
    return 'Hello ' . $name;
});

$router->get('/login', 'UserController::login', 'login'); // name your route
$router->post('login', 'UserController::post_login');

$router->match(['get', 'post'], '{aid}/push', 'PushController::push');

//use route group
$router->group(['prefix' => 'user'], function ($router) {
    /**
     * @var Router $router
     */
    $router->post('delete/{id:\d+}', 'UserController::delete'); // {param:type}
});

$router->group(['prefix' => 'api'], function (Router $router) {
    // match GET or POST request method
    $router->get('', 'ApiController::index');
    $router->get('address/create', 'ApiController::addressCreate');
    $router->match(['get', 'POST'], '/login', 'ApiController::login');

    $router->group(['prefix' => 'user', 'namespace' => 'Api'], function (Router $router) {
        $router->get('/{id}', 'UserController::show');
        $router->post('/create', 'UserController::create');
        $router->put('/{id}', 'UserController::update');
        $router->delete('/{id}/delete', 'UserController::delete');
    });
});


// match all request method
$router->any('/other', 'ApiController::other');

$router->get('/r/test', 'TestController::test1');
$router->post('/r/test', 'TestController::test2');

//var_dump($router->getRoutes()->getTree()['full']);