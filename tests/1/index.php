<?php
/**
 * Date: 2019-09-06
 * Time: 17:57
 */

ini_set('display_errors', 'On');

require '../../vendor/autoload.php';

use Moon\Routing\Router;

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
$router->get('/hello/{name}', function($name){
    return 'Hello '.$name;
});

$router->get('/login', 'UserController::login', 'login'); // name your route
$router->post('login', 'UserController::post_login');

//use route group
$router->group(['prefix'=>'user'], function($router){
    /**
     * @var Router $router
     */
    $router->post('delete/{id:\d+}', 'UserController::delete'); // {param:type}
});

// match GET or POST request method
$router->match(['get', 'post'], '/api', 'ApiController::index');

// match all request method
$router->any('/other', 'ApiController::other');

echo '<pre>';
//var_dump($router->getRoutes());exit;


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
$res = $router->dispatch($path, $method);

var_dump($res);