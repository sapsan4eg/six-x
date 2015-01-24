<?php
class RouteMap
{
	public static $routes = array();
	public static function create()
	{
		self::$routes[] = array('name' => 'mui', 'controller' => 'Test', 'action' => 'Index', 'url' => '{my_mui_language}/{personal_route}.html', 'error_controller' => 'Error');
		self::$routes[] = array('name' => 'personal_min', 'controller' => 'Home', 'action' => 'Index', 'url' => '{personal_route}.html', 'error_controller' => 'Error');
		self::$routes[] = array('name' => 'personal', 'controller' => 'Home', 'action' => 'Index', 'url' => '{controller}/{action}/{personal_route}.html', 'error_controller' => 'Error');
		//self::$routes[] = array('name' => 'new', 'controller' => 'Test', 'action' => 'Index', 'url' => '{id}/er{controller}_hp/{action}/', 'error_controller' => 'Error');
		//self::$routes[] = array('name' => 'new', 'controller' => 'Test', 'action' => 'Index', 'url' => 'year/er{controller}_hp/{action}/', 'error_controller' => 'Error');
		self::$routes[] = array('name' => 'default', 'controller' => 'Home', 'action' => 'Index', 'url' => '{controller}/{action}/', 'error_controller' => 'Error');	
	}
}
?>