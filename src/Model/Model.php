<?php namespace Crondex\Model;

use Crondex\Database\Database;

class Model extends Database implements ModelInterface
{
    protected $class;
    protected $table;

    public function __construct($config)
    {
	parent::__construct($config); //this calls the Database constructor
        $this->class = get_class($this);

        //gets the class and strip Model off the end
        $this->table = strtolower(rtrim($this->class, 'Model'));
    }

    public function selectAll()
    {
        $sql = 'select * from ' . $this->table;
	$params = array();
        return $this->query($sql, $params, 'names');
    }

    public function __destruct()
    {
    }
}

