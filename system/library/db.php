<?php
namespace Six_x;
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
 * Initialize the database
 *
 * @package		six-x
 * @subpackage	library
 * @category	Database
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/guide/database/
 */
class Db {

	private $_driver;
	
	/**
	 * Construct
	 * 
	 * */
	public function __construct()
	{
		// name of class to work with database
		$driver =  DB_DRIVER . '_x';
		$this->_driver = new $driver(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Execute query in database
	 * 
	 * @param	string
	 * @return	mixed
	 */
  	public function query($sql, $writeble = FALSE)
  	{
		return $this->_driver->query($sql, $writeble);
  	}

	// --------------------------------------------------------------------
	
	/**
	 * Escape 
	 * 
	 * @param	string
	 * @return	string
	 */
	public function escape($value) 
	{
		return $this->_driver->escape($value);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Count affected  
	 * 
	 * @return	int
	 */
  	public function countAffected() 
  	{
		return $this->_driver->countAffected();
  	}

	// --------------------------------------------------------------------
	
	/**
	 * Get last inserted id  
	 * 
	 * @return	int
	 */
  	public function getLastId() 
  	{
		return $this->_driver->getLastId();
  	}
}

/* End of file db.php */
/* Location: ./system/library/db.php */