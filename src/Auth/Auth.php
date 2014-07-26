<?php namespace Crondex\Auth;

use Crondex\Model\Model;
use Crondex\Security\Random;
use Crondex\Security\RandomInterface;
use Crondex\Config\EnvironmentInterface;

class Auth extends Model implements AuthInterface
{
    protected $random;
    protected $user;
    protected $token;
    private $loggedInUsersTable;
    private $sessionIdColumn;
    private $tokenColumn;
    private $userIdColumn;
    private $adminTable;
    private $usernameColumn;

    public function __construct($config, RandomInterface $randomObj)
    {
        //call the parent constructor
        parent::__construct($config);

        //inject objects
        $this->random = $randomObj;
        $this->config = $config;

        //get database table and column names (from main.ini config)
        $this->loggedInUsersTable = $config->get('loggedInUsersTable');
        $this->sessionIdColumn = $config->get('sessionIdColumn');
        $this->tokenColumn = $config->get('tokenColumn');
        $this->userIdColumn = $config->get('userIdColumn');
        $this->adminTable = $config->get('adminTable');
        $this->usernameColumn = $config->get('usernameColumn');
    }

    protected function setToken() {

        /*
         * Assign a random value to $token
         */
        $token = $this->random->get_random_bytes(50);

        /*
         * hash the token
         * although not a password, we're using the password_hash function
         */
        $this->token = password_hash($token, PASSWORD_BCRYPT, array("cost" => 5));

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
            $_SESSION['token'] = $this->token;

            //set sql to update token logged-in-users
            $sql = "UPDATE $this->loggedInUsersTable SET $this->sessionIdColumn=?, $this->tokenColumn=? WHERE $this->userIdColumn=?";
            $params = array(session_id(), $this->token, $userID);

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
            $sql = "DELETE FROM $this->loggedInUsersTable WHERE $this->userIdColumn=? OR $this->sessionIdColumn=? OR $this->tokenColumn=?";
            $params = array($_SESSION['user_id'], session_id(), $_SESSION['token']);
        
            if ($this->query($sql, $params, 'names')) {
                return true;
            }
            return false;
        }
        return false;
    }

    public function login($user)
    {
        //grab user row based on username
        $sql = "SELECT * FROM $this->adminTable WHERE $this->usernameColumn=?";
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
            $_SESSION['token'] = $this->token;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $user;
        } else {
            return false;
        }

        //first remove logged-in users
        if ($this->removeLoggedInUser()) {

            //next insert new 'logged_in_user' record
            $sql = "INSERT INTO $this->loggedInUsersTable ($this->userIdColumn, $this->sessionIdColumn, $this->tokenColumn) VALUES (?, ?, ?)";
            $params = array($user_id, session_id(), $this->token);

            if ($this->query($sql, $params, 'names')) {
	        return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //check if logged in
    public function check()
    {
        if (isset($_SESSION['user_id'])) {

            $sql = "SELECT * FROM $this->loggedInUsersTable WHERE $this->userIdColumn=?";
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
                    $this->refresh($this->loggedInUsersTable, $_SESSION['user_id']);

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

