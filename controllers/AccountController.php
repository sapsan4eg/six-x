<?php
class AccountController extends Controller {
	public function Index()
	{
		$this->view->RedirectToAction('Login', 'Account');
	}
	public function Login()
	{
		$this->join->model('Account/Login');
		$this->AccountLoginModel->get_main_valls();		
		return $this->view->ViewResult('LoginForm');
	}
	public function Logout()
	{
		$this->autorization->Logout();
		$this->view->RedirectToAction($this->view->router->route['default_action'], $this->view->router->route['default_controller']);
	}
	public function MyProfile()
	{
		$this->join->model('Account/UserProfile');
		$this->view->Identity = $this->AccountUserProfileModel->get_user_info();
		$this->view->links = array('mainlink' => $this->router->Link('Index', 'Home'));
		return $this->view->ViewResult('UserProfile');
	}
}

/* End of file AccountController.php */
/* Location: ./controllers/AccountController.php */