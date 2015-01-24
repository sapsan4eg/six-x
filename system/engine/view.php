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
 * View Class
 *
 * responsible for issuing results
 *
 * @package		six-x
 * @subpackage	engine
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
final class View {
	private $_data = array();
	public $router;

	/**
	 * Constructor
	 *
	 * @param	array
	 */
	public function __construct($data = array())
	{
		$this->set($data);
	}

	// --------------------------------------------------------------------

	/**
	 * return array
	 *
	 * @access	public
	 * @return	array
	 */
	public function get()
	{
		return $this->_data;
	}

	// --------------------------------------------------------------------

	/**
	 * set array
	 *
	 * @access	public
	 * @param	array
	 */
	public function set($data)
	{
		if(isset($data['router']))
		{
			$this->router = $data['router'];
			unset($data['router']);
		}
		$this->_data = $data;	
	}

	// --------------------------------------------------------------------

	/**
	 * overload magic method __set
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 */
	public function __set($key, $value)
	{
		$this->_data[$key] = $value;
	}

	// --------------------------------------------------------------------

	/**
	 * overload magic method __get
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function __get($key)
	{
		return ($this->has($key) ? $this->_data[$key] : null);
	}

	// --------------------------------------------------------------------

	/**
	 * check has key in array
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function has($key)
	{
    		return isset($this->_data[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * overload magic method __has
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function __isset($key)
	{
		return isset($this->_data[$key]);
	}

	// --------------------------------------------------------------------

	/**
	 * retrun text from template file
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */
	public function ViewResult($actionName = '', $layot = true, $controllerName = '')
	{
		// check name action
		if(strlen($actionName) == 0)
		{
			$actionName = $this->_data['ActionName'];
		}

		// check name controller
		if(strlen($controllerName) == 0)
		{
			$controllerName = $this->_data['ControllerName'];
		}

		// path to template
		$file = DIR_VIEWS . $controllerName . '/' . $actionName . '.' . FILE_VIEW;

		// check need layout
		if($layot == true)
		{
			// layout file
			$layoutfile =  FILE_LAYOUT;

			// check no default layout
			if(isset($this->_data['layout']))
			{
				if(strlen($this->_data['layout']) > 0)
				{
					$layoutfile = $this->_data['layout'];
				}
			}

			// path to layout file			
			$file = DIR_VIEWS .DIR_SHARED . $layoutfile . '.' . FILE_VIEW;

			// reqursive to get layout
			$this->_data['RenderBody'] = $this->ViewResult($actionName, false, $controllerName);
		}

		// check exist file template
		if (file_exists($file))
		{
			extract($this->_data);
			ob_start();
			include($file);
			$content = ob_get_contents();	
			ob_end_clean();	
			return $content;
		}
		else
		{
			trigger_error('Error: Could not load view ' . $file . '!');
			exit();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * retrun text from template file without layout
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	public function PartialViewResult($actionName = '', $controllerName = '')
	{
		return $this->ViewResult($actionName, FALSE, $controllerName);
	}

	// --------------------------------------------------------------------

	/**
	 * redirect browser to new page
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	array
	 */
	public function RedirectToAction($action = '', $controller = '', $arguments = array())
	{
		header('Location: ' . $this->router->Link($action, $controller, $arguments));
		exit;
	}

	// --------------------------------------------------------------------

	/**
	 * generates error page with status 404
	 *
	 * @access	public
	 * @param	mixed
	 * @param	string
	 * @return	string
	 */
	public function NotFoundResult($actionName = '',  $layot = TRUE, $controllerName = '')
	{
		// get sapi name
		$sapi_name = php_sapi_name();

		// check what the answer to generate
		if ($sapi_name == 'cgi' || $sapi_name == 'cgi-fcgi')
		{
			header('Status: 404 Not Found');
		}
		else
		{
			header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
		}

		return $this->ViewResult($actionName, $layot, $controllerName);
	}

	// --------------------------------------------------------------------

	/**
	 * return file stream to browser
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 */
	public function FileResult($file = '', $filename = '')
	{
		// check exist file
		if (file_exists($file))
		{
			// check what name is the output file
			if(strlen($filename) == 0)
			{
				$filename = basename($file);
			}
			else
			{
				$filename .= '.' . substr(strrchr($file, '.'), 1);
			}

			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);

			exit();
		}
		else
		{
			trigger_error('Error: Could not find file ' . $file . '!');
			exit();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * return json string format
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	public function JsonResult($array = array())
	{
		// check support json force object
		if (version_compare(phpversion(), '5.2.0', '<') == TRUE)
		{
			json_encode($array, JSON_FORCE_OBJECT);
		}
		else
		{
			$return = json_encode($array);
		}

		if($return)
		{
			return $return;
		}
	}
}

/* End of file view.php */
/* Location: ./system/engine/view.php */