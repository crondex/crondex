<?php namespace Crondex\Routing;

class Router implements RouterInterface
{
    /**
     * The page uri
     *
     * @var string
     */
    protected $uri;

    /**
     * The routes array
     *
     * @var array
     */
    protected $routes = array();

    /**
     * The ParseUri object
     *
     * @var object ParseUriInterface $parseuri
     */
    protected $parseUri;

    /**
     * Constructor
     *
     * @param string $uri
     * @param object $routesObj
     * @param object $parseUriObj
     */
    public function __construct($uri, $routes, ParseUriInterface $parseUriObj)
    {
        $this->uri = $uri;
        $this->routes = $routes;
        $this->parseUri = $parseUriObj;
        $this->routeValues = array('model' => '', 'view' => '', 'controller' => '', 'action' => '', 'parameters' => '');

        //this might be better placed in Bootstrap
        $this->setRoute(); 
        $this->setRouteValues();
    }

    /**
     * Set the Route
     *
     * @return true
     */
    public function setRoute() {

        //the routes array is injected in the contstructor via the $routes object

        //if custom route exists in the routes array
        if ($this->routes->get($this->uri)) {

            //set the route, based on the $uri
            $this->route = $this->routes->get($this->uri);

            return true;
        }
    }

    /**
     * Set a route value
     *
     * @return void
     */
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
            } 
        }
    }

    /**
     * Get a route value
     *
     * @param string $value
     * @return string 
     */
    public function getRouteValue($value)
    {
        return $this->routeValues[$value];
    }
}
