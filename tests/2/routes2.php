<?php
/**
 * Date: 2019-12-23
 * Time: 17:16
 */


// action also can be a Closure
use Moon\Routing\Router;

$router->get('', 'ApiController::addressCreate');

$router->group(['prefix' => 'api'], function (Router $router) {
    // match GET or POST request method
    $router->get('address/create', 'ApiController::addressCreate');
    $router->get('address/create/test', 'ApiController::addressCreate');
});

//var_dump($router->getRoutes()->getTree()['full']);