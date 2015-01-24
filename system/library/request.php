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
 * Request Class
 *
 * contains global variables
 * 
 * @package		six-x
 * @subpackage	libraries
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Request 
{
	public $get = array();
	public $post = array();
	public $cookie = array();
	public $files = array();
	public $server = array();

	/**
	 * Constructor
	 */
  	public function __construct() 
  	{
		$_GET = $this->clean($_GET);
		$_POST = $this->clean($_POST);
		$_REQUEST = $this->clean($_REQUEST);
		$_COOKIE = $this->clean($_COOKIE);
		$_FILES = $this->clean($_FILES);
		$_SERVER = $this->clean($_SERVER);

		$this->get = $_GET;
		$this->post = $_POST;
		$this->request = $_REQUEST;
		$this->cookie = $_COOKIE;
		$this->files = $_FILES;
		$this->server = $_SERVER;
	}

	// --------------------------------------------------------------------
	
	/**
	 * remove special chars
	 *
	 * @access	public
	 * @param	array
	 * @return	array
	 */
	public function clean($data) 
	{
		if (is_array($data)) 
		{
			foreach ($data as $key => $value) 
			{
				unset($data[$key]);
				$data[$this->clean($key)] = $this->clean($value);
			}
		}
		else 
		{
			$data = htmlspecialchars($data, ENT_COMPAT);
		}
		return $data;
	}
}

/* End of file request.php */
/* Location: ./system/library/request.php */