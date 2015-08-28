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
    public function Access()
    {
        $this->join->model('Account/Access');
        $this->AccountAccessModel->GetMain();
        $this->view->controllers = $this->AccountAccessModel->ListControllers();
        $this->view->groups = $this->AccountAccessModel->ListGroups();
        $this->view->users = $this->AccountAccessModel->ListUSers();
        $this->view->getPermissions = $this->router->Link('GetPermissions');
        $this->view->to = 0;
        $this->view->pervios = -1;
        return $this->view->ViewResult();
    }
    public function GetPermissions()
    {
        if( ! empty($this->request->post['controller_name']))
        {
            $array = [];
            $this->join->model('Account/Access');
            $array = $this->AccountAccessModel->GetPermissions($this->request->post['controller_name']);
            return $this->view->JsonResult($array);
        }
        return $this->view->RedirectToAction('not_found', 'Error');
    }
}

/* End of file AccountController.php */
/* Location: ./controllers/AccountController.php */