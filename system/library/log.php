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
		// name log file
		$file = self::FileName();

		// handle
		$handle = fopen($file, 'a+');	

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
	protected static function FileName()
	{
		// default file name
		$filename = 'error';

		// check isset file name
		if(defined('ERR_FILE'))
		{
			$filename = ERR_FILE;
		}

		// default dir to log file
		$dir = 'system/logs/';

		// check isset dir to log file
		if(defined('ERR_PATH'))
		{
			$dir = ERR_PATH;
		}

		return $dir . $filename . '.txt';	
	}

	// --------------------------------------------------------------------

	/**
	 * get log file name
	 *
	 * @access	public
	 * @return	string
	 */
	public static function GetText()
	{
		// path to file
		$file = self::FileName();

		// error log text
		$log = '';

		// try get error text
		if (file_exists($file))
		{
			$log = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		}

		return $log;
	}
}

/* End of file log.php */
/* Location: ./system/library/log.php */