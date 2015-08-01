<?php

/**

 * Six-X

 *

 * An open source application development framework for PHP 5.4.0 or newer

 *

 * @package		six-x

 * @author	Yuri Nasyrov <sapsan4eg@ya.ru>

 * @copyright	Copyright (c) 2014 - 2015, Yuri Nasyrov.

 * @license	http://six-x.org/guide/license.html

 * @link	http://six-x.org

 * @since	Version 1.0.0.0

 * @filesource

 */



// ------------------------------------------------------------------------


/**

 * Storage Class

 *

 * contains objects

 * 

 * @package	six-x

 * @subpackage	engine

 * @category	Libraries

 * @author	Yuri Nasyrov <sapsan4eg@ya.ru>

 * @link		http://six-x.org/

 */

final class Storage 
{
	private $data = array();
	/**

	 * get object in array

	 *

	 * @access	public

	 * @param	string

	 */

	public function get($key)

	{

		return ($this->has($key) ? $this->data[$key] : null);

	}

	// --------------------------------------------------------------------


	/**

	 * set object in array

	 *

	 * @access	public

	 * @param	string

	 * @param	object

	 */

	public function set($key, $value)

	{

		$this->data[$key] = $value;

	}

	// --------------------------------------------------------------------



	/**

	 * check object in array

	 *

	 * @access	public

	 * @param	string

	 * @param	object

	 */

	public function has($key)

	{

		return isset($this->data[$key]);

	}

}



/* End of file storage.php */

/* Location: ./system/engine/storage.php */