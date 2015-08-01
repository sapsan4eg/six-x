<?php
class Main_model extends Object {
	 public function get_main_values()
	 {
		$this->_partial_language();
		$this->_partial_login();
	 }
	 public function home_links()
	 {
	 	$data['Redirected']	= $this->router->Link('Test');
		$data['File']		= $this->router->Link('News');
		$data['Json']		= $this->router->Link('Json', 'Test', array('left' => 'good'));
		$data['Part']		= $this->router->Link('Partial', 'Home');
		$data['Aut']		= $this->router->Link('TestAutorize', 'Test', array('id' => '213', 'some' => 'feel', 'tort' => '232'));
		$data['Log']		= $this->router->Link('ErrorLog', 'Error');
		$data['mainlink']	= $this->router->Link('Index', 'Home');
		return $data;
	 }
	 protected function _partial_login()
	 {		
		$old_data = $this->view->get();
		$data['Identity']				= $this->autorization->Identity;
		$data['links']['login']			= $this->router->Link('Login', 'Account');
		$data['links']['profile']		= $this->router->Link('MyProfile', 'Account');
		$data['links']['logout'] 		= $this->router->Link('Logout', 'Account');
		$data['links']['registration']	= '';
		$this->view->set($data);
		$old_data['partialLogin']		= $this->view->PartialViewResult('LoginPartial', DIR_SHARED);
		$this->view->set($old_data);
	 }
	 protected function _partial_language()
	 {
	 	$old_data = $this->view->get();
	 	$data['list_languges']			= \Six_x\Mui::get_list_languges();
		$data['link_change_languge']	= $this->router->Link('ChangeLanguage', 'Home');
		$data['RequestedUrl']			= isset($old_data['RequestedUrl']) ? $old_data['RequestedUrl'] : "";
		$this->view->set($data);
		$old_data['languges_partial']	= $this->view->PartialViewResult('languagePartial', DIR_SHARED);
		$this->view->set($old_data);
	 }
}

/* End of file Domain.php */
/* Location: ./models/Domain.php */