<?php
/**
 * Created by PhpStorm.
 * User: ttt
 * Date: 2017/8/8
 * Time: 21:04
 */

namespace Moon\Routing;

/**
 * Class Route
 * @method string getName()
 * @method string getPath()
 * @method array getMethods()
 * @method string|\Closure getAction()
 * @method array getMiddleware()
 * @package Moon\Routing
 */
class Route
{
    public function __construct(array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    /** @var string $name */
    protected $name;

    /** @var string $path */
    protected $path;

    /** @var array $methods */
    protected $methods = [];

    /** @var string|\Closure $action */
    protected $action;

    /** @var array $regex */
    protected $regex = [];

    /** @var array $middleware */
    protected $middleware = [];

    /**
     * set name
     * @param string $name
     * @return $this
     */
    public function name($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * set methods
     * @param string | array $methods
     * @return $this
     */
    public function methods($methods)
    {
        $this->methods = (array)$methods;
        return $this;
    }

    /**
     * set middleware
     * @param string|array $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        $this->middleware = array_merge($this->middleware, (array)$middleware);
        $this->middleware = array_unique($this->middleware);
        return $this;
    }

    /**
     * set regex
     * @param string $key
     * @param string $pattern
     * @return $this
     */
    public function regex($key, $pattern)
    {
        $this->regex[$key] = $pattern;
        return $this;
    }

    public function __call($name, $arguments)
    {
        if (strpos($name, 'get') === 0) { //get protected attribute
            $attribute = lcfirst(substr($name, 3));
            if (property_exists($this, $attribute)) {
                return $this->$attribute;
            }
        }
        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $name . '()');
    }
}