<?php namespace Crondex\Routing;

interface ParseUriInterface {
    public function controller();
    public function model();    
    public function view();    
    public function action();    
    public function parameters();    
}
