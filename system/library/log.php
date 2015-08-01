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
 * Log Class
 *
 * logging errors, notices ...
 * 
 * @package		six-x
 * @subpackage	libraries
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Log
{
	/**
	 * writing into log file
	 *
	 * @access	public
	 * @param	string
	 */
	public static function write($message)
	{
		// handle
		$handle = fopen(self::_file_name(), 'a+');	

		// writing into stream	
		fwrite($handle, date('Y-m-d G:i:s') . ' - ' . $message . "\n");

		// close file
		fclose($handle);
	}
	
	// --------------------------------------------------------------------

	/**
	 * get log file name
	 *
	 * @access	protected
	 * @return	string
	 */
	protected static function _file_name()
	{
		return (defined('DIR_ERRORS') ? DIR_ERRORS : DIR_SYSTEM . 'logs/') . (defined('ERR_FILE') ? ERR_FILE : 'error') . '.log';
	}

	// --------------------------------------------------------------------

	/**
	 * get log file name
	 *
	 * @access	public
	 * @return	string
	 */
	public static function Get_text()
	{
		return file_exists(self::_file_name()) ? file_get_contents(self::_file_name(), FILE_USE_INCLUDE_PATH, NULL) : '';
	}
	
	// --------------------------------------------------------------------

	/**
	 * get log file name
	 *
	 * @access	public
	 * @return	bool
	 */
	public static function Clear_log()
	{
		return file_exists(self::_file_name()) ? unlink(self::_file_name()) : TRUE;
	}
}

/* End of file log.php */
/* Location: ./system/library/log.php */