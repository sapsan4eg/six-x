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
 * System Initialization File
 *
 * Loads the base classes and executes the request.
 *
 * @package		six-x
 * @subpackage	six-x
 * @category	Initial 
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/guide/license.html
 */
 
// ------------------------------------------------------------------------

/**
* Determines if the current version of PHP is greater then the supplied value
*
* @access	public
* @param	string
* @return	bool	TRUE if the current version is $version or higher
*/
if (version_compare(phpversion(), '5.3.0', '<') == TRUE) 
{
	exit('PHP5.3+ Required');
}


/*
 * ------------------------------------------------------
 *  Check register globals and if isset remove all keys
 * ------------------------------------------------------
 */
if (ini_get('register_globals')) 
{
	$globals = array($_REQUEST, $_SESSION, $_SERVER, $_FILES);
	foreach ($globals as $global) 
	{
		foreach(array_keys($global) as $key) 
		{
			unset(${$key});
		}
	}
}

/*
 * ------------------------------------------------------
 *  Check Magic Quotes and fix
 * ------------------------------------------------------
 */
if (ini_get('magic_quotes_gpc')) 
{
	function clean($data) 
	{
   		if (is_array($data)) 
   		{
  			foreach ($data as $key => $value) 
  			{
    			$data[clean($key)] = clean($value);
  			}
		} 
		else 
		{
  			$data = stripslashes($data);
		}

		return $data;
	}
	$_GET = clean($_GET);
	$_POST = clean($_POST);
	$_REQUEST = clean($_REQUEST);
	$_COOKIE = clean($_COOKIE);
}

/*
 * ------------------------------------------------------
 *  Set default time zone
 * ------------------------------------------------------
 */
if ( ! ini_get('date.timezone')) 
{
	date_default_timezone_set('Asia/Yekaterinburg');
}

/*
 * ------------------------------------------------------
 *  Support Windows IIS Compatibility
 * ------------------------------------------------------
 */
if ( ! isset($_SERVER['DOCUMENT_ROOT'])) 
{
	if (isset($_SERVER['SCRIPT_FILENAME'])) 
	{
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr($_SERVER['SCRIPT_FILENAME'], 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}

if ( ! isset($_SERVER['DOCUMENT_ROOT'])) 
{
	if (isset($_SERVER['PATH_TRANSLATED'])) 
	{
		$_SERVER['DOCUMENT_ROOT'] = str_replace('\\', '/', substr(str_replace('\\\\', '\\', $_SERVER['PATH_TRANSLATED']), 0, 0 - strlen($_SERVER['PHP_SELF'])));
	}
}
if ( ! isset($_SERVER['REQUEST_URI'])) 
{
	$_SERVER['REQUEST_URI'] = substr($_SERVER['PHP_SELF'], 1);

	if (isset($_SERVER['QUERY_STRING'])) 
	{
		$_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
	}
}

/*
 * ------------------------------------------------------
 *  Load loader class
 * ------------------------------------------------------
 */
require_once(DIR_ENGINE. 'loader.php');

/*
 * ------------------------------------------------------
 *  Enable autoload
 * ------------------------------------------------------
 */
spl_autoload_register(
	function ($name) {
	    Loader::autoload($name);
	}
);

/*
 * ------------------------------------------------------
 *  Get bundles
 * ------------------------------------------------------
 */
Bundles::create();

/* End of file startup.php */
/* Location: ./system/startup.php */