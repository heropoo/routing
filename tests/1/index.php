<?php
/**
 * Date: 2019-09-06
 * Time: 17:57
 */

require_once __DIR__ . '/../../Route.php';
require_once __DIR__ . '/../../Router.php';
require_once __DIR__ . '/../../RouteCollection.php';

use Moon\Routing\Router;

$router = new Router(null, [
    'namespace' => 'app\\controllers',
    'middleware' => [
        'csrfFilter', 'sessionStart'
    ],
    'prefix' => 'demo'
]);

$router->get('/', function () {
    return 'index';
});

//$router->get('/home/{name}', 'IndexController::home')->setRequirement('name', '([\w\s\x{4e00}-\x{9fa5}]+)?');
$router->get('/login', 'IndexController::login')->name('login');
$router->post('/login', 'IndexController::post_login');

$res = $router->any('api', 'ApiController::index')->middleware(['api.auth', 'api.oauth']);

$router->group(['prefix' => 'admin/', 'middleware' => 'auth', 'namespace' => 'admin'], function ($router) {
    $router->post('/login', 'AdminController::login');
});

$router->group(['prefix' => 'admin2/', 'middleware' => 'auth2', 'namespace' => 'admin2'], function (Router $router) {
    $router->post('/login', 'Admin2Controller::login');
    $router->group(['prefix' => 'admin3/', 'middleware' => 'auth3', 'namespace' => 'admin3'], function (Router $router) {
        $router->post('/login', 'Admin3Controller::login');
        $router->post('/logout', 'Admin3Controller::login');
    });
});

var_dump($res);

$routes = $router->getRoutes();
//var_dump(count($routes));
echo '<table>';
echo '<th>Name</th><th>Path</th><th>Methods</th><th>Action</th><th>Middleware</th>';
foreach ($routes as $route) {
    /** @var \Moon\Routing\Route $route */
    //var_dump($route->getName().'|'.$route->getPath().'|'.json_encode($route->getMethods()).'|'.json_encode($route->getMiddleware()));
    echo '<tr>';
    echo '<td>' . $route->getName() . '</td>';
    echo '<td>' . $route->getPath() . '</td>';
    echo '<td>' . json_encode($route->getMethods()) . '</td>';
    echo '<td>' . json_encode($route->getAction()) . '</td>';
    echo '<td>' . json_encode($route->getMiddleware()) . '</td>';
    echo '</tr>';
}
echo '</table>';

echo 'Memory used: '.(memory_get_usage()/1024).'KB';