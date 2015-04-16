<?php namespace Crondex;

/**
 * Crondex - a PHP micro framework.
 * 
 * @author      Andrew McLaughlin
 * @copyright   2015 Andrew McLaughlin <info@crondex.com>
 * @link        http://www.crondex.com
 * @license     http://www.crondex.com/license
 * @version     1.0
 * @package     crondex
 *  
 */ 

use Crondex\Config\Config;
use Crondex\Config\Environment;
use Crondex\View\View;
use Crondex\Security\Random;
use Crondex\Routing\ParseUri;
use Crondex\Routing\Router;
use Crondex\Session\SessionManager;
use Crondex\Html\Sanitize;
use Crondex\Helpers\Msg;
use CrondexAuth\Auth;
use Exception;

class Bootstrap {

    /**
     * Path for config file
     *
     * @var string $configFilePath
     */
    protected $configFilePath;

    /**
     * Path for config file
     *
     * @var string $routesFilePath
     */ 
    protected $routesFilePath;

    /**
     * Constructor
     *
     * @param string $configFilePath
     * @param string $routesFilePath
     */
    public function __construct($configFilePath, $routesFilePath)
    {
        try {

            /**
             * Instantiate config
             */
            $config = new Config($configFilePath);

            /**
             * Instantiate routes
             */
            $routesObj = new Config($routesFilePath);

            /**
             * Instantiate Environment, configs for caching, logging, etc.
             */
            $envObj = new Environment; 
            $envObj->reporting($config->get('displayErrors'), $config->get('errorLogPath')); //replace this with monolog
           
            /**
             * Get the URI
             * This is set via public/.htaccess
             */
            $uri = $_GET['uri'];

            /**
             * Instantiate URI parser
             */
            $parseUriObj = new ParseUri($uri);

            /**
             * Instantiate Router
             */
            $router = new Router($uri, $routesObj, $parseUriObj);

            /**
             * Get model
             */
            $model = $router->getRouteValue('model');

            /**
             * Get controller
             */
            $controller = $router->getRouteValue('controller');

            /**
             * Get action
             */
            $action = $router->getRouteValue('action');

            /**
             * Get view
             */
            $view = $router->getRouteValue('view');

            /**
             * Get parameters
             */
            $parameters = $router->getRouteValue('parameters');

            /*
             * Instantiate random token generator
             */
            $randomObj = new Random;

            /**
             * Instantiate session handler
             */
            $sessionManagerObj = new SessionManager($config);

            /**
             * Set a custom session handler
             */
            session_set_save_handler($sessionManagerObj);

            /* 
             * Instantiate auth manager and check auth/session
             */
            if ($config->get('auth') === 'on') {
                $authObj = new Auth($config,$randomObj,$sessionManagerObj);

                //this needs to be broken out and either added to the Auth class or an auth model
                if (isset($_SESSION['user_id'])) {
                    if ($authObj->check($_SESSION['user_id'])) {
                        $authObj->getLoggedInUserDetails($_SESSION['user_id']);
                    }
                }

            } else {
                $authObj = NULL;
            }

            /**
             * Debugging -> this one's on the chopping block;
             */
            $msgObj = new Msg;
            $msgObj->debug = $config->get('msg_debug');

            /**
             * Checks to see if method $action (in class $controller) exists
             *
             * @param string $controller
             * @param string $action
             */
            if ((int)method_exists($controller, $action)) {
 
                /**
                 * Instantiate model
                 */
                $modelObj = new $model($config,$randomObj,$authObj,$msgObj,$envObj);

                /**
                 * Instantiate view (template)
                 */ 
                $viewObj = new View($view,$action);

                /**
                 * This instantiates the crondex object (which is an instance of $controller (the subcontroller),
                 * which extends Controller (the main/front controller) and also injects model and view objects
                 */
                $crondex = new $controller($modelObj,$viewObj);

                if (!is_array($parameters)) {

                    /**
                     * Call the method $action of object $crondex and pass paramenter $parameters
                     *
                     * @param object $crondex
                     * @param string $action
                     * @param string $parameters
                     *
                     * This is the same as: $crondex->$action($parameters);
                     */
                    call_user_func(array($crondex,$action),$parameters);

                } else {

                    /**
                     * Call the method $action of object $crondex and pass paramenters as array $parameters
                     *
                     * @param object $crondex
                     * @param string $action
                     * @param array $parameters
                     */
                    call_user_func_array(array($crondex,$action),$parameters);
                }

            /**
             * Method $action in $controller doesn't exist
             */
            } else {
                /**
                 * Page not found, throw 404
                 */
                throw new Exception('404');	
            } 

        /**
         * Catch any errors thrown during bootstrapping
         */
        } catch (Exception $Exception) {

            /**
             * 404 Error
             */
            if ($Exception->getMessage() === '404') {
                header("HTTP/1.0 404 Not Found");
                include (ROOT . DS . 'app' . DS . 'views' . DS . '404' . DS . 'index.php');
                exit();
            }
        }
    }
}

