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
	 * writing debug
	 *
	 * @access	public
	 * @param	string
	 */
	public static function write($message)
	{
        $exception = array_merge(["LEVEL_NAME" => "DEBUG"], self::_getStat());
        $exception["MESSAGE"] = $message;
        self::_write(json_encode($exception));
	}

    // --------------------------------------------------------------------

    /**
     * write into log file
     *
     * @access	protected
     * @param	string
     * @return	void
     */
    protected static function _write($message)
    {
        // handle
        $handle = fopen(self::_file_name(), 'a+');

        // writing into stream
        fwrite($handle, $message . PHP_EOL);

        // close file
        fclose($handle);
    }

    // --------------------------------------------------------------------

    /**
     * writing error
     *
     * @access	public
     * @param	Exception
     * @return	void
     */
    public static function error($error, $message, $file, $line)
    {
        $exception = array_merge(["LEVEL_NAME" => $error], self::_getStat());
        $exception["MESSAGE"] = $message;
        $exception["LINE"] = $line;
        $exception["FILE"] = $file;
        self::_write(json_encode($exception));
    }
    // --------------------------------------------------------------------

    /**
     * writing exception
     *
     * @access	public
     * @param	Exception
     * @return	void
     */
	public static function exception(\Exception $e)
    {
        $exception = array_merge(["LEVEL_NAME" => "ERROR"], self::_getStat());
        $exception["MESSAGE"] = $e->getMessage();
        $exception["TRACE"] = $e->getTraceAsString();
        $exception["FILE"] = $e->getFile();
        self::_write(json_encode($exception));
    }

    // --------------------------------------------------------------------

    /**
     * getting stat
     *
     * @access	protected
     * @return	array
     */
    protected static function _getStat()
    {
        $time = time();
        $event["HTTP"] = ["SERVER" => [
            'HTTP_USER_AGENT' => isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '',
            'HTTP_REFERER' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
            'HTTP_ACCEPT_ENCODING' => isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : '',
            'HTTP_ACCEPT_LANGUAGE' => isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : '',
            'REMOTE_ADDR' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
            'REQUEST_SCHEME' => isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : '',
            'REMOTE_PORT' => isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : '',
            'SERVER_PROTOCOL' => isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : '',
            'REQUEST_METHOD' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
            'QUERY_STRING' => isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '']
            , ($_SERVER['REQUEST_METHOD'] == "POST" ? $_SERVER['REQUEST_METHOD'] : "GET") =>
                $_SERVER['REQUEST_METHOD'] == "POST" ? $_POST : $_GET,
            "COOKIE" => $_COOKIE,
        ];
        $event["TIME"] = ["EVENTIME" => date("c", $time), "DATETIME" => date("Y-m-d H:i:s", $time), "UNIXTIME" => $time];
        $event["SOURCEIP"] = $_SERVER['SERVER_ADDR'];
        $event["HOST"] = $_SERVER['SERVER_NAME'];

        return $event;
    }

	// --------------------------------------------------------------------

	/**
	 * get log file name
	 *
	 * @access	protected
	 * @return	string
	 */
	protected static function _file_name($name = NULL)
	{
		return (defined('DIR_ERRORS') ? DIR_ERRORS : DIR_SYSTEM . 'logs/') . ($name === NULL ?  date("Y-m-d") : $name) . '.log';
	}

	// --------------------------------------------------------------------

	/**
	 * get log file name
	 *
	 * @access	public
	 * @return	string
	 */
	public static function GetText($name = NULL)
	{
		return file_exists(self::_file_name($name)) ? file_get_contents(self::_file_name($name), FILE_USE_INCLUDE_PATH, NULL) : '';
	}
	
	// --------------------------------------------------------------------

	/**
	 * get log file name
	 *
	 * @access	public
	 * @return	bool
	 */
	public static function ClearLog($name = NULL)
	{
		return file_exists(self::_file_name($name)) ? unlink(self::_file_name($name)) : TRUE;
	}
}

/* End of file log.php */
/* Location: ./system/library/log.php */