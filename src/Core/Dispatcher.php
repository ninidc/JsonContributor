<?php
//--------------------------------------------------------------//
//              DISPATCHER
//--------------------------------------------------------------//
namespace Core;

class Dispatcher {

	public $routes;
	public $controllers = array();

	protected $app;

	public function __construct($app)
	{
		$this->app = $app;
	}

	/*
	*	Return controller already instanciate
	*	or create instance and return it
	*
	* 	params: 
	*		ControllerName (string) : Name of the controller 
	*/
	private function getController($ControllerName)
	{
		$Controller = null;

		// Search if an instance of the controller exist.
	    foreach($this->controllers as $c) {
	        if($ControllerName == $c["name"]) {
	            $Controller = $c["instance"];
	        }
	    }

	    // If controller not already instanciate we create-it.
	    if(!$Controller) {
	        $Controller = new $ControllerName($this->app);

	        $this->controllers[] = array(
	            "name"      => $ControllerName,
	            "instance"  => $Controller
	        );
	    }

	    return $Controller;
	}


	/*
	*	Declare new route
	*
	*	params: 
	*		route (array)  (see routes files for definition)
	*/
	public function addRoute($route) 
	{

		$ControllerName = 'Core\Controller\\' . $route["controller"];
	    $Controller     = $this->getController($ControllerName);

	    if(isset($route["bind"])) {
	        $this->app->$route["type"]($route['route'], array($Controller, $route['method']))->bind($route["bind"]);
	    } else {
	        $this->app->$route["type"]($route['route'], array($Controller, $route['method']));
	    }
	}


	/*
	*	Set an array of routes
	*	
	*	params :
	*		routes (array)
	*/
	public function setRoutes($routes)
	{
		foreach($routes as $route) {
			$this->addRoute($route);
		}
	}

}
//--------------------------------------------------------------//
?>