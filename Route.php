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

    /** @var mixed $action */
    protected $action;

    protected $regex;

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
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getPath(){
        return $this->path;
    }

    public function getAction(){
        return $this->action;
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
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

//    public function __call($name, $arguments)
//    {
//        if (strpos($name, 'set') === 0) { //set protected attribute
//            $attribute = lcfirst(substr($name, 3));
//            if (property_exists($this, $this->$attribute)) {
//                $this->$attribute = $arguments;
//                return $this;
//            }
//        }
//        throw new \BadMethodCallException('Call to undefined method ' . get_class($this) . '::' . $name . '()');
//    }
}