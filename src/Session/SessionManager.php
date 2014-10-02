<?php namespace Crondex\Session;

/** This class extends SessionHandler, which exposes the internal PHP session save handler.
  * This allows you to override the methods, intercept, or filter them by calls to the
  * parent class methods which ultimately wrap the interal PHP session handlers. 
  *
  * I am modifying it specifically to encrypt the session data, and to set basic
  * cookie and session parameters.
  *
  * In the future, if you write your own custom save handlers, implement the
  * SessionHandlerInterface interface instead of extending from SessionHandler. 
  * See: http://www.php.net/manual/en/class.sessionhandlerinterface.php
  *
  * However, the constructor would remain mostly the same.
  */

class SessionManager extends \SessionHandler
{

    public $config;

    //uncomment next line if encrypting data
    //private $sessionKey;

    //remove '= null' from the constructor argument if injecting key

    public function __construct($config, $sessionKey = null, $maxlife = 1440)
    {
        session_set_cookie_params(0,'/',$config->get('cookie_domain'),false,true);
        ini_set('session.gc_maxlifetime', $maxlife);
        session_name('CRONDEXSESSION');

        //if session isn't started, start it now
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['timeout']) && $_SESSION['timeout'] < time()) {
            session_destroy();
            session_start();
            $this->regenerate();
            $_SESSION = array();
        }

        $_SESSION['timeout'] = time() + $maxlife;

        //uncomment next line if encrypting data
        //$this->sessionKey = $sessionKey;
    }

    public function read($id)
    {
        $data = parent::read($id);
        return $data;

        //replace the return line with this next line to encrypt
        //return mcrypt_decrypt(MCRYPT_3DES, $this->sessionKey, $data, MCRYPT_MODE_ECB);
    }

    public function write($id, $data)
    {
        //add this next line to encrypt data
        //$data = mcrypt_encrypt(MCRYPT_3DES, $this->sessionKey, $data, MCRYPT_MODE_ECB);
        //source: http://www.php.net/manual/en/class.sessionhandler.php

        return parent::write($id, $data);
    }

    public function regenerate()
    {
        //check to see if the session regeneration has already been initiated
	if (!isset($_SESSION['reload_timeout']) || $_SESSION['reload_timeout'] < time()) {

            //this sets the expiration to 10 seconds in the future
            $_SESSION['reload_timeout'] = time() + 10;

            //create a new (and discard old) session
            session_regenerate_id(true); 
            return true;

        } else {
            return false;
        }
    }
}

