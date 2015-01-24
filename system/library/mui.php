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
class Mui
{	
	private $_default = 'en_EN';
	private $_lang;
	private $_dictionary = array();
	private $_name;
	private $_list_languges = array();
	
	/**
	 * Constructor
	 * 
	 * @param	object
	 * @param	array
	 * @param	array
	 * @param	object
	 * @param	array
	 * @param	array
	 */
	public function __construct($db, $arguments, $post, $session, $cookie, $server)
	{
		// get default language
		$this->_lang = $this->_default;
		
		// name of deafult language
		$this->_name = 'no name';
		
		// check in database supports languages
		$query = $db->query("SELECT * FROM `" . DB_PREFIX . "language` 
										WHERE status = '1' 
										ORDER BY `sort_order`");
										
		// create associative array type => name								
		$this->_set_list_languges($query);
		
		// check what languege need		
		if($query->count > 1)
		{
			$languages = array();
			
			// loop through result list
			foreach ($query->list As $result)
			{
				$languages[$result['lang']] = $result;
			}
			
			// array possible language accepted
			$array = array(
						isset($arguments['my_mui_language']) ? $arguments['my_mui_language'] : FALSE, 
						isset($post['my_mui_language']) ? $post['my_mui_language'] : FALSE, 
						isset($session->data['language']) ? $session->data['language'] : FALSE, 
						isset($cookie['language']) ? $cookie['language'] : FALSE
					);					
						
			// need check browser to get language locale
			$check_browser = TRUE;
			
			// loop throught possible array
			foreach($array As $value)
			{
				if ($value != FALSE && array_key_exists($value, $languages) && $languages[$value]['status'])
				{
					$this->_name	= $languages[$value]['name'];
					$this->_lang	= $value;
					$check_browser	= FALSE;
					break;
				}
			}
			
			// if need check browser to language locale			
			if($check_browser)
			{
				if (isset($server['HTTP_ACCEPT_LANGUAGE']))
				{
					if($server['HTTP_ACCEPT_LANGUAGE'])
					{ 
						$browser_languages = explode(',', $server['HTTP_ACCEPT_LANGUAGE']);	
									
						foreach ($browser_languages As $browser_language)
						{
							foreach ($languages As $key => $value)
							{
								if ($value['status'])
								{
									$locale = explode(',', $value['locale']);
								
									if (in_array($browser_language, $locale))
									{
										$this->_lang = $key;
									}
								} 
							}
						}
					}
				}		
			}	
			
			// ------------------------------------------------------------------------
					
		} 
		else if ($query->count == 1)
		{
			$this->_name = $query->first['name'];
			$this->_lang = $query->first['lang'];
		} 
		
		// check isset in session langauge locale
		if ( ! isset($session->data['language']))
		{
			$session->data['language'] = $this->_lang;	
		}
		elseif ($session->data['language'] != $this->_lang)
		{
			$session->data['language'] = $this->_lang;
		}	
		
		//  check isset in cookie language locale
		if ( ! isset($cookie['language']))
		{
			setcookie('language', $this->_lang, time() + 60 * 60 * 24 * 30, '/', $server['HTTP_HOST']);
		}
		elseif ($cookie['language'] != $this->_lang)
		{
			setcookie('language', $this->_lang, time() + 60 * 60 * 24 * 30, '/', $server['HTTP_HOST']);
		}
		
		// load main dictionary 
		$this->load('main');
	}

	// --------------------------------------------------------------------
	
	/**
	 * load dictionary
	 *
	 * @access	public
	 * @param	string
	 * @param	mixed
	 */
	public function load($filename, $lang = FALSE) 
	{
		// get name of language file
		$lang = $lang != FALSE ? $lang : $this->_lang; 
		
		// path to language file
		$file = DIR_LANGUAGE . $lang . '/' . $filename . '.php';
		
		// check exist language file
		if (file_exists($file))
		{
			$_ = array();
			require($file);
			$this->_dictionary = array_merge($this->_dictionary, $_);
		} 
		else if($lang != $this->_default)
		{
			$this->load($filename, $this->_default);
		}
		else
		{
			trigger_error(' Could not load language ' . $filename . ' from ' . $file .'!' );
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * get translated string
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	public function get($key) 
	{
		return (isset($this->_dictionary[$key]) ? $this->_dictionary[$key] : $key);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * get type language
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_lang()
	{
		return $this->_lang;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * get name language
	 *
	 * @access	public
	 * @return	string
	 */
	public function get_name()
	{
		return $this->_name;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * get list language type => name
	 *
	 * @access	public
	 * @return	array
	 */
	public function get_list_languges()
	{
		return $this->_list_languges;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * get list language type => name
	 *
	 * @access	public
	 * @param	array
	 */
	protected function _set_list_languges($query)
	{
		$list = array();
		
		foreach ($query->list as $result)
		{
			$list[$result['lang']] = $result['name'];
		}
		
		$this->_list_languges = $list;
	}
}

/* End of file mui.php */
/* Location: ./system/library/mui.php */