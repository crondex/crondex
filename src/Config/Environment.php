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
}
