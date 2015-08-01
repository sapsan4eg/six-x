<?php
/**
 * Six-X
 *
 * An open source application development framework for PHP 5.4.0 or newer
 *
 * @package	six-x
 * @author	Yuri Nasyrov <sapsan4eg@ya.ru>
 * @copyright	Copyright (c) 2014 - 2015, Yuri Nasyrov.
 * @license	http://six-x.org/guide/license.html
 * @link	http://six-x.org
 * @since	Version 1.0.0.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Session Class
 *
 * contains and manage session values
 * 
 * @package	six-x
 * @subpackage	libraries
 * @category	Libraries
 * @author	Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link	http://six-x.org/
 */
class Session 
{
	public $data = array();
			
	/**
	 * Constructor
	 */
  	public function __construct() 
  	{		
		if ( ! session_id()) 
		{
			ini_set('session.use_cookies', 'On');
			ini_set('session.use_trans_sid', 'Off');
			
			session_set_cookie_params(0, '/');
			session_start();
		}
	
		$this->data =& $_SESSION;
	}
}

/* End of file session.php */
/* Location: ./system/library/session.php */