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
 * Router Class
 *
 * is responsible for routing
 * 
 * @package		six-x
 * @subpackage	libraries
 * @category	Libraries
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/
 */
class Router
{
	public $request_url = '';
	private $_url = array();
	private $_routes = array();
	private $_direction = 'forward';
	public $route = array();
	private $_num_route = 0;
	private $_db;	
	
	/**
	 * Constructor
	 * 
	 * @param	array
	 * @param	object
	 */
	public function __construct($get, $db)
	{
		// create a structure route
		$this->route = array(
						'controller' => '', 
						'action' => '', 
						'default_controller' => '', 
						'default_action' => '', 
						'error_controller' => '', 
						'arguments' => array()
					);
		
		// convey identifier database object to local var			
		$this->_db = $db;
		
		// load route map
		Loader::load('RouteMap', DIR_START);
		
		// create route map
		RouteMap::create();
		
		// take routes array
		$this->_routes = RouteMap::$routes;	
		
		// get requested url
		$this->request_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; 
		
		// parsing url
		$this->_url = parse_url($this->request_url);
		
		$controller = null;
		$action = null;
		
		// take num of last route
		$this->_num_route = count($this->_routes) - 1;	
		
		// if .htaccess work	
		if(isset($get['_route_']))
		{
			// get requested url without index.php
			$url = strlen($get['_route_']) == strrpos($get['_route_'], '/') + 1 ? substr($get['_route_'], 0, strlen($get['_route_']) -1) : str_replace('/index.php', '', $get['_route_']);				
			
			// convert to array
			$route = explode('/', $url);
			
			// count indexes
			$count = count($route);

			// check what route will fit
			$this->_check_num_route($route, $count);
			
			// get controller, action, and arguments
			$array = $this->_from_route_values($route);
			
			// fill values
			$controller	= $array['controller'];
			$action		= $array['action'];
			
			// check count indexes greater than count indexes in route
			if($count > $array['count'])
			{
				$controller	= $this->_routes[$this->_num_route]['error_controller'];
				$action		= $this->_routes[$this->_num_route]['action'];
			} 
			
			// using a reverse address
			$this->_direction = 'reverse';
		}
		else 
		{
			if(defined('DIRECTION_LINKS') AND strtoupper(DIRECTION_LINKS) == 'REVERS')
			{
				$this->_direction = 'reverse';
			}
			// check transmitted by get names controller and action
			isset($get['controller']) ? $controller = $get['controller'] : FALSE;
			isset($get['action']) ? $action = $get['action'] : FALSE;
		}

		// if associative array containing arguments get
		if(isset($this->_url['query']))
		{
			// explode to array requested url
			$array = explode('&', htmlspecialchars_decode($this->_url['query']));

			// containig gets arguments
			$arguments = array();

			// loop through get arguments
			foreach($array As $value)
			{
				if(strpos($value, '=') > 0)
				{
					$name = substr($value, 0, strpos($value, '='));
					$val = substr($value, strpos($value, '=') + 1);

					if($name != 'controller' & $name != 'action')
					{
						$arguments[$name] = $val;	 
					}
				}
			}

			// contains arguments in route arguments array
			$this->route['arguments'] = array_merge($this->route['arguments'], $arguments);
		}

		// create routes
		$this->_create_default_route($this->_routes[$this->_num_route]);	
		$this->_create_route($controller, $action);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * check what route will fit
	 *
	 * @access	protected
	 * @param	array
	 * @param	int
	 */
	protected function _check_num_route($route, $count)
	{
		// num default route
		$this->_num_route = count($this->_routes) - 1;

		// loop through routes list
		foreach ($this->_routes As $key => $value) 
		{
			// explode url path in route
			$map = explode('/', strlen($value['url']) == strrpos($value['url'], '/') + 1 ? substr($value['url'], 0, strlen($value['url']) -1) : $value['url']);

			// if the number of paths coincide
			if($count == count($map))
			{
				$thisroute = true;

				// loop through for check route
				foreach($map As $key_m => $value_m)
				{
					// check on the relative parameter
					if(strpos($value_m, '{') !== false)
					{
						// if isset relative parametr
						if(strpos($value_m, '{') > 0 OR strpos($value_m, '}') < strlen($value_m) -1)
						{								
							$start = strpos($value_m, '{');
							$end = strlen($value_m) - strpos($value_m, '}') -1;
							
							if(substr($route[$key_m], 0, $start) != substr($value_m, 0, $start) || substr($route[$key_m], strlen($route[$key_m]) - $end) != substr($value_m, $value_m - $end))
							{
								$thisroute = FALSE;
							}
						}
					}
					elseif($route[$key_m] != $value_m)
					{
						$thisroute = FALSE;
					}
				}

				if($thisroute)
				{
					$this->_num_route = $key;
					break;
				}
			}			
		}
	}

	// --------------------------------------------------------------------

	/**
	 * return controller, action, and arguments
	 *
	 * @access	protected
	 * @param	array
	 * @return	array
	 */
	protected function _from_route_values ($route)
	{
		$array = array('controller' => NULL, 'action' => NULL, 'count' => 0);

		// explode url path in route
		$map = explode('/', strlen($this->_routes[$this->_num_route]['url']) == strrpos($this->_routes[$this->_num_route]['url'], '/') + 1 ? substr($this->_routes[$this->_num_route]['url'], 0, strlen($this->_routes[$this->_num_route]['url']) -1) : $this->_routes[$this->_num_route]['url']);

		$array['count'] = count($map);

		// loop through to get parametrs
		foreach($map As $key => $value)
		{
			if(isset($route[$key]) && strpos($value, '{') !== FALSE && strpos($value, '}') !== FALSE)
			{
				$name = $this->_clear_name($value);

				if($name == 'controller' OR $name == 'action')
				{
					$array[$name] = $this->_clear_route($value, $route[$key]);
				}
				else
				{
					$this->route['arguments'][$name] = $this->_clear_route($value, $route[$key]);
				}
			}
		}

		// check personal route
		if(strpos($this->_routes[$this->_num_route]['url'], '{personal_route}') !== FALSE)
		{
			$try_again = FALSE;
			
			// find personal route
			$query = $this->_db->query("SELECT * FROM `" . DB_PREFIX . "personal_routes` 
											WHERE `keyword` = '" . $this->_db->escape($this->route['arguments']['personal_route']) ."'");

			if($query->count > 0)
			{
				$tempArray = unserialize($query->first['route']);

				if($array['controller'] != NULL)
				{
					$array['controller'] != $tempArray['controller'] ? $tryAgain = TRUE : FALSE;
				} 
				else
				{
					$array['controller'] = $tempArray['controller'];
				}
				
				if($array['action'] != NULL)
				{
					$array['action'] != $tempArray['action'] ? $try_again = TRUE : FALSE;
				}
				else
				{
					$array['action'] = $tempArray['action'];
				}

				foreach ($this->route['arguments'] As $key => $value)
				{
					if(isset($tempArray['arguments'][$key]))
					{
						$tempArray['arguments'][$key] != $value ? $try_again = TRUE : FALSE;
					}
				}
			}
			else
			{
				$try_again = TRUE;
			}

			// need check another route
			if($try_again)
			{
				array_splice($this->_routes, $this->_num_route, 1);
				
				$this->_check_num_route($route, count($route));
				
				$array = $this->_from_route_values($route);
			}
		}

		return $array;
	}

	// --------------------------------------------------------------------

	/**
	 * get name from path route
	 *
	 * @access	protected
	 * @param	string
	 * @return	string
	 */
	protected function _clear_name($value = '')
	{
		return substr($value, strpos($value, '{') + 1, strlen($value) - (strlen($value) - strpos($value, '}')) - (strpos($value, '{') + 1));
	}
	
	// --------------------------------------------------------------------

	/**
	 * get value from path url
	 *
	 * @access	protected
	 * @param	string
	 * @param	string
	 * @return	string
	 */
	protected function _clear_route($reg, $value)
	{
		$value = substr($value, strpos($reg, '{'));
		$end = strlen($reg) - strpos($reg, '}') - 1;
		
		return substr($value, 0, strlen($value) - $end);;
	}
	
	// --------------------------------------------------------------------

	/**
	 * create deafault route
	 *
	 * @access	protected
	 * @param	array
	 */
	protected function _create_default_route($route)
	{
		$this->route['default_controller'] = $route['controller'];
		$this->route['default_action'] = $route['action'];
		$this->route['error_controller'] = $route['error_controller'];
	}

	// --------------------------------------------------------------------

	/**
	 * create route
	 *
	 * @access	protected
	 * @param	string
	 * @param	string
	 */
	protected function _create_route($controller = NULL, $action = NULL)
	{
		// default values
		$this->route['controller']	= $this->route['default_controller'];
		$this->route['action']		= $this->route['default_action'];

		// get name controller
		if($controller != NULL)
		{
			if (file_exists(DIR_CONTROLLERS . $controller . 'Controller.php'))
			{
				$this->route['controller'] = $controller;	
			}
			else
			{
				$this->route['controller'] = $this->route['error_controller'];
			}
		}

		// get name action
		if($action != NULL)
		{
			$this->route['action'] = $action;
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * create url
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	string
	 */
	public function Link($action = '', $controller = '', $arguments = array())
	{
		// if not set controller
		if(strlen($controller) == 0)
		{
			$controller = isset($this->route['controller']) ? $this->route['controller'] : $this->route['default_controller'];
		}
		
		// if not set action
		strlen($action) == 0 ? $action = $this->route['default_action'] : FALSE;

		// create url string
		$url = $this->_url['scheme'] . '://' . $this->_url['host'] . (isset($this->_url['port']) ? ':' . $this->_url['port'] : '');

		$path = substr(HTTP_SERVER, strpos(HTTP_SERVER, '//') + 2);

		strpos($path, '/') > 0 ? $path = substr($path, strpos($path, '/') + 1) : $path = '';
		
		$url .= '/' . $path;
		$url = (strlen($url) - 1) == strrpos($url, '/') ? $url : $url . '/';

		// if .htaccess work
		if($this->_direction == 'reverse')
		{			
			foreach($this->_routes As $values)
			{
				$thisroute = $this->_it_is_my_route($values['url'], $arguments, $url, $controller, $action);

				if($thisroute['isnt'])
				{
					$url = $thisroute['url'];
					$arguments = $thisroute['arguments'];
					break;
				}
			}
		}
		else
		{
			$url .= '?controller=' . $controller . '&action=' . $action;
		}
		
		// check have get arguments
		$url = strpos($url, '?') > 0 ? $url . '&' : $url . '?';

		// loop through argeuments
		foreach($arguments As $key => $value)
		{
			$url .= $key . '=' . $value . '&';	
		}
		
		$url = substr($url, 0, strlen($url) - 1);
		
		return $url;		
	}
	
	// --------------------------------------------------------------------

	/**
	 * create url
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	string
	 */
	public static function Source($name = '')
	{
		// create url string		
		$url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'];

		$path = substr(HTTP_SERVER, strpos(HTTP_SERVER, '//') + 2);

		strpos($path, '/') > 0 ? $path = substr($path, strpos($path, '/') + 1) : $path = '';
		
		$url .= '/' . $path;
		$url = ((strlen($url) - 1) == strrpos($url, '/') ? $url : $url . '/') . $name;
		
		return $url;		
	}
	
	// --------------------------------------------------------------------

	/**
	 * check have this route
	 *
	 * @access	protected
	 * @param	string
	 * @param	array
	 * @param	string
	 * @param	string
	 * @param	string
	 * @return	array
	 */
	protected function _it_is_my_route($myvalue, $arguments, $url, $controller, $action)
	{
		$thisroute = FALSE;

		// if isset personal route
		if(strpos($myvalue, '{personal_route}') !== FALSE)
		{
			$thisroute = TRUE;
			
			$map = explode('/', $myvalue);
			
			foreach($map As $value)
			{
				if(strpos($value, '{') !== TRUE)
				{
					if($this->_clear_name($value) != 'controller' && $this->_clear_name($value) != 'action' && $this->_clear_name($value) != 'personal_route')
					{
						if( ! isset($arguments[$this->_clear_name($value)]))
						{
							$thisroute = FALSE;
						}
					}
				}
			}

			if($thisroute)
			{
				$temp = serialize(array('controller' => $controller, 'action' => $action));

				$temp = "'" . $temp . "' OR ( route LIKE '%" . substr($temp , 5, strlen($temp) - 6) . "%'";

				if(count($arguments) > 0)
				{
					$temp .= ' AND (';

					foreach($arguments As $key => $value)
					{
						$t = substr(serialize(array($key => $value)), 5);
						$temp .= "route LIKE '%" . substr($t, 0 , strlen($t) - 1) . "%' OR ";
					}

					$temp = substr($temp, 0, strlen($temp) - 3);
					$temp .= '))';

				}
				else
				{
					$temp = substr($temp, 0, strpos($temp, 'OR'));
				}
				
				$query = $this->_db->query("SELECT * FROM " . DB_PREFIX . "personal_routes WHERE route = " . $temp);
				
				if($query->count > 0)
				{
					$myarg = array('key' => 0, 'count' => 0);

					foreach ($query->list As $key => $value)
					{
						$r = unserialize($value['route']);					
						if(isset($r['arguments']))
						{
							$countarguments = count($r['arguments']);
							if($countarguments <= count($arguments))
							{							
								foreach ($arguments As $keyarg => $valuearg)
								{
									if(isset($r['arguments'][$keyarg]))
									{
										$countarguments = $r['arguments'][$keyarg] == $valuearg ? ($countarguments - 1) : $countarguments;
									}
								}
								if($countarguments <= 0)
								{
									$myarg = $myarg['count'] <= count($r['arguments']) ? array('key' => $key, 'count' => count($r['arguments'])) : $myarg;
								}
							}
						}
						else
						{
							$myarg = $myarg['count'] == 0 ? array('key' => $key, 'count' => 0) : $myarg;
						}
					}

					$url .= str_replace('{personal_route}', $query->list[$myarg['key']]['keyword'], str_replace('{action}', $action, str_replace('{controller}', $controller, $myvalue)));

					$needDelArray = array();

					foreach($arguments As $key => $value)
					{
						if(strpos($url, '{' . $key . '}') !== false)
						{
							$url = str_replace('{' . $key . '}', $value, $url);
							$needDelArray[] = $key;
						}
					}

					$r = unserialize($query->list[$myarg['key']]['route']);

					if(isset($r['arguments']))
					{
						foreach ($r['arguments'] As $key => $value)
						{
							if( ! isset($needDelArray[$key]))
							{
								$needDelArray[] = $key;
							}
						}
					}

					foreach ($needDelArray As $value)
					{
						unset($arguments[$value]);
					}
				}
				else
				{
					$thisroute = FALSE;
				}
			}
		} 
		elseif(count(explode('}', $myvalue)) - 3 <= count($arguments))
		{
			$thisroute = TRUE;

			$map = explode('/', $myvalue);

			foreach($map As $value)
			{
				if(strpos($value, '{') !== FALSE)
				{
					if($this->_clear_name($value) != 'controller' & $this->_clear_name($value) != 'action')
					{
						if( ! isset($arguments[$this->_clear_name($value)]))
						{
							$thisroute = FALSE;
						}
					}
				}
			}

			if($thisroute)
			{
				$url .= str_replace('{action}', $action, str_replace('{controller}', $controller, $myvalue));

				$needDelArray = array();

				foreach($arguments As $key => $value)
				{
					if(strpos($url, '{' . $key . '}') !== FALSE)
					{
						$url = str_replace('{' . $key . '}', $value, $url);
						$needDelArray[] = $key;
					}
				}
				
				foreach ($needDelArray As $value)
				{
					unset($arguments[$value]);
				}
			}
		}

		return array('url' => $url, 'arguments' => $arguments, 'isnt' => $thisroute);
	}
}

/* End of file router.php */
/* Location: ./system/library/router.php */