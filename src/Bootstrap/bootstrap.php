<?php

use Crondex\Config\Config;
use Crondex\View\View;
use Crondex\Security\Random;
use Crondex\Routing\ParseUri;
use Crondex\Routing\Router;
use Crondex\Session\SessionManager;
use Crondex\Auth\Auth;
use Crondex\Log\Msg;
use Crondex\Html\Clean;
use Crondex\Bootstrap\RecursiveArrayWalk;
use Crondex\Bootstrap\RemoveMagicQuotes;

setReporting();
//removeMagicQuotes();
unregisterGlobals();
noCache();

//print headers
//foreach (getallheaders() as $name => $value) {
//    echo "$name: $value\n";
//}

try {

    //remove magic quotes
    $recursiveArrayWalk = new RecursiveArrayWalk;
    $removeMagicQuotes = new RemoveMagicQuotes($recursiveArrayWalk);

    $_GET = $removeMagicQuotes->removeQuotes($_GET);
    $_POST = $removeMagicQuotes->removeQuotes($_POST);
    $_COOKIE = $removeMagicQuotes->removeQuotes($_COOKIE);

    //this is set by the page uri via public/.htaccess
    $uri = $_GET['uri'];

    //inject these paths when I make bootstrap a class;
    $config = new Config(ROOT . DS . 'app' . DS . 'config' . DS . 'main.ini');
    $routes = new Config(ROOT . DS . 'app' . DS . 'config' . DS . 'routes.ini');
    $parseUriObj = new ParseUri($uri);
    $router = new Router($uri, $routes, $parseUriObj);

    $model = $router->getRouteValue('model');
    $controller = $router->getRouteValue('controller');
    $action = $router->getRouteValue('action');
    $view = $router->getRouteValue('view');
    $parameters = $router->getRouteValue('parameters');

    $randomObj = new Random; //this is from PHPass

    //instantiate session handler
    $sessionManager = new SessionManager($config);
    session_set_save_handler($sessionManager);

    //instatiate auth manager and check auth/session
    if ($config->get('auth') === 'on') {
        $authObj = new Auth($config,$randomObj);
        $authObj->check();
    } else {
        $authObj = NULL;
    }

    //debugging
    $msgObj = new Msg;
    $msgObj->debug = $config->get('msg_debug');

    //Checks to see if method $action (in class $controller) exists
    if ((int)method_exists($controller, $action)) {
 
        //instantiate model
        $modelObj = new $model($config,$randomObj,$authObj,$msgObj);

        //instantiate view (template)
        $viewObj = new View($view,$action);

        //instantiate input cleaner
        $clean = new Clean;

        //This instantiates the crondex object (which is an instance of $controller (the subcontroller),
        //which extends Controller (the main/front controller) and also injects model and view objects
        $crondex = new $controller($modelObj,$model,$viewObj);

        if (!is_array($parameters)) {

            //call the method $action of object $crondex and pass paramenter $parameters
            //same as: $crondex->$action($parameters);
            call_user_func(array($crondex,$action),$parameters);

        } else {

            //call the method $action of object $crondex and pass paramenters as array $parameters
            call_user_func_array(array($crondex,$action),$parameters);
        }

    //method $action in $controller doesn't exist
    //page not found
    } else {
        throw new Exception('404');	
    } 

} catch (Exception $Exception) {

    if ($Exception->getMessage() === '404') {
        throw404();
    }
}

