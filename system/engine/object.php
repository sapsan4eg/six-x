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
 * Object Class
 *
 * abstract class
 * 
 * @package		six-x
 * @subpackage	engine
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
abstract class Object {
	protected $_storage;

	/**
	 * Constructor
	 */
	public function __construct($storage)
	{
		$this->_storage = $storage;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * overload magic method __get
	 *
	 * @access	public
	 * @param	string
	 */
	public function __get($key)
	{
		return  $this->_storage->get($key);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * overload magic method __set
	 *
	 * @access	public
	 * @param	string
	 * @param	object
	 */
	public function __set($key, $value)
	{
		$this->_storage->set($key, $value);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * overload magic method __isset
	 *
	 * @access	public
	 * @param	string
	 */
	public function __isset($key)
	{
		$this->_storage->has($key);
	}
}

/* End of file object.php */
/* Location: ./system/engine/object.php */