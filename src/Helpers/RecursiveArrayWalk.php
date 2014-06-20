<?php namespace Crondex\Helpers;

/**
 * Crondex - a PHP micro framework.
 * 
 * @author      Andrew McLaughlin
 * @copyright   2014 Andrew McLaughlin <info@crondex.com>
 * @link        http://www.crondex.com
 * @license     http://www.crondex.com/license
 * @version     1.0
 * @package     crondex
 * 
 */

/** 
 * Applys a function to every element in an array. If the element
 * is an array, it recursively walks the array and calls the
 * function for each leaf node in successive arrays.
 *
 * @package crondex
 * @author  Andrew McLaughlin
 * @since   1.0
 */ 
class RecursiveArrayWalk implements RecursiveArrayWalkInterface
{
    /**
     * The array (or array element) being evalutated.
     *
     * @var array|string
     */
    protected $value;

    /**
     * The function to be called on each element
     *
     * @var string
     */
    protected $function;

    /**
     * An array containing the values after applying the function
     *
     * @var array
     */
    protected $values;

   /**
    * Creates new array with evaluated elements.
    *
    * @param array $array 
    * @param string $function
    * @return void
    */
   public function setValue($array, $function)
    {
        $this->value = $array;
        $this->function = $function;
        $this->values = $this->traverse($this->value);
    }

    /**
     * Recursively traverse the array. If a multi-demnsional
     * array is passed, the child arrays will be pass back
     * to this method.
     *
     * @param array|string $value
     * @return array|string
     */
    protected function traverse($value)
    {
        if (!is_array($value)) {
            //same as $this->function($value);
            $this->value = call_user_func($this->function, $value);
        } else {

            //if the array is a list
            if (array_values($value) == $value) {
                //call this method for each element of array
                $this->value = array_map(array($this, 'traverse'), $value);

            //else if it is an associative array
            } else {

                //loop through keys ($value is an array)
                foreach ($value as $key => $val) {

                    if (!is_int($key)) {

                        //apply function to key
                        $newKey = call_user_func($this->function, $key);

                        //set new key
                        $value[$newKey] = $value[$key];

                        //unset old key
                        unset($value[$key]);
                    }
                }

                $this->value = array_map(array($this, 'traverse'), $value);
            }
       }
        return $this->value;
    }

    /**
     * Gets the newly evaluated array.
     *
     * @param void
     * @return array
     */
    public function getValue()
    {
        return $this->values;
    }
}

