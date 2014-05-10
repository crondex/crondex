<?php namespace Crondex\Routing;

class Router implements RouterInterface
{
    protected $uri;
    protected $routes = array();
    protected $route;
    protected $routeValues = array();
    protected $model;
    protected $view;
    protected $controller;
    protected $action;
    protected $parameters;
    protected $parseUri;

    public function __construct($uri, $routes, ParseUriInterface $parseUriObj)
    {
        $this->uri = $uri;
        $this->routes = $routes;
        $this->parseUri = $parseUriObj;
        $this->routeValues = array('model' => '', 'view' => '', 'controller' => '', 'action' => '', 'parameters' => '');
        $this->setRoute(); 
        $this->setRouteValues();
    }

    public function setRoute() {

        //the routes array is injected in the contstructor via the $routes object

        //if custom route exists in the routes array
        if ($this->routes->get($this->uri)) {

            //set the route, based on the $uri
            $this->route = $this->routes->get($this->uri);

            return true;
        }
    }

    public function setRouteValues()
    {
        foreach ($this->routeValues as $key => $value) {
            if (isset($this->route[$key])) {

                //this sets an associate value in $this->routeValues
                $this->routeValues[$key] = $this->route[$key];

            } else {
                //if not, just parse it with the predefined convention
                //this calls the method $key in object $this->parseUri (injected via constructor)
                $this->routeValues[$key] = call_user_func(array($this->parseUri, $key));

                //another way to do this is to use  a variable variable, possibly quicker
                //$this->routeValue[$key] = $this->parseUri->{$key}(); 
            } 
        }
    }

    public function getRouteValue($value)
    {
        return $this->routeValues[$value];
    }
}
