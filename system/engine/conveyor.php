<?php
/**
 * Six-X
 *
 * An open source application development framework for PHP 5.4.0 or newer
 *
 * @package		six-x
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @copyright	Copyright (c) 2014 - 2015, Yuri Nasyrov.
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
		$array = array(
			'HTTP_USER_AGENT' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '', 
			'HTTP_REFERER' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '', 
			'HTTP_ACCEPT_ENCODING' => isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '', 
			'HTTP_ACCEPT_LANGUAGE' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '', 
			'REMOTE_ADDR' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '', 
			'REQUEST_SCHEME' => isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : '', 
			'REMOTE_PORT' => isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '', 
			'SERVER_PROTOCOL' => isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '', 
			'REQUEST_METHOD' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '', 
			'QUERY_STRING' => isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '');
		#Log::write(json_encode($array));

		// instantiating Storage class
		$this->_storage = new Storage();
		
		// instantiating Joiner class and add to stroage
		$this->_storage->set('join', new Joiner($this->_storage));
		
		// instantiating Request class and add to stroage
		$this->join->library('request');
		
		// instantiating Session class and add to stroage
		$this->join->library('session');
		
		// instantiating Db class and add to stroage   
		$this->join->library('Six_x\Db');
		
		// instantiating Router class and add to stroage
		$this->join->library('router', array($this->request->get, $this->db));
		
		// instantiating Mui class
        \Six_x\Mui::start($this->db, $this->router->route['arguments'], $this->request->post,  $this->session, $this->request->cookie, $this->request->server);
        \Loader::load('translate', DIR_HELPER);

		// check need autorization to application
   		if(defined('AUTORIZATION'))
   		{
   			// instantiating autorization class and add to stroage
			$this->join->library(AUTORIZATION . 'Autorization', array($this->router->route), 'autorization');
		}
		
		// --------------------------------------------------------------------

		// get main parametrs
		$controllerName = $this->router->route['controller'];
		$action = $this->router->route['action'];
		$arguments = array();
		
		// instantiating controller class and add to stroage
   		$controller = $this->join->controller($controllerName);
		
		// check exist action in controller
   		if ( ! method_exists($controller, $action) OR array_search($action, get_class_methods($controller)) === FALSE)
   		{
   			$view = new View(array('router' => $this->router));
			$view->RedirectToAction($this->router->route['default_action'] , $this->router->route['error_controller']);	
		}
		
		// call to action from the controller
		$return = call_user_func_array(array($controller, $action), $arguments);
		
		// rendering
  		echo $return;
	}
}

/* End of file conveyor.php */
/* Location: ./system/engine/conveyor.php */