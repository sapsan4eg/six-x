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
 * Joiner Class
 *
 * Check and load class
 * 
 * @package		six-x
 * @subpackage	engine
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Joiner extends Object {
	/**
	 * load and instantiating model class
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	mixed
	 */
	public function model($name, $arguments = array(), $alternative_name = FALSE)
	{
		// get class name changing slashes to underline
		$class = $this->_take_class_name(str_replace("/", "_", $name ), '_model');
		
		// check alternative name
		if($alternative_name === FALSE )
		{
			// changing the slashes in the name and add suffix Model
			$alternative_name = str_replace("/", "", $name ) . 'Model';
		}
		
		// load and instantiating class then add to stroage
		$this->_try_join($name, $class, DIR_MODELS, $arguments, $alternative_name);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * load and instantiating library class
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	mixed
	 */
	public function library($name, $arguments = array(), $alternative_name = FALSE)
	{
		// get the class name
		$class = $this->_take_class_name($name);
		
		// remove underline in the name
		$name = str_replace("_", "", $name);
		
		// check alternative name
		if($alternative_name === FALSE )
		{
			$alternative_name = $name;
		}
		
		// load and instantiating class then add to stroage
		$this->_try_join($name, $class, DIR_LIBRARY, $arguments, $alternative_name);
	}	
	
	// --------------------------------------------------------------------
	
	/**
	 * load and instantiating library class
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @return	object
	 */
	public function controller($name, $arguments = array(), $alternative_name = FALSE)
	{
		// get the class name
		$class = $this->_take_class_name($name, 'Controller');
		
		// changing the underline to slashes in the name
		$name = str_replace("_", "/", $name);	
		
		// load and instantiating class then return
		return $this->_try_join($name . 'Controller', $class, DIR_CONTROLLERS, $arguments, $alternative_name, FALSE);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * load and instantiating library class
	 *
	 * @access	protected
	 * @param	string
	 * @param	string
	 * @param	string
	 * @param	array
	 * @param	mixed
	 * @param	bool
	 * @return	mixed
	 */
	protected function _try_join($name, $class, $dir, $arguments = array(), $alternative_name = FALSE, $to_storage = TRUE)
	{
		// load the class file
		Loader::load($name, $dir);
		
		// check class exist
		if(class_exists($class))
		{
			// check extends class of the object or controller
			if(get_parent_class($class) !== FALSE && (get_parent_class($class) == 'Object' OR get_parent_class($class) == 'Controller'))
			{
				array_unshift($arguments, $this->_storage);
			}
			
			// check type of arguments
			if(is_array($arguments))
			{
				// instantiating ReflectionClass
				$ref_class = new ReflectionClass($class);
				
				// check alternative name
				if($alternative_name) 
				{
					$class = $alternative_name;
				}
				
				//check need add to storage
				if($to_storage)
				{
					$this->_storage->set($class, $ref_class->newInstanceArgs($arguments));
				}
				else 
				{

					return $ref_class->newInstanceArgs($arguments);
				}
			}
		}
		else
		{
		  	trigger_error('Error: Class not exist ' . $class . ' in ' . $name . '!');
			exit();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * convert string
	 *
	 * @access	protected
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _take_class_name($name, $finishing = '')
	{
		$class = preg_replace('/[^a-zA-Z0-9]_/', '', $name) . $finishing;		
		$class = strtoupper(substr($class, 0, 1)) . substr($class, 1);

		return $class;
	}
}

/* End of file joiner.php */
/* Location: ./system/engine/joiner.php */