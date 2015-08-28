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
if (version_compare(phpversion(), '5.4.0', '<') == TRUE)
{
	exit('PHP5.4+ Required');
}
/**
 * six-x Error display (Display on browser and write on log file = all, Display only on browser = monitor, Only write on log file = log)
 * @var string
 */
define('ERR_DISPLAY', 'all');
/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
error_reporting(E_ALL);
ini_set('html_errors', 'on');
/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
set_error_handler('error_handler');
set_exception_handler('exception_handler');
/**
 * Error Handler
 * This function lets us invoke the exception class and
 * display errors using the standard error template located
 * This function will send the error page directly to the
 * browser and exit.
 *
 * @access	public
 * @return	void
 */
function error_handler($errno, $message, $file, $line)
{
    switch ($errno)
    {
        case E_NOTICE:
        case E_USER_NOTICE:
            $error = 'NOTICE';
            break;
        case E_WARNING:
        case E_USER_WARNING:
            $error = 'WARNING';
            break;
        case E_ERROR:
        case E_USER_ERROR:
            $error = 'FATAL ERROR';
            break;
        default:
            $error = 'UNKNOW';
            break;
    }
    writeError($error, $message, $file, $line);
}
/**
 * Write exception on display or log file
 * This function lets us invoke the exception class and
 * display errors using the standard error template located
 * This function will send the error page directly to the
 * browser and exit.
 *
 * @access	public
 * @return	void
 */
function exception_handler(\Exception $exception)
{
    if(defined('ERR_DISPLAY'))
    {
        if(ERR_DISPLAY == 'all' || ERR_DISPLAY == 'monitor')
        {
            echo '<i><b>ERROR</b>: ' .  $exception->getMessage() . ' in <b>"' . $exception->getFile() . '"</b> trace <b>"' . $exception->getTraceAsString() . '</b>"</i><br />';
        }
        if(ERR_DISPLAY == 'all' || ERR_DISPLAY == 'log')
        {
            if(class_exists('Log'))
            {
                Log::exception($exception);
            }
        }
    }
}
/**
 * Write error on display or log file
 * This function lets us invoke the exception class and
 * display errors using the standard error template located
 * This function will send the error page directly to the
 * browser and exit.
 *
 * @access	public
 * @return	void
 */
function writeError($error, $message, $file, $line)
{
    if(defined('ERR_DISPLAY'))
    {
        if(ERR_DISPLAY == 'all' || ERR_DISPLAY == 'monitor')
        {
            echo '<i><b>' . $error . '</b>: ' . $message . ' in <b>"' . $file . '"</b> on line <b>"' . $line . '</b>"</i><br />';
        }
        if(ERR_DISPLAY == 'all' || ERR_DISPLAY == 'log')
        {
            if(class_exists('Log'))
            {
                Log::error($error, $message, $file, $line);
            }
        }
    }
}
/**
 * Write fatal error on display or log file
 * This function lets us invoke the exception class and
 * display errors using the standard error template located
 * This function will send the error page directly to the
 * browser and exit.
 *
 * @access	public
 * @return	void
 */
function fatal_error_handler($buffer) {
    if (preg_match("|(Fatal error</b>:)(.+)(<br)|", $buffer, $regs) ) {
        Log::fatal($buffer);
        return "FATAL ERROR CAUGHT, check log file" ;
    }
    return $buffer;
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
// If you use composer
if(file_exists('../vendor/autoload.php')) {
    require_once('../vendor/autoload.php');
}


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

// usefull functions
\Loader::load('usefull', DIR_HELPER);

/* End of file startup.php */
/* Location: ./system/startup.php */