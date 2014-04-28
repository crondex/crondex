<?php namespace Crondex\Routing;

class ParseUri implements ParseUriInterface
{
    protected $_uri;
    protected $_uriArray = array();
    protected $_controller;
    protected $_model;
    protected $_view;
    protected $_action;
    protected $_parameters = array();

    public function __construct($uri)
    {
        $this->_uri = $uri;

        //break out $uri into an array, delimited by '/'
        $this->_uriArray = explode('/',$this->_uri);
    }

    public function controller()
    {
        if (isset($this->_uriArray[0]) && !empty($this->_uriArray[0])) {
            $this->_controller = ucfirst(strtolower($this->_uriArray[0]));
	} else {
            $this->_controller = 'Index';
        }
        $this->_controller .= 'Controller';
        return $this->_controller;
    }

    public function model()
    {
        if ($this->controller()) {
            $this->_model = str_replace('Controller','Model',$this->controller()); 
	    return $this->_model;
        }
    }

    public function view()
    {
        if ($this->controller()) {
            $this->_view = strtolower(str_replace('Controller','',$this->controller())); 
            return $this->_view;
        }
    }

    public function action()
    {
        //if action is set and not empty (it would be empty if the user types a '/' of the uri
        if (isset($this->_uriArray[1]) && !empty($this->_uriArray[1])) { 
            $this->_action = strtolower($this->_uriArray[1]);
	} else {
	    //if not set, set to default
            $this->_action = 'index';
        }
        return $this->_action;
    }

    public function parameters()
    {
        if (isset($this->_uriArray[2]) && !empty($this->_uriArray[2])) {

            //get everything except for the first two elements of $this->_uriArray
            $this->_parameters = array_slice($this->_uriArray, 2);

	    return $this->_parameters;
	}
    }
}

