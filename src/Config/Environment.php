<?php namespace Crondex\Config;

/**
 * Setup environment settings
 */
class Environment implements EnvironmentInterface
{
    /**
     * @var string
     */
    protected $displayErrors;

    /**
     * @var string
     */ 
    protected $errorLogPath;

    /**
     * @var string
     */  
    protected $pageCachingState;

    /**
     * Set ini reporting
     */
    public function reporting($displayErrors, $errorLogPath)
    {
        $this->displayErrors = $displayErrors;
        $this->errorLogPath = $errorLogPath;

        ini_set('error_log', $this->errorLogPath);
   
	if ($this->displayErrors === 'on') {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
            ini_set('log_errors', 'On');
            return true;
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
            return true;
        }
        return false;
    }

    /*
     * If set to 'off', prevent page-caching
     */
    public function setHeaders($parameter) {

        if ($parameter == 'noCache') {
            header("Cache-Control: private, must-revalidate, max-age=0");
            header("Pragma: no-cache");
            header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // A date in the past
        }
    }

    /*
     * As of PHP 5.4.0 the register_globals setting has been removed and can no longer be used.
     * For PHP < 5.4.0, registered_globals should be turned off, but if not...
     */
    public function unregisterGlobals()
    {
        if (ini_get('register_globals')) {

            $superglobals = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');

            foreach ($superglobals as $globalsValue) {
                foreach ($GLOBALS[$globalsValue] as $key => $val) {

                    // If one of the values of a superglobal array (for instance a value of $_GET)
                    // is also a $GLOBAL variable, and it's value matches the variable
                    // set in the superglobal array, unset the $GLOBALS variable

                        // For instance, if $_GET['id'] is currently set to '999'
                        // and $GLOBALS['id'] is also set to '999'
                        // unset the global variable $GLOBALS['id']

                    if ($GLOBALS[$key] === $val) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
            return true;
        }
    }
}
