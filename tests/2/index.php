<?php
/**
 * Date: 2019-12-23
 * Time: 17:14
 */

ini_set('display_errors', 'On');

require '../../vendor/autoload.php';

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

echo '<pre>';

require __DIR__.'/routes.php';
//echo '<hr>';
var_dump($router->getRoutes()->getTree());

//$routes = $router->getRoutes();
////var_dump($routes);
//echo '<table>';
//echo '<th>Name</th><th>Path</th><th>Methods</th><th>Action</th><th>Middleware</th>';
//foreach ($routes as $route) {
//    /** @var \Moon\Routing\Route $route */
//    echo '<tr>';
//    echo '<td>' . $route->getName() . '</td>';
//    echo '<td>' . $route->getPath() . '</td>';
//    echo '<td>' . json_encode($route->getMethods()) . '</td>';
//    echo '<td>' . json_encode($route->getAction()) . '</td>';
//    echo '<td>' . json_encode($route->getMiddleware()) . '</td>';
//    echo '</tr>';
//}
//echo '</table>';

echo '<pre>';

/**
 * match request
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
 * return [
 *   'route' => $route,  // Route
 *   'params' => $params // array
 * ];
 *
 */
//$res = $router->dispatch($path, $method);

$res = $router->dispatch($path, $method);
var_dump($res);

//todo cache routes and tree