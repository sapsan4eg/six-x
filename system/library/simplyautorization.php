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
 * Autorization class
 *
 * @package		six-x
 * @subpackage	autorization
 * @category	Autorization
 * @author		Yuri Nasyrov <sapsan4eg@ya.ru>
 * @link		http://six-x.org/guide/autorization/
 */
class SimplyAutorization extends Object {

    /**
     * Autorization data
     *
     * @var array
     */
	public $Identity = array(
		'IsAuthenticated' => NULL, 
		'Name' => NULL, 
		'Email' => NULL, 
		'id' => NULL
		);

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param	object  $storage
     * @param   array   $route
     */
	public function __construct($storage, $route)
	{
		$this->_storage = $storage;
		$this->_checkAccess($route);
	}

    // --------------------------------------------------------------------

    /**
     * Checking access to part of system
     *
     * @access  protected
     * @param	array	$route
     * @return	void
     */
	protected function _checkAccess($route)
	{
		$havePermission = TRUE;
		$user_id = 0;

		if(isset($this->session->data['user']) && isset($this->session->data['user_id']))
		{
            // Select user in DB
			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "users` 
								WHERE `user_id` = '" . (int)$this->session->data['user_id'] . "' 
								AND LOWER(`logon_name`) = '" . strtolower($this->db->escape(\Six_x\Protecter::injection_clear($this->session->data['user']))) . "'
								AND `status` = '1'");

			if($query->count == 1)
			{
                $authenticated = TRUE;

				if(defined('AUTORIZATION_MULTILOGON'))
				{
                    // If user can logon only from 1 device
					if(AUTORIZATION_MULTILOGON == 'false')
					{
						if(session_id() == $query->first['session_id'])
						{
							$this->db->query("UPDATE `" . DB_PREFIX . "users` 
									SET `session_time`='" . date('Y-m-d H:i:s') . "' 
									WHERE `user_id`=" . $query->first['user_id'], TRUE);
						}
						else
						{
							$this->session->data['message'] = array('warning',_('warning'), _('error_another_logged'));
							$this->Logout(FALSE);
                            $authenticated = FALSE;
						}
					}
				}

                if($authenticated)
                {
                    $this->Identity['IsAuthenticated'] = TRUE;
                    $this->Identity['Name'] = $query->first['logon_name'];
                    $this->Identity['id'] = $query->first['user_id'];
                    $user_id = $query->first['user_id'];
                }
			}
			else
			{
				$this->Logout();
			}
		}
        // Check have to any permission on controller
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "permissions` 
								WHERE `controller_name` = '" . $route['controller'] . "'");

		if($query->count == 1)
		{
			$havePermission = $this->_havePermission(unserialize($query->first['permission']), $route, $user_id);
		}

		if($havePermission == FALSE)
		{
			$view = new View(array('router' => $this->router));
			$this->session->data['message'] = array('warning', _('warning'), _('error_not_have_perm'));
			$came = '{controllerFrom=' . $route['controller'] . '&actionFrom=' . $route['action'];
			if(count($route['arguments']) > 0)
			{
				foreach($route['arguments'] as $key => $value)
				{
					$came .= '&' . $key . 'From=' . $value;	
				}
			}
			$came .= '}';

			$view->RedirectToAction('Login', 'Account', array('Came_From' => urlencode($came)));
		}
	}

    // --------------------------------------------------------------------

    /**
     * Try sign in system
     *
     * @access  public
     * @param	string	$username
     * @param   string  $password
     * @return	bool
     */
	public function Login($username = '', $password = '')
	{
		Log::write(json_encode(array('action'=> 'try enter', 'user' => $username, 'password' => Six_x\Protecter::escape($password))));
		
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "users` 
									WHERE LOWER(`logon_name`) = '" . strtolower($this->db->escape(\Six_x\Protecter::injection_clear($username))) . "'
									AND  `password` = '" . md5($password) . "'
									AND `status` = '1'");

		if($query->count == 1)
		{
			if(defined('AUTORIZATION_MULTILOGON'))
			{
                // If user can logon only from 1 device
				if(AUTORIZATION_MULTILOGON == 'false')
				{
					if(strlen($query->first['session_id']) > 0)
					{
						if(session_id() != $query->first['session_id'])
						{
							$diff = strtotime(date("Y-m-d H:i:s")) - strtotime($query->first['session_time']);
							if($diff < 1200)
							{
								$this->session->data['message'] = array('warning',_('warning'), _('error_another_logged'));
								return FALSE;
							}
						}
					}

					$this->db->query("UPDATE `" . DB_PREFIX . "users` 
										SET `session_id`='" . session_id() . "' 
										WHERE `user_id`=" . $query->first['user_id'], TRUE);
				}
			}
			$this->session->data['user'] 	= $query->first['logon_name'];
			$this->session->data['user_id'] = $query->first['user_id'];

			Log::write(json_encode(array('action'=> 'success enter', 'user' => $username)));
			return TRUE;
		}
		return FALSE;
	}

    // --------------------------------------------------------------------

    /**
     * Log out from system
     *
     * @access  public
     * @param	bool	$change
     * @return	void
     */
	public function Logout($change = TRUE)
	{
		Log::write(json_encode(array('action'=> 'logout', 'user' => isset($this->session->data['user']) ? $this->session->data['user'] : 'not understand')));
		if(defined('AUTORIZATION_MULTILOGON') & isset($this->session->data['user_id']) & isset($this->session->data['user']))
		{
			if(AUTORIZATION_MULTILOGON == 'false' & $change == TRUE)
			{
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "users` 
											WHERE `user_id` = '" . (int)$this->session->data['user_id'] . "' 
											AND LOWER(`logon_name`) = '" . strtolower($this->db->escape($this->session->data['user'])) . "' 
											AND `status` = '1'");
				if($query->count == 1)
				{

					$this->db->query("UPDATE `" . DB_PREFIX . "users` 
										SET `session_id`='', session_time='0000-00-00 00:00:00'  
										WHERE `user_id`=" . $query->first['user_id'], TRUE);

				}
			}
		}

		if(isset($this->session->data['user']))
		{
			unset($this->session->data['user']);
		}

		if(isset($this->session->data['user_id']))
		{
			unset($this->session->data['user_id']);
		}
	}

    // --------------------------------------------------------------------

    /**
     * Check have user permission
     *
     * @access  protected
     * @param	array	$permissionArray
     * @param   array   $route
     * @param   int     $user_id
     * @return	bool
     */
	protected function _havePermission($permissionArray, $route, $user_id)
	{
		$permission = FALSE;
		$checkPermissionOnController = TRUE;

		if(isset($permissionArray['actions']))
		{
			if(isset($permissionArray['actions'][$route['action']]))
			{
				$permission = $this->_comparisonPermissions ($permissionArray['actions'][$route['action']], $user_id);
				$checkPermissionOnController = FALSE;
			}

		}

		if(isset($permissionArray['controller']))
		{
			if($checkPermissionOnController == TRUE)
			{
				$permission = $this->_comparisonPermissions ($permissionArray['controller'], $user_id);
			}
			else
			{
				$permission = TRUE;
			}
		}
		else if($checkPermissionOnController == TRUE)
		{
			$permission = TRUE;

		}

		return $permission;
	}

    // --------------------------------------------------------------------

    /**
     * Check comparison user permission
     *
     * @access  protected
     * @param	array	$permissions
     * @param   int     $user_id
     * @return	bool
     */
    protected function _comparisonPermissions ($permissions, $user_id)
	{
		$permission = TRUE;

		if(is_array($permissions))
		{
			$notneedRoles = TRUE;
			if(isset($permissions['Users']))
			{
				if(is_array($permissions['Users']))
				{
					$notneedRoles = array_search($user_id, $permissions['Users']);
				}
				elseif($permissions == 'Autorize' & $user_id == 0)
				{
					$permission = FALSE;

				}
			}
			else
			{
				$notneedRoles = FALSE;
			}

			if($notneedRoles == FALSE)
			{
				$permission = FALSE;

				if(isset($permissions['Roles']))
				{
					if(is_array($permissions['Roles']))
					{
						if($user_id > 0)
						{
							$querystring = "SELECT COUNT(t3.user_id) FROM `" . DB_PREFIX . "user_roles` t1, `" . DB_PREFIX . "user_roles` t2, `" . DB_PREFIX . "users_in_roles` t3 WHERE (";
							foreach($permissions['Roles'] as $value)
							{
								$querystring .= "t1.role_name = '" . $value . "' OR ";

							}

							$querystring = substr($querystring,0, strlen($querystring) -3);

							$querystring .= ") AND t2.role_to <= t1.role_to AND t2.role_from >= t1.role_from AND t3.user_id = " . $user_id ." AND t3.role_id = t2.role_id";

							$query = $this->db->query($querystring);

							if($query->first['COUNT(t3.user_id)'] > 0)
							{
								$permission = TRUE;
							}
						}
					}
					elseif($permissions == 'Autorize' & $user_id == 0)
					{
						$permission = FALSE;
					}
				}
			}
		}
		elseif($permissions == 'Autorize' & $user_id == 0)
		{
			$permission = FALSE;
		}

		return $permission;
	}
}

/* End of file simplyAutorization.php */
/* Location: ./system/library/simplyAutorization.php */