<?php
/**
 * User: Heropoo
 * Date: 2017/8/8
 * Time: 17:33
 */

namespace Moon\Routing;

/**
 * Class Router
 * @method Route get(string $path, string|\Closure $action, string $name = null)
 * @method Route head(string $path, string|\Closure $action, string $name = null)
 * @method Route post(string $path, string|\Closure $action, string $name = null)
 * @method Route put(string $path, string|\Closure $action, string $name = null)
 * @method Route patch(string $path, string|\Closure $action, string $name = null)
 * @method Route delete(string $path, string|\Closure $action, string $name = null)
 * @method Route options(string $path, string|\Closure $action, string $name = null)
 * @package Moon\Routing
 */
class Router
{
    /**
     * All of the verbs supported by the router.
     *
     * @var array
     */
    //const VERBS = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']; // php version >= 5.6
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var array
     * [
     *      'namespace'=>'app\\controllers',    //support controller namespace
     *      'middleware'=>[                     //support middleware
     *          'startSession',
     *           'verifyCSRFToken',
     *           'auth'
     *      ],
     *      'prefix'=>'api'                     //support prefix
     * ]
     */
    protected $attributes = [];

    protected $actionSeparation = '::';         //if you like laravel style you can set it '@'
    protected $actionSuffix = 'Action';         //

    /**
     * Router constructor.
     * @param RouteCollection|null $routes
     * @param array $attributes
     */
    public function __construct(array $attributes = [], RouteCollection $routes = null)
    {
        $this->attributes = $attributes;
        $this->routes = $routes ? $routes : new RouteCollection();
    }

    /**
     * @param string $separation
     * @return $this
     */
    public function setActionSeparation($separation)
    {
        $this->actionSeparation = $separation;
        return $this;
    }

    /**
     * @param string $suffix
     * @return $this
     */
    public function setActionSuffix($suffix)
    {
        $this->actionSuffix = $suffix;
        return $this;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string|array $methods
     * @param string $path
     * @param string|\Closure $action
     * @param string $name
     * @return Route
     */
    public function match($methods, $path, $action, $name = null)
    {
        $methods = array_map(function ($method) {
            return strtoupper($method);
        }, $methods);
        return $this->addRoute($path, $methods, $action);
    }

    /**
     * @param string $path
     * @param string|\Closure $action
     * @param string $name
     * @return Route
     */
    public function any($path, $action, $name = null)
    {
        if (is_null($name)) {
            $name = 'ANY:' . $path;
        }
        return $this->addRoute($path, static::$verbs, $action, $name);
    }

    /**
     * @param array $attributes
     * @param \Closure $callback
     */
    public function group($attributes, \Closure $callback)
    {
//        $router = clone $this;
//        $router->mergeAttributes($attributes);
//        $callback($router);
//        unset($router);

        //Same effect as above
        $preAttributes = $this->attributes;
        $this->mergeAttributes($attributes);
        $callback($this);
        $this->attributes = $preAttributes;
    }

    /**
     * @param string $path
     * @param string|array $methods
     * @param string|\Closure $action
     * @param string $name
     * @return Route
     */
    public function createRoute($path, $methods, $action, $name = null)
    {
        if (isset($this->attributes['prefix'])) {
            $path = $this->attributes['prefix'] . '/' . $path;
        }

        $path = strpos($path, '/') === 0 ? $path : '/' . $path;
        $path = str_replace('//', '/', $path);

        if ($action instanceof \Closure) {
            $action = $action->bindTo(null, null); // not bind $this
        } else if (isset($this->attributes['namespace'])) {
            $action = "\\" . trim($this->attributes['namespace'] . "\\" . trim($action, "\\"), "\\");
            $action = str_replace('\\\\', '\\', $action);
        }

        if (is_null($name)) {
            //$name = md5(implode('.', (array)$methods) . '.' . $path);
            $name = implode('.', (array)$methods) . ':' . $path;
        }

        $route = new Route([
            'name' => $name,
            'path' => $path,
            'methods' => (array)$methods,
            'action' => $action
        ]);

        if (isset($this->attributes['middleware'])) {
            $route->middleware($this->attributes['middleware']);
        }

        return $route;
    }

    /**
     * @param string $path
     * @param string|array $methods
     * @param string|\Closure $action
     * @param string $name
     * @return Route
     */
    public function addRoute($path, $methods, $action, $name = null)
    {
        $route = $this->createRoute($path, $methods, $action, $name);
        $this->routes->add($route->getName(), $route);

        return $route;
    }

    /**
     * @param string $path
     * @param string $controller
     */
    public function controller($path, $controller)
    {
        $controllerClass = isset($this->attributes['namespace'])
            ? $this->attributes['namespace'] . '\\' . $controller : $controller;

        if (!class_exists($controllerClass)) {
            throw new \InvalidArgumentException("Class $controllerClass is not found.");
        }

        $ref = new \ReflectionClass($controllerClass);
        foreach ($ref->getMethods() as $reflectionMethod) {
            /** @var \ReflectionMethod $reflectionMethod */
            $name = $reflectionMethod->getName();
            if ($reflectionMethod->isPublic()
                && strlen($name) > strlen($this->actionSuffix)
                && strrchr($name, $this->actionSuffix) == $this->actionSuffix
            ) {
                $method = substr($name, 0, -strlen($this->actionSuffix));
                $sub_path = $this->convertUppercaseToDash($method);
                $this->any($path . '/' . $sub_path, $controller . $this->actionSeparation . $name, trim($path . '.' . $sub_path, '/'));
            }
        }
    }

    public function resource($path, $controller)
    {
        $path = trim($path, '/');
        $this->addRoute($path, ['GET'], $controller . $this->actionSeparation . 'index', $path . '.index');
        $this->addRoute($path . '/create', ['GET'], $controller . $this->actionSeparation . 'create', $path . '.create');
        $this->addRoute($path, ['POST'], $controller . $this->actionSeparation . 'store', $path . '.store');
        $this->addRoute($path . '/{id}', ['GET'], $controller . $this->actionSeparation . 'show', $path . '.show');
        $this->addRoute($path . '/{id}/edit', ['GET'], $controller . $this->actionSeparation . 'edit', $path . '.edit');
        $this->addRoute($path . '/{id}', ['PUT', 'PATCH'], $controller . $this->actionSeparation . 'update', $path . '.update');
        $this->addRoute($path . '/{id}', ['DELETE'], $controller . $this->actionSeparation . 'destroy', $path . '.destroy');
    }

    protected function convertUppercaseToDash($str)
    {
        //$str = str_replace("_", "-", $str);
        $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
            return '-' . strtolower($matches[0]);
        }, $str);
        return ltrim($str, "-");
    }

    /**
     * @param array $attributes
     */
    protected function mergeAttributes($attributes)
    {
        if (isset($attributes['prefix'])) {
            $prefix = isset($this->attributes['prefix']) ? $this->attributes['prefix'] : '/';
            $prefix = $prefix . '/' . trim($attributes['prefix'], '/');
            $this->attributes['prefix'] = $prefix;
        }

        if (isset($attributes['namespace'])) {
            $namespace = isset($this->attributes['namespace']) ? $this->attributes['namespace'] : '\\';
            $namespace = $namespace . '\\' . trim($attributes['namespace'], '\\');
            $this->attributes['namespace'] = $namespace;
        }

        if (isset($attributes['middleware'])) {
            $middleware = isset($this->attributes['middleware']) ? $this->attributes['middleware'] : [];
            $middleware = array_merge($middleware, (array)$attributes['middleware']);
            $middleware = array_unique($middleware);
            $this->attributes['middleware'] = $middleware;
        }
    }

    /**
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param string $name
     * @return null|Route
     */
    public function getRoute($name)
    {
        return $this->routes->get($name);
    }

    /**
     * Dispatch
     * @param string $path
     * @param string $method
     * @return array
     * @throws UrlMatchException
     */
    public function simpleDispatch($path, $method)
    {
        foreach ($this->routes as $route) {
            /** @var Route $route */
            if (in_array($method, $route->getMethods())) {
                $pattern = "#^{$route->getPath()}$#U";
                $param_keys = [];
                //var_dump("#({.*?})#", $route->getPath());
                if ($res = preg_match_all("#({.*?})#", $route->getPath(), $matches)) {
                    //var_dump($matches);
                    foreach ($matches[0] as $v) {
                        $tmp = explode(':', substr($v, 1, strlen($v) - 2));
                        $param_key = $tmp[0];
                        if (count($tmp) > 2) {
                            unset($tmp[0]);
                            $tmp[1] = implode(':', $tmp);
                        }
                        //$patterns[$tmp[0]] = $tmp[1];
                        $param_pattern = isset($tmp[1]) ? '(' . $tmp[1] . ')' : '([^/]+)'; // default param pattern
                        $pattern = str_replace($v, $param_pattern, $pattern);
                        $param_keys[] = $param_key;
                    }
                }
//                echo $pattern;
                if (@preg_match($pattern, $path, $matches)) {
//                    var_dump($matches);
                    unset($matches[0]);
                    $params = [];
                    foreach ($matches as $v) {
                        $key = array_shift($param_keys);
                        $params[$key] = $v;
                    }
                    return [
                        'route' => $route,
                        'params' => $params
                    ];
                }
            }
        }
        throw new UrlMatchException('No Route Matched for path: "' . $path . '"', 404);
    }

    /**
     * Dispatch
     * @param string $path
     * @param string $method
     * @return array
     * @throws UrlMatchException
     */
    public function dispatch($path, $method)
    {
        $pathArr = explode('/', $path);
        unset($pathArr[0]);

        $value = $this->routes->getTree()['full'];
        for ($i = 1; $i <= count($pathArr); $i++) {
            $k = $pathArr[$i];
            if (!isset($value[$k])) {
                $value = false;
                break;
            }
            $value = $value[$k];
        }

        $pathMatchedRoute = false;
        if (is_array($value) && isset($value[0])) {
            for ($i = 0; $i < count($value); $i++) {
                $route = isset($value[$i]) ? $value[$i] : null;
                //var_dump($route);exit;
                if ($route instanceof Route) {
                    if (in_array($method, $route->getMethods())) {
                        return [
                            'route' => $route,
                            'params' => [],
                            'match_by_tree' => true // for debug
                        ];
                    } else {
                        $pathMatchedRoute = $route;
                    }
                }
            }
        }

        if ($pathMatchedRoute) {
            throw new UrlMatchException('Method not allow for path: "' . $path . '"', 405);
        }

        // fall back to regex match
        return $this->dispatchRegex($path, $method);
    }

    /**
     * Dispatch by regex
     * @param string $path
     * @param string $method
     * @return array
     * @throws UrlMatchException
     */
    protected function dispatchRegex($path, $method)
    {
        foreach ($this->routes->getTree()['regex'] as $route) {
            /** @var Route $route */
            if (in_array($method, $route->getMethods())) {
                $pattern = "#^{$route->getPath()}$#U";
                $param_keys = [];
                //var_dump("#({.*?})#", $route->getPath());
                if ($res = preg_match_all("#({.*?})#", $route->getPath(), $matches)) {
                    //var_dump($matches);
                    foreach ($matches[0] as $v) {
                        $tmp = explode(':', substr($v, 1, strlen($v) - 2));
                        $param_key = $tmp[0];
                        if (count($tmp) > 2) {
                            unset($tmp[0]);
                            $tmp[1] = implode(':', $tmp);
                        }
                        //$patterns[$tmp[0]] = $tmp[1];
                        $param_pattern = isset($tmp[1]) ? '(' . $tmp[1] . ')' : '([^/]+)'; // default param pattern
                        $pattern = str_replace($v, $param_pattern, $pattern);
                        $param_keys[] = $param_key;
                    }
                }
//                echo $pattern;
                if (@preg_match($pattern, $path, $matches)) {
//                    var_dump($matches);
                    unset($matches[0]);
                    $params = [];
                    foreach ($matches as $v) {
                        $key = array_shift($param_keys);
                        $params[$key] = $v;
                    }
                    return [
                        'route' => $route,
                        'params' => $params
                    ];
                }
            }
        }
        throw new UrlMatchException('No Route Matched for path: "' . $path . '"', 404);
    }

    public function __call($name, $arguments)
    {
        $method = strtoupper($name);
        if (in_array($method, static::$verbs)) {
            if (count($arguments) < 2) {
                throw new \InvalidArgumentException('Too few arguments to function ' . get_class($this) . '::' . $name . '()');
            }
            $path = $arguments[0];
            $action = $arguments[1];
            $route_name = isset($arguments[2]) ? $arguments[2] : null;
            return $this->addRoute($path, $method, $action, $route_name);
        }
        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $name . '()');
    }
}
