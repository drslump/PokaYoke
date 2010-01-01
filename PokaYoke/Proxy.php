<?php
/**
 * @category   DrSlump
 * @package    Zend_View
 * @copyright  Copyright (c) 2010 Iván -DrSlump- Montes <drslump@pollinimini.net>
 * @license    New BSD License
 * @version    $Id$
 */


/**
 * Wraps the data in a Zend_View object to escape it before returning it
 *
 * @category   DrSlump
 * @package    Zend_View
 * @copyright  Copyright (c) 2010 Iván -DrSlump- Montes <drslump@pollinimini.net>
 * @license    New BSD License
 */
class Tid_Zend_View_PokaYoke_Proxy implements ArrayAccess, Iterator
{
    /** @var object */
    protected $_data;

    /** @var Zend_View_Interface */
    protected $_view;

    
    /**
     *
     * @param mixed $data
     * @param Zend_View_Interface $view
     */
    public function __construct($data, Zend_View_Interface $view)
    {
        $this->_data = $data;
        $this->_view = $view;
    }

    /**
     * Gets a view value escaped
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Sets a view value
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }

    /**
     * Checks if a property is set
     *
     * @param string $name
     * @return bool
     */
    public function  __isset($name) {
        return $this->offsetExists($name);
    }

    /**
     * Removes a property
     *
     * @param string $name
     */
    public function  __unset($name) {
        $this->offsetUnset($name);
    }

    /**
     * Calls a view helper with its result escaped
     *
     * @param string $name
     * @return mixed
     */
    public function __call($name, $args)
    {
        // If the data is an array no view helpers can be called
        if (is_array($this->_data)) {
            return null;
        }

        $callable = array($this->_data, $name);
        $value = call_user_func($callable, $args);
        return $this->_escape($value);
    }

    /**
     * Either escapes the value or wraps it in a proxy object
     *
     * @note It'll use the Zend_View object escape method
     *
     * @param mixed $value
     * @return mixed
     */
    protected function _escape($value)
    {
        if (is_string($value)) {
            return $this->_view->escape($value);
        } else if (is_object($value)) {
            return new self($value, $this->_view);
        } else if (is_array($value)) {
            return new self($value, $this->_view);
        }

        return $value;
    }

    // Implements ArrayAccess for the proxy object

    public function offsetGet($name)
    {
        $val = is_array($this->_data) ? $this->_data[$name] : $this->_data->$name;
        return $this->_escape($val);
    }

    public function offsetSet($name, $val)
    {
        if (is_array($this->_data)) {
            $this->_data[$name] = $val;
        } else {
            $this->_data->$name = $val;
        }
    }

    public function offsetExists($name)
    {
        if (is_array($this->_data)) {
            return isset($this->_data[$name]);
        } else {
            return isset($this->_data->$name);
        }
    }

    public function offsetUnset($name)
    {
        if (is_array($this->_data)) {
            unset($this->_data[$name]);
        } else {
            unset($this->_data->$name);
        }
    }

    // Implements Iterator for the proxy object

    public function current()
    {
        $value = current($this->_data);
        return $this->_escape($value);
    }

    public function next()
    {
        next($this->_data);
    }

    public function rewind()
    {
        reset($this->_data);
    }

    public function key()
    {
        return key($this->_data);
    }

    public function valid()
    {
        return key($this->_data) !== null;
    }
}


