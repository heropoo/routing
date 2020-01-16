<?php
/**
 * Date: 2019-09-06
 * Time: 18:02
 */

namespace Moon\Routing;

/**
 * Class RouteCollection
 * @package Moon\Routing
 */
class RouteCollection implements \Countable, \IteratorAggregate
{
    protected $items = [];

    protected $tree = ['full' => [], 'regex' => []];

    public function count()
    {
        return count($this->items);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    public function get($key)
    {
        if (!isset($this->items[$key])) {
            throw new \InvalidArgumentException("The route named '$key' is not defined.");
        }
        return $this->items[$key];
    }

    public function add($key, $value)
    {
        $this->items[$key] = $value;
        $this->addToTree($value);
        return $this;
    }

    public function getTree()
    {
        return $this->tree;
    }

    public function addToTree(Route $route)
    {
        $path = $route->getPath();
        if (strpos($path, '{') !== false) {
            $this->tree['regex'][$route->getName()] = $route;
            return;
        }

        $pathArr = explode('/', $path);
        unset($pathArr[0]);

        $tree = $this->parseNode($pathArr, $route);

        $node = array_key_first($tree);

        if(isset($this->tree['full'][$node])){
            $this->tree['full'][$node] = array_merge_recursive($this->tree['full'][$node], $tree[$node]);
        }else{
            $this->tree['full'][$node] = $tree[$node];
        }
    }

    protected function parseNode($pathArr, Route $route)
    {
        $tree = [];
        $node = array_shift($pathArr);
        if (empty($pathArr)) {
            $tree[$node][] = $route;
            return $tree;
        }
        $tree[$node] = $this->parseNode($pathArr, $route);
        return $tree;
    }
}