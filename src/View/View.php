<?php namespace Crondex\View;

//View.php (this is the main view/template)

class View implements ViewInterface
{
    protected $_view;
    protected $_action;
    protected $variables = array();

    public function __construct($view, $action)
    {
        $this->_view = $view;
	$this->_action = $action;
    }

    public function set($name,$value)
    {
        $this->variables[$name] = $value;
    }

    public function render()
    {
        extract($this->variables);

	//set view file
        $view_file = ROOT . DS . 'app' . DS . 'views' . DS . $this->_view . DS . $this->_action . '.php';

        //load view file
        if (file_exists($view_file)) {
            include ($view_file);
        } else {
            // I'm not sure this should go here.
            echo "404 will be included here.";
	}
    }

    public function __destruct()
    {
        echo '<br />View has been destructed<br />';
    }
}
