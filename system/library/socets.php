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
 * Mui Class
 *
 * support multi language interface of application
 * 
 * @package		six-x
 * @subpackage	libraries
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Socets extends Object
{
	/**
	 * Constructor
	 */
	public function __construct($storage)
	{
		$this->_storage = new Storage();
		$this->db = $storage->get('db');
		$this->mui = $storage->get('mui');
		$this->mui->load('Validation/validation');
	}
	
	// --------------------------------------------------------------------
	
}

/* End of file mui.php */
/* Location: ./system/library/mui.php */	