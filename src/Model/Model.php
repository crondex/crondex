<?php namespace Crondex\Model;

use Crondex\Database\Database;

class Model extends Database implements ModelInterface
{
    protected $_class;
    protected $_table;

    public function __construct($config)
    {
	parent::__construct($config); //this calls the Database constructor
        $this->_class = get_class($this);

        //gets the class and strip Model off the end
        $this->_table = strtolower(rtrim($this->_class, 'Model'));
    }

    public function selectAll()
    {
        $sql = 'select * from ' . $this->_table;
	$params = array();
        return $this->query($sql, $params, 'names');
    }

    public function __destruct()
    {
    }
}

