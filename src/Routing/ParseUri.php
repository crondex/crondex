<?php namespace Crondex\Routing;

class ParseUri implements ParseUriInterface
{
    /**
     * Current URI, set via .htaccess
     *
     * @var string $uri;
     */
    protected $uri;

    /**
     * Constructor
     *
     * @param string $uri
     */
    public function __construct($uri)
    {
        $this->uri = $uri;

        //break out $uri into an array, delimited by '/'
        $this->uriArray = explode('/',$this->uri);
    }

    /**
     * Parse controller
     *
     * @return string
     */
    public function controller()
    {
        if (isset($this->uriArray[0]) && !empty($this->uriArray[0])) {
            $this->controller = ucfirst(strtolower($this->uriArray[0]));
	} else {
            $this->controller = 'Index';
        }
        $this->controller .= 'Controller';
        return $this->controller;
    }

    /**
     * Parse model
     *
     * @return string
     */
    public function model()
    {
        if ($this->controller()) {
            $this->model = str_replace('Controller','Model',$this->controller()); 
	    return $this->model;
        }
    }

    /**
     * Parse view
     *
     * @return string
     */
    public function view()
    {
        if ($this->controller()) {
            $this->view = strtolower(str_replace('Controller','',$this->controller())); 
            return $this->view;
        }
    }

    /**
     * Parse action
     *
     * @return string
     */
    public function action()
    {
        //if action is set and not empty (it would be empty if the user types a '/' of the uri
        if (isset($this->uriArray[1]) && !empty($this->uriArray[1])) { 
            $this->action = strtolower($this->uriArray[1]);
	} else {
	    //if not set, set to default
            $this->action = 'index';
        }
        return $this->action;
    }

    /**
     * Parse parameters
     *
     * @return array
     */
    public function parameters()
    {
        if (isset($this->uriArray[2]) && !empty($this->uriArray[2])) {

            //get everything except for the first two elements of $this->uriArray
            $this->parameters = array_slice($this->uriArray, 2);

	    return $this->parameters;
	}
    }
}

