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
 * Controller Class
 *
 * @package		six-x
 * @subpackage	engine
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Controller extends Object {
	/**
	 * Constructor
	 * 
	 * @param	object 
	 */
	public function __construct($storage)
	{
		// append accessor to Storage
		$this->_storage = $storage;

		// array reserved variables 
		$data = array(
					'ControllerName' => $this->router->route['controller'], 
					'ActionName' => $this->router->route['action'], 
					'RequestedUrl'=> 'http' . (isset($this->request->server['HTTPS']) ? 's' : '') . '://' . $this->request->server['HTTP_HOST'] . htmlspecialchars_decode($this->request->server['REQUEST_URI']),
					'router' => $this->router
					);					
		
		// check whether it is sent to the session
		if(isset($this->session->data['message']))
		{
			$data['message'] = $this->session->data['message'];
			unset($this->session->data['message']);
		}

		// instantiating View class
		$this->view = new View($data);
	}
}

/* End of file controller.php */
/* Location: ./system/engine/controller.php */