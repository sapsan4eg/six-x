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
	protected $_server;
	protected $_port = '80';
	
	/**
	 * Constructor
	 */
	public function __construct($storage)
	{
		$this->_storage = new Storage();
		//$this->db		= $storage->get('db');
		$this->mui		= $storage->get('mui');
		$this->mui->load('Socets/main');
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * get content from remote server
	 *
	 * @access	public
	 * @param	string
	 * @return	mixed
	 */
	public function get_content_http($get)
	{
		$fp = fsockopen($this->_server, $this->_port, $errno, $errstr, 30);
		if ( ! $fp) 
		{
			trigger_error('Error: ' . $errstr . ' ()' . $errno . ')!');
		    return FALSE;
		} 
		else 
		{
		    $out = "GET " . $get . " HTTP/1.1\r\n";
			$out .= "User-Agent: Mozilla/5.0 Firefox/3.6.12\r\n";;
		    $out .= "Host: " . $this->_server . "\r\n";
		    $out .= "Connection: Close\r\n\r\n";
		    fwrite($fp, $out);
			
			$return = '';
		    while ( ! feof($fp)) 
		    {
		        $return .= fgets($fp, 128);
		    }
		    fclose($fp);
			
			return $return;
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * get content from remote server
	 *
	 * @access	public
	 * @param	string
	 */
	 public function set_server($server)
	 {
	 	$this->_server = $server;
	 }
}

/* End of file mui.php */
/* Location: ./system/library/mui.php */	