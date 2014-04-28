<?php namespace Crondex\Database; 

use PDO;
use PDOException;

class Database extends PDO
{
    public $config;
    public $sqlError;
    public $sqlErrorCode;
    public $sqlErrorMessage;

    function __construct($config)
    {
        try {

            parent::__construct($config->get('db_type').
                ':host='.$config->get('db_host').
                ';dbname='.$config->get('db_name'),
                $config->get('db_username'),
                $config->get('db_password'));

            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->setAttribute(PDO::ATTR_EMULATE_PREPARES,false); 

        } catch(PDOException $e) {				
            die("ERROR: ". $e->getMessage());
        }
    }
	
    public function query($sql, $params, $fetchmode = '')
    {
        //set PDO fetchmode and prepare and execute $sql query
        try {

            $stmt = $this->prepare($sql);

            //set fetchmode
            if ($fetchmode !== '') {
                switch ($fetchmode) {
                    case ('names'):
                       $stmt->setFetchMode(PDO::FETCH_ASSOC);
                       break;
                    case ('numbers'):
                        $stmt->setFetchMode(PDO::FETCH_NUM);
                        break;
                    default:
                        $stmt->setFetchMode(PDO::FETCH_BOTH);
                }
            }

            $stmt->execute($params);	
            return $stmt;

        } catch(PDOException $e) {

            $exception = $e->getMessage();

            if (isset($stmt)) {
                $sqlError = $stmt->errorInfo();
                $this->sqlErrorMessage = $sqlError[0];
                $this->sqlErrorCode = $sqlError[1];
            } else {
                echo $exception;
                echo "Something went terribly wrong. Sorry about that. We're on it...";
                // maybe have it email the $exception to me? This would notify me when there is a
                //bad query or if/when someone is hacking, etc...
            }
            return false;
        }
    }
}

