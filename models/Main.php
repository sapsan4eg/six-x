<?php
class Main_model extends Object
{
	 public function get_main_values()
	 {
	 	$old_data = $this->view->get();
		$old_data['title']				= 'PHP MVC framework!';
		$old_data['header']				= 'new MVC framework for PHP';
		$old_data['name_controller']	= $this->mui->get('text_name_controller');
		$old_data['name_action']		= $this->mui->get('text_name_action');
		$old_data['core_version']		= $this->mui->get('text_core_version');
		$this->view->set($old_data);
		$this->_partial_language();
		$this->_partial_login();	 }
	 public function home_links()
	 {
	 	$data['Redirected']	= array('link' => $this->router->Link('Test'), 'title' => '');
		$data['File']		= array('link' => $this->router->Link('News'), 'title' => ''); 
		$data['Json']		= array('link' => $this->router->Link('Json', 'Test', array('left' => 'good')),'title' => ''); 
		$data['Part']		= array('link' => $this->router->Link('Partial', 'Home'),'title' => '');
		$data['Aut']		= array('link' => $this->router->Link('TestAutorize', 'Test', array('id' => '213', 'some' => 'feel', 'tort'=>'232')), 'title' => '');
		$data['Log']		= array('link' => $this->router->Link('ErrorLog', 'Error'),'title' => '');
		$data['mainlink']	= array('link' => $this->router->Link('Index', 'Home'), 'title' => $this->mui->get('text_home'));
		return $data;
	 }
	 protected function _partial_login()
	 {		
		$old_data = $this->view->get();
		$data['Identity']				= $this->autorization->Identity;
		$data['links']['login']			= array('link' => $this->router->Link('Login', 'Account'), 'title' => $this->mui->get('button_login'));
		$data['links']['profile']		= array('link' => $this->router->Link('MyProfile', 'Account'), 'title' => $this->mui->get('button_profile'));
		$data['links']['logout'] 		= array('link' => $this->router->Link('Logout', 'Account'), 'title' => $this->mui->get('button_logout'));
		$data['links']['registration']	= array('link' => '', 'title' => $this->mui->get('button_registration'));
		$this->view->set($data);
		$old_data['partialLogin']		= $this->view->PartialViewResult('LoginPartial', DIR_SHARED);
		$this->view->set($old_data);
	 }
	 protected function _partial_language()
	 {
	 	$old_data = $this->view->get();
	 	$data['list_languges']			= $this->mui->get_list_languges();
		$data['link_change_languge']	= $this->router->Link('ChangeLanguage', 'Home');
		$data['RequestedUrl']			= isset($old_data['RequestedUrl']) ? $old_data['RequestedUrl'] : "";
		$this->view->set($data);
		$old_data['languges_partial']	= $this->view->PartialViewResult('languagePartial', DIR_SHARED);
		$this->view->set($old_data);
	 }}

/* End of file Domain.php */
/* Location: ./models/Domain.php */