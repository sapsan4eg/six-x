<?php
class Account_Login_model extends Object {
	public function get_main_valls()
	{
		$this->join->model('Main');
		$this->MainModel->get_main_values();
		$array = array('title' => _('login.heading_title'), 'text_local_account' => _('text_local_account'),
		'email' => _('entry_email'), 'password' => _('entry_password'),
		'login' => _('text_login'), 'register' => _('text_register')
		);
		$fileLocalizationJqueryVall = DIR_SCRIPTS . 'localization/' . 'messages_' . _('code') . '.min.js';
		if (file_exists($fileLocalizationJqueryVall)) $array['localization_validate'] = '<script src="' . HTTP_SERVER . $fileLocalizationJqueryVall .'"></script>' . PHP_EOL;
		$this->view->set(array_merge($this->view->get(), $array));
		$this->view->links = array('mainlink' => $this->router->Link('Index', 'Home', array('hello' => 'hi', 'id' => '2')));
		if(isset($this->request->get['Came_From']))
		{
			$came = str_replace('amp;', '', str_replace('&amp;', '&', $this->request->get['Came_From']));
			$this->view->camefrom = urlencode($came);
		}
		if(isset($this->request->post['email']) & isset($this->request->post['password']))
		{
			$came = isset($came) ? $came : null;
			$this->_check_user($this->request->post['email'], $this->request->post['password'], $came);
		}
	}
	protected function _check_user($email, $password, $came = null)
	{
		if($this->autorization->Login($email, $password))
		{
		 	$controller = $this->view->router->route['default_controller'];
			$action = $this->view->router->route['default_action'];
			$arguments = array();
			if(isset($came))
			{
				$came = substr($came, 1, strlen($came) - 2);
				$array = explode('&', $came);
				foreach ($array as $value) 
				{
					$value = str_replace('From', '', $value);
					if(strpos($value, '=') > 0)
					{
						
						$name = substr($value, 0, strpos($value, '='));
						$val = substr($value, strpos($value, '=') + 1);
						if($name == 'controller') $controller = $val;
						else if($name == 'action') $action = $val;
						else if($name != 'personal_route') $arguments[$name] = $val;	
					}	
				}
			}
		    $this->view->RedirectToAction($action, $controller, $arguments);
		 }
		 else
		 {
		 	if(!isset($this->autorization->session->data['message']))
		 	{
				$this->view->message = array('warning', _('warning'), _('error_notfind_user'));
			}
			else
			{
				$this->view->message = $this->autorization->session->data['message'];
				unset($this->autorization->session->data['message']);
			}
		 }
	}
}

/* End of file Login.php */
/* Location: ./models/Account/Login.php */