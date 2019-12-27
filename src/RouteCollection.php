<?php
/**
 * Date: 2019-09-06
 * Time: 18:02
 */

namespace Moon\Routing;


class RouteCollection implements \Countable, \IteratorAggregate
{
    protected $items = [];
    protected $names = [];

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
        return $this->items[$key];
    }

    public function add($key, $value)
    {
        $this->items[$key] = $value;
        $this->addToTree($value);
        return $this;
    }

    // todo
    public function name($name, $key)
    {
        $this->names[$key] = $name;
        return $this;
    }

    public function getTree()
    {
        return $this->tree;
    }

    public function addToTree(Route $route)
    {
        $path = $route->getPath();
        $pathArr = explode('/', $path);
        unset($pathArr[0]);
        $node = $pathArr[1];

        if (strpos($node, '{') !== false) {
            $this->tree['regex'][$route->getName()] = $route;
            return;
        }
        $res = $this->parseNode($pathArr, $route, $hasRegex);
        if ($hasRegex) {
            $this->tree['regex'][$route->getName()] = $route;
        } else {
            $this->tree['full'][$node][] = $res;
        }
    }

    protected function parseNode($pathArr, Route $route, &$hasRegex = false)
    {
        array_values($pathArr);
        $node = array_pop($pathArr);

        if (empty($pathArr)) {
            return $route;
        } else {

            if ($hasRegex || strpos($node, '{') !== false) {
                $hasRegex = true;
                return false;
            }

            $tree[$node] = $this->parseNode($pathArr, $route, $hasRegex);
        }
        return $tree;
    }
}