<?php namespace Crondex\Auth;

use Crondex\Model\Model;
use Crondex\Security\Random;
use Crondex\Security\RandomInterface;

class Auth extends Model implements AuthInterface
{
    protected $_random;
    protected $_user;
    protected $_token;
    private $_loggedInUsersTable;
    private $_sessionIdColumn;
    private $_tokenColumn;
    private $_userIdColumn;
    private $_adminTable;
    private $_usernameColumn;

    public function __construct($config, RandomInterface $randomObj)
    {
        //call the parent constructor
        parent::__construct($config);

        //inject object
        $this->_random = $randomObj;

        //get database table and column names (from main.ini config)
        $this->_loggedInUsersTable = $config->get('loggedInUsersTable');
        $this->_sessionIdColumn = $config->get('sessionIdColumn');
        $this->_tokenColumn = $config->get('tokenColumn');
        $this->_userIdColumn = $config->get('userIdColumn');
        $this->_adminTable = $config->get('adminTable');
        $this->_usernameColumn = $config->get('usernameColumn');
    }

    protected function setToken() {

        /*
         * Assign a random value to $token
         */
        $token = $this->_random->get_random_bytes(50);

        /*
         * hash the token
         * although not a password, we're using the password_hash function
         */
        $this->_token = password_hash($token, PASSWORD_BCRYPT, array("cost" => 5));

        if (isset($token)) {
            return true;
        }
        return false;
    }

    protected function refresh($userID)
    {
        //Regenerate id
	session_regenerate_id();

        //setup session
        if ($this->setToken()) {
            $_SESSION['token'] = $this->_token;

            //set sql to update token logged-in-users
            $sql = "UPDATE $this->_loggedInUsersTable SET $this->_sessionIdColumn=?, $this->_tokenColumn=? WHERE $this->_userIdColumn=?";
            $params = array(session_id(), $this->_token, $userID);

            //update database
            if ($this->query($sql, $params, 'names')) {

                //session has been updated
                return true;
            }
            //session update failed
            return false;
        }
        //session update failed - new token not set
        return false;
    }

    public function removeLoggedInUser() {

        //if $_SESSION variables are set
        if (isset($_SESSION['user_id']) || isset($_SESSION['token'])) {

            //delete logged-in users
            $sql = "DELETE FROM $this->_loggedInUsersTable WHERE $this->_userIdColumn=? OR $this->_sessionIdColumn=? OR $this->_tokenColumn=?";
            $params = array($_SESSION['user_id'], session_id(), $_SESSION['token']);
        
            if ($this->query($sql, $params, 'names')) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function login($user) {

        //grab user row based on username
        $sql = "SELECT * FROM $this->_adminTable WHERE $this->_usernameColumn=?";
        $params = array($user);
        $rows = $this->query($sql, $params, 'names');

        //get user's 'id' and assign to $user_id
        if ($rows) {
            //loop through each row (there should only be one match)
            foreach ($rows as $row) {
                $user_id = $row['id'];
            }
        } else {
            return false;
        }

        //setup session vars
        if ($this->setToken()) {
            $_SESSION['token'] = $this->_token;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $user;
        } else {
            return false;
        }

        //first remove logged-in users
        if ($this->removeLoggedInUser()) {

            //next insert new 'logged_in_user' record
            $sql = "INSERT INTO $this->_loggedInUsersTable ($this->_userIdColumn, $this->_sessionIdColumn, $this->_tokenColumn) VALUES (?, ?, ?)";
            $params = array($user_id, session_id(), $this->_token);

            //grab the hash from the user's row
            if ($this->query($sql, $params, 'names')) {
	        return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function check()
    {
        if (isset($_SESSION['user_id'])) {

            $sql = "SELECT * FROM $this->_loggedInUsersTable WHERE $this->_userIdColumn=?";
            $params = array($_SESSION['user_id']);
            $rows = $this->query($sql, $params, 'names');

            if ($rows) {

                //loop through each row (there should only be one match)
                foreach ($rows as $row) {

                    $session_id = $row['session_id'];
                    $token = $row['token'];
                }

                //check to see if the session_id and token match the database
                if ($session_id === session_id() && $token === $_SESSION['token']) {

                    //they are the same
                    $this->refresh($this->_loggedInUsersTable, $_SESSION['user_id']);

                } else {

                    //they are different
                    $this->logout();
                }
            }
        }
    }

    public function logout()
    {
        if ($this->removeLoggedInUser()) {

            session_unset();
            //$_SESSION = '';
            session_destroy();

            return true;
        }
        return false;
    }
}

