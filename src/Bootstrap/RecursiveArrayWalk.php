<?php namespace Crondex\Bootstrap;

/*
 * Applys function to value
 * If array, recursively applys function to all values in array
 */

class RecursiveArrayWalk implements RecursiveArrayWalkInterface
{
    protected $_value;
    protected $_function;
    protected $_values;

    public function setValue($value, $function)
    {
        $this->_value = $value;
        $this->_function = $function;
        $this->_values = $this->traverse($this->_value, $this->_function);
    }

    protected function traverse($value)
    {
        if (!is_array($value)) {
            $this->_value = call_user_func($this->_function, $value);
        } else {
            $this->_value = array_map(array($this, 'traverse'), $value);
        }
        return $this->_value;
    }

    public function getValue()
    {
        return $this->_values;
    }
}

