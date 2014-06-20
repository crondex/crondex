<?php namespace Crondex\Routing;

//Controller.php (this is the front controller)
//this class is extended by the sub controllers (located in: ROOT . DS . 'apps/controllers/')

/**
* Here is the workflow:
*
* The URI (via the URL or a form action) is first parsed in boostrap.php (via ParseURL.php), which then
* then intantiates model and view and calls the desired method.
*
* The general URL/URI syntax is as follows:
*
*     http://www.domain.com/controller/action/querystring
*   
* Or, another way to look at it is:
*
*     http://www.domain.com/class/method/
*
* If input has been set via $_POST, it is then passed to the subcontroller (and set via set()).
* and which then passes it to the front/main Controller, which passes them to the view (also via set()).
*
* Upon destruction, the main/front Controller then renders the view (calling $view->render()). This
* extracts the variables set (via set()) and includes the subview file (which is included as view_file.php)
*
* Because these variables were set via the View and the subview file was included at the same time,
* the included file has access to use these variables/data structures (and display them accordingly).
* and the View then extracts these variables and makes them available to the subview to
*/

use Crondex\Model\ModelInterface;
use Crondex\View\ViewInterface;
	
class Controller
{
    protected $model;
    protected $view;

    public function __construct(ModelInterface $modelObj, ViewInterface $viewObj)
    {
        //inject model object (using the $model variable)
	$this->model = $modelObj;

        //inject view object
	$this->view = $viewObj;
    }

    public function getPost($value)
    {
        if (isset($_POST["$value"])) {
            return $_POST["$value"];
        } else {
            return false;
        }
    }

    public function getGet($value)
    {
        if (isset($_GET["$value"])) {
            return $_GET["$value"];
        } else {
            return false;
        }      
    }

    public function set($name,$value)
    {
        $this->view->set($name,$value);
    }

    public function __destruct()
    {
        //this extracts the variables set by 'set()' above, and then
        //includes the the subview (view_file.php).
        $this->view->render();
    }
}
