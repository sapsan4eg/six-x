<?php
/**
 * six-x Version
 *
 * @var string
 *
 */
	define('VERSION', '0.0.0.1');
	
/**
 * six-x Error display (Display on browser and write on log file = all, Display only on browser = monitor, Only write on log file = log)
 *
 * @var string
 *
 */
	define('ERR_DISPLAY', 'all');

/*
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 *
 * Different environments will require different levels of error reporting.
 * By default development will show errors but testing and live will hide them.
 */
	error_reporting(E_ALL);
	
/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
 	set_error_handler('error_handler');
	
/**
* Error Handler
*
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
* Write error on display or log file
*
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
			{				echo '<i><b>' . $error . '</b>: ' . $message . ' in <b>"' . $file . '"</b> on line <b>"' . $line . '</b>"</i><br />';			}
			if(ERR_DISPLAY == 'all' || ERR_DISPLAY == 'log')
			{			    if(class_exists('Log'))
			    {			    	Log::write('PHP ' . $error . ':  ' . $message . ' in ' . $file . ' on line ' . $line);			    }			}		}
	}
	
/*
 * ------------------------------------------------------
 *  Load the framework constants
 * ------------------------------------------------------
 */
	require_once('config.php');
	
/*
 * ------------------------------------------------------
 *  Load the startup file
 * ------------------------------------------------------
 */
	require_once(DIR_SYSTEM . 'startup.php');
	
/*
 * ------------------------------------------------------
 *  Start the conveyor
 * ------------------------------------------------------
 */
	$conveyor = new Conveyor();

/* End of file index.php */
/* Location: ./index.php */