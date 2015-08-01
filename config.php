<?php
/*
|--------------------------------------------------------------------------
| Http path to server
|--------------------------------------------------------------------------
|
| These modes are used when working with http
|
*/
define('HTTP_SERVER', 'http://six/');
define('HTTP_IMAGE', 'http://six/image/');

/*
|--------------------------------------------------------------------------
| dir on server
|--------------------------------------------------------------------------
|
| These modes are used when working with dirictory
|
*/
define('DIR_SYSTEM', '../system/');
define('DIR_DATABASE', DIR_SYSTEM . 'database/');
define('DIR_ENGINE', DIR_SYSTEM . 'engine/');
define('DIR_LIBRARY', DIR_SYSTEM . 'library/');
define('DIR_HELPER', DIR_SYSTEM . 'helper/');
define('DIR_CONTENT', 'content/');
define('DIR_CONTROLLERS', '../controllers/');
define('DIR_MODELS', '../models/');
define('DIR_SCRIPTS', 'scripts/');
define('DIR_VIEWS', '../views/');
define('DIR_SHARED', 'Shared/');
define('DIR_START', '../app_start/');
define('DIR_IMAGE', 'image/');
define('DIR_LANGUAGE', '../lang/');

/*
|--------------------------------------------------------------------------
| database connection parametrs
|--------------------------------------------------------------------------
|
| These modes are used when working with database
|
*/
define('DB_DRIVER', 'Mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'mvc');
define('DB_PREFIX', '');

/*
|--------------------------------------------------------------------------
| extends of file
|--------------------------------------------------------------------------
|
| These modes are used when working files
|
*/
define('FILE_LAYOUT', 'layout');
define('FILE_VIEW', 'tpl');
define('AUTORIZATION', 'simply');

/*
|--------------------------------------------------------------------------
| Param to autorization
|--------------------------------------------------------------------------
|
| Use this parametr when you need to constrain multilogon
|
*/
//define('AUTORIZATION_MULTILOGON', 'false');

/* End of file config.php */
/* Location: ./config.php */