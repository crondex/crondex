<?php namespace Crondex\Routing;

class Router implements RouterInterface
{
    protected $_uri;
    protected $_routes = array();
    protected $_route;
    protected $_routeValues = array();
    protected $_model;
    protected $_view;
    protected $_controller;
    protected $_action;
    protected $_parameters;
    protected $_parseUri;

    public function __construct($uri, $routes, ParseUriInterface $parseUriObj)
    {
        $this->_uri = $uri;
        $this->_routes = $routes;
        $this->_parseUri = $parseUriObj;
        $this->_routeValues = array('model' => '', 'view' => '', 'controller' => '', 'action' => '', 'parameters' => '');
        $this->setRoute(); 
        $this->setRouteValues();
    }

    public function setRoute() {

        //the routes array is injected in the contstructor via the $routes object

        //if custom route exists in the routes array
        if ($this->_routes->get($this->_uri)) {

            //set the route, based on the $uri
            $this->_route = $this->_routes->get($this->_uri);

            return true;
        }
    }

    public function setRouteValues()
    {
        foreach ($this->_routeValues as $key => $value) {
            if (isset($this->_route[$key])) {

                //this sets an associate value in $this->_routeValues
                $this->_routeValues[$key] = $this->_route[$key];

            } else {
                //if not, just parse it with the predefined convention
                //this calls the method $key in object $this->parseUri (injected via constructor)
                $this->_routeValues[$key] = call_user_func(array($this->_parseUri, $key));

                //another way to do this is to use  a variable variable, possibly quicker
                //$this->_routeValue[$key] = $this->_parseUri->{$key}(); 
            } 
        }
    }

    public function getRouteValue($value)
    {
        return $this->_routeValues[$value];
    }
}
