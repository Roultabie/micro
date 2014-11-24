<?php
class staticObject
{
    function __call($method, $elements) {
        $var = strtolower(substr($method, 3));
        if (!strncasecmp($method, 'get', 3)) {
            return $this->$var;
        }
        if (!strncasecmp($method, 'set', 3)) {
            $this->$var = $elements[0];
        }
    }
}

class staticStack
{
    public $stack;
    public $nbElements;

    function __construct()
    {
        $this->stack      = new StdClass();
        $this->nbElements = 0;
    }

    function getStack()
    {
        return $this->stack;
    }

    function addObject($object)
    {
        if (is_object($object)) {
            $stack       = (array)$this->stack;
            $stack[]     = $object;
            $this->stack = (object)$stack;
            $this->nbElements++;
        }
    }

    function delObject($key)
    {
        if (is_object($this->stack->$key)) {
            unset($this->stack[$key]);
            $this->nbElements--;
        }
    }
}
?>