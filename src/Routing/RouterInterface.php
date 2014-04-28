<?php namespace Crondex\Routing;

interface RouterInterface {
    public function setRoute();
    public function setRouteValues();    
    public function getRouteValue($value);    
}
