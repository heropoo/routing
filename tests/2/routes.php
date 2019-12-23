<?php
/**
 * Date: 2019-12-23
 * Time: 17:16
 */


// action also can be a Closure
use Moon\Routing\Router;

$router->get('/', function(){
    return 'Welcome ＼( ^▽^ )／';
});

//route parameter
$router->get('/hello/{name}', function($name){
    return 'Hello '.$name;
});

$router->get('/login', 'UserController::login')->name('login'); // name your route
$router->post('login', 'UserController::post_login');

//use route group
$router->group(['prefix'=>'user'], function($router){
    /**
     * @var Router $router
     */
    $router->post('delete/{id:\d+}', 'UserController::delete'); // {param:type}
});

$router->group(['prefix'=> 'api'], function(Router $router){
    // match GET or POST request method
    $router->get('/', 'ApiController::index');
    $router->match(['get', 'POST'], '/login', 'ApiController::login');
});



// match all request method
$router->any('/other', 'ApiController::other');