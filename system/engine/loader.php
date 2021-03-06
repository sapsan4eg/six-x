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
 * Loader Class
 *
 * loading anything
 * 
 * @package		six-x
 * @subpackage	engine
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Loader
{
	/**
	 * load anything
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	string
	 */
	
	public static function load($name, $dir, $ext = 'php')
	{
		// path to file
		
		$file = $dir .'/' . $name . '.' . $ext;

		// check exist the file
		
		if (file_exists($file)) 
		{
			require_once($file);
		}
		else
		{
			trigger_error('Error: Could not load ' . $name . ' from ' . $dir . '!');
			exit();
		}
	
	}

	/**
	 * autoloading
	 *
	 * @access	public
	 * @param	string
	 */
	public static function autoload ($name)
	{
		// list of path
		$list_dir = array(DIR_ENGINE, DIR_LIBRARY, DIR_START, DIR_DATABASE, DIR_MODELS, DIR_CONTROLLERS);
        $name = explode('\\', strtolower($name));
		// loop to path list
		foreach($list_dir as $dir)
		{
			// path to file
			$file = $dir . $name[count($name) - 1] . '.php';

			// check exist the file
			if (file_exists($file))
			{
				require_once($file);
				return;
			}
		}
	}

}

/* End of file loader.php */
/* Location: ./system/engine/loader.php */