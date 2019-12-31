<?php
/**
 * Date: 2019-09-06
 * Time: 17:57
 */

$start_time = microtime(true);

ini_set('display_errors', 'On');

require '../../vendor/autoload.php';
require_once __DIR__ . '/TestController.php';

use Moon\Routing\Router;

$router = new Router();
$router->get('', 'SiteController::index');

$router = new Router([
    'namespace' => 'app\\controllers',
    'middleware' => [
        'csrfFilter', 'sessionStart'
    ],
    'prefix' => ''
], $router->getRoutes());

$router->get('/', function () {
    return 'index';
});

$router->get('/home/{name}', 'IndexController::home');
$router->get('/login', 'IndexController::login', 'login');
$router->post('/login', 'IndexController::post_login');

$res = $router->any('api/{aaa}', 'ApiController::index')->middleware(['api.auth', 'api.oauth']);
$res = $router->get('api/{version}/user/{id:\d+}', 'ApiControllers\\UserController::user')->middleware(['api.auth', 'api.oauth']);

$router->group(['prefix' => 'admin/', 'middleware' => 'auth', 'namespace' => 'admin'], function (Router $router) {
    $router->post('/login', 'AdminController::login');
});

$router->group(['prefix' => 'admin2/', 'middleware' => 'auth2', 'namespace' => 'admin2'], function (Router $router) {
    $router->post('/login', 'Admin2Controller::login');
    $router->group(['prefix' => 'api/', 'middleware' => 'auth3', 'namespace' => 'api'], function (Router $router) {
        $router->post('/login', 'Admin3Controller::login');
        $router->post('/logout', 'Admin3Controller::login');
    });
});

$router->get('/test/{name}', 'TestController::home');
$router->controller('test', 'TestController');


$routes = $router->getRoutes();
//var_dump(count($routes));
echo '<table>';
echo '<th>Name</th><th>Path</th><th>Methods</th><th>Action</th><th>Middleware</th>';
foreach ($routes as $route) {
    /** @var \Moon\Routing\Route $route */
    echo '<tr>';
    echo '<td>' . $route->getName() . '</td>';
    echo '<td>' . $route->getPath() . '</td>';
    echo '<td>' . json_encode($route->getMethods()) . '</td>';
    echo '<td>' . json_encode($route->getAction()) . '</td>';
    echo '<td>' . json_encode($route->getMiddleware()) . '</td>';
    echo '</tr>';
}
echo '</table>';

echo '<hr>Memory used: ' . (memory_get_usage() / 1024) . 'KB<br>';
echo 'Time used: ' . (microtime(true) - $start_time) . 's<br>';

echo '<hr>$_SERVER[\'REQUEST_URI\']: ' . $_SERVER['REQUEST_URI'] . '<br>';
echo '$_SERVER[\'PHP_SELF\']: ' . $_SERVER['PHP_SELF'] . '<br>';
echo '$_SERVER[\'SCRIPT_NAME\']: ' . $_SERVER['SCRIPT_NAME'] . '<br>';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = substr($uri, -(strlen($uri) - strlen(dirname($_SERVER['SCRIPT_NAME']))));
$path = str_replace('//', '/', '/' . $path);
$method = $_SERVER['REQUEST_METHOD'];

echo 'path: ' . $path . '<br>';
echo 'method: ' . $method . '<br>';

echo '<hr>Memory used: ' . (memory_get_usage() / 1024) . 'KB<br>';
echo 'Time used: ' . (microtime(true) - $start_time) . 's<br>';

$res = $router->dispatch($path, $method);
echo '<hr>';
var_dump($res);

echo '<hr>Memory used: ' . (memory_get_usage() / 1024) . 'KB<br>';
echo 'Time used: ' . (microtime(true) - $start_time) . 's<br>';