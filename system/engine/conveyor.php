<?php
/**
 * Six-X
 *
 * An open source application development framework for PHP 5.3.0 or newer
 *
 * @package		six-x
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @copyright	Copyright (c) 2014 - 2014, Yuri Nasyrov.
 * @license		http://six-x.org/guide/license.html
 * @link		http://six-x.org
 * @since		Version 1.0.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Conveyor Class
 *
 * @package		six-x
 * @subpackage	engine
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Conveyor extends Object
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// instantiating Storage class
		$this->_storage = new Storage();
		
		// instantiating Joiner class and add to stroage
		$this->_storage->set('join', new Joiner($this->_storage));
		
		// run application
		$this->_core();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Main application method 
	 *
	 * @access	protected
	 */
	protected function _core()
	{
		// instantiating Request class and add to stroage
		$this->join->library('request');
		
		// instantiating Session class and add to stroage
		$this->join->library('session');
		
		// instantiating Db class and add to stroage   
		$this->join->library('db');
		
		// instantiating Router class and add to stroage
		$this->join->library('router', array($this->request->get, $this->db));
		
		// instantiating Mui class and add to stroage
		$this->join->library('mui', array($this->db, $this->router->route['arguments'], $this->request->post,  $this->session, $this->request->cookie, $this->request->server));
		
		// check need autorization to applicatio
   		if(defined('AUTORIZATION'))
   		{
   			// instantiating autorization class and add to stroage
			$this->join->library(AUTORIZATION . '_autorization', array($this->router->route), 'autorization');
		}
		
		// --------------------------------------------------------------------

		// get main parametrs
		$controllerName = $this->router->route['controller'];
		$action = $this->router->route['action'];
		$arguments = array();
		
		// instantiating controller class and add to stroage
   		$controller = $this->join->controller($controllerName);
		
		// check exist action in controller
   		if ( ! method_exists($controller, $action))
   		{
   			$view = new View(array('router' => $this->router));
			$view->RedirectToAction($this->router->route['default_action'] , $this->router->route['error_controller']);	
		}
		
		// call to action from the controller
		$return = call_user_func_array(array($controller, $action), $arguments);
		
		// rendering
  		$this->_rend($return);	
	}

	/**
	 * Rendering 
	 *
	 * @access	protected
	 */
	protected function _rend($return)
	{
		echo $return;
	}
}

/* End of file conveyor.php */
/* Location: ./system/engine/conveyor.php */